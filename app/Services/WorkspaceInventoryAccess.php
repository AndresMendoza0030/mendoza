<?php

namespace App\Services;

use App\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Http;

class WorkspaceInventoryAccess
{
    public function resolveActor($email): User
    {
        $email = $this->normalizeEmail($email);
        if ($email === null) {
            $this->fail(403, 'inventory_actor_denied', 'La identidad de inventario no es válida.');
        }

        // Never provision accounts or infer roles from gateway-supplied data.
        $users = User::whereRaw('LOWER(email) = ?', [$email])->limit(2)->get();
        if ($users->count() !== 1) {
            $this->fail(403, 'inventory_actor_denied', 'La cuenta no tiene acceso al inventario.');
        }

        $user = $users->first();
        $this->assertActive($user);

        return $user;
    }

    public function assertActive(User $user): void
    {
        // Null/missing status must not become an active account by integer coercion.
        $attributes = $user->getAttributes();
        if (!array_key_exists('estatus', $attributes) || (string) $attributes['estatus'] !== '0') {
            $this->fail(403, 'inventory_actor_denied', 'La cuenta de inventario está inactiva.');
        }
    }

    public function capabilities(User $user): array
    {
        $names = $user->roles->where('guard_name', 'web')->pluck('name')
            ->merge($user->getAllPermissions()->where('guard_name', 'web')->pluck('name'));
        $operator = $names->contains('gerente') || $names->contains('bodega');
        $requester = $names->contains('solicitante');

        return [
            'catalog' => $operator,
            'stocks' => $operator,
            'movements' => $operator,
            'requests' => $operator || $requester,
            'write' => false,
        ];
    }

    public function authorize(User $user): array
    {
        $this->assertActive($user);
        $email = $this->normalizeEmail($user->email);
        if ($email === null) {
            $this->fail(403, 'inventory_actor_denied', 'La identidad de inventario no es válida.');
        }

        $config = $this->callbackConfiguration();
        $headers = [
            'Authorization' => 'Bearer ' . $config['permission_token'],
            'Accept' => 'application/json',
            'Cache-Control' => 'no-store',
        ];
        if ($config['access_client_id'] !== '') {
            $headers['CF-Access-Client-Id'] = $config['access_client_id'];
            $headers['CF-Access-Client-Secret'] = $config['access_client_secret'];
        }

        try {
            // One decision per request. No retries, redirects, cache or TLS bypass.
            $response = Http::withHeaders($headers)->withOptions([
                'timeout' => $config['timeout'],
                'connect_timeout' => $config['connect_timeout'],
                'allow_redirects' => false,
                'verify' => true,
            ])->post($config['access_url'], ['email' => $email]);
        } catch (\Throwable $exception) {
            // Do not log upstream bodies, request headers or exception credentials.
            $this->fail(503, 'inventory_permission_unavailable', 'No se pudo verificar el permiso de inventario.');
        }

        $contentType = strtolower((string) $response->header('Content-Type'));
        $body = $response->body();
        if ($response->status() !== 200 || strlen($body) > 16384 ||
            !preg_match('~^application/(?:[a-z0-9.+-]+\+)?json(?:;|$)~', $contentType)) {
            $this->fail(503, 'inventory_permission_unavailable', 'La verificación de acceso no está disponible.');
        }

        $decision = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decision) ||
            !array_key_exists('allowed', $decision) || !is_bool($decision['allowed']) ||
            !isset($decision['version']) || !is_int($decision['version']) || $decision['version'] < 0) {
            $this->fail(503, 'inventory_permission_unavailable', 'La respuesta de acceso no es válida.');
        }

        if ($decision['allowed'] !== true) {
            $this->fail(403, 'inventory_access_denied', 'El acceso a inventario no está autorizado.');
        }

        return ['allowed' => true, 'version' => $decision['version']];
    }

    protected function callbackConfiguration(): array
    {
        $config = config('workspace_inventory', []);
        $gateway = $config['token'] ?? null;
        $token = $config['permission_token'] ?? null;
        $url = $config['access_url'] ?? null;
        $parts = is_string($url) ? parse_url($url) : false;

        if (!$this->validSecret($gateway) || !$this->validSecret($token) ||
            hash_equals($gateway, $token) || !is_array($parts) ||
            filter_var($url, FILTER_VALIDATE_URL) === false ||
            ($parts['scheme'] ?? '') !== 'https' || empty($parts['host']) ||
            ($parts['path'] ?? '') !== '/api/integrations/inventory/access' ||
            isset($parts['user']) || isset($parts['pass']) ||
            isset($parts['query']) || isset($parts['fragment']) ||
            (isset($parts['port']) && $parts['port'] !== 443)) {
            $this->fail(503, 'inventory_not_configured', 'La integración de inventario no está configurada.');
        }

        $clientId = $config['access_client_id'] ?? '';
        $clientSecret = $config['access_client_secret'] ?? '';
        $clientId = $clientId === null ? '' : $clientId;
        $clientSecret = $clientSecret === null ? '' : $clientSecret;
        if (!is_string($clientId) || !is_string($clientSecret) ||
            (($clientId === '') !== ($clientSecret === '')) ||
            preg_match('/[\r\n]/', $clientId . $clientSecret)) {
            $this->fail(503, 'inventory_not_configured', 'Las credenciales del acceso privado están incompletas.');
        }

        return [
            'permission_token' => $token,
            'access_url' => $url,
            'access_client_id' => $clientId,
            'access_client_secret' => $clientSecret,
            'timeout' => max(1, min(10, (int) ($config['timeout'] ?? 5))),
            'connect_timeout' => max(1, min(3, (int) ($config['connect_timeout'] ?? 3))),
        ];
    }

    public function validSecret($secret): bool
    {
        return is_string($secret) && strlen($secret) >= 32 &&
            trim($secret) === $secret && !preg_match('/[\r\n]/', $secret);
    }

    protected function normalizeEmail($email): ?string
    {
        if (!is_string($email) || strlen($email) > 254) {
            return null;
        }

        $email = strtolower(trim($email));

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    public function response(array $payload, int $status = 200)
    {
        return response()->json($payload, $status, [
            'Cache-Control' => 'no-store, private, max-age=0',
            'Pragma' => 'no-cache',
            'Vary' => 'Authorization, X-Workspace-Actor, Cookie',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function fail(int $status, string $code, string $message, array $details = []): void
    {
        throw new HttpResponseException($this->response(
            array_merge(['error' => $message, 'code' => $code], $details),
            $status
        ));
    }
}
