<?php

namespace App\Http\Middleware;

use App\Services\WorkspaceInventoryAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkspaceInventoryWebAccess
{
    private $access;

    public function __construct(WorkspaceInventoryAccess $access)
    {
        $this->access = $access;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!config('workspace_inventory.enforce_web_access', false)) {
            return $next($request);
        }

        // These routes contain authentication UI only, never inventory records.
        // POST logout remains usable even if the callback is down or access was revoked.
        $path = trim($request->path(), '/');
        $authenticationPage = ($path === '' && in_array($request->method(), ['GET', 'HEAD'], true)) ||
            ($path === 'login' && in_array($request->method(), ['GET', 'HEAD', 'POST'], true)) ||
            ($path === 'logout' && $request->isMethod('POST'));
        $route = $request->route();
        $routeName = $route ? (string) $route->getName() : '';
        $passwordRoute = in_array($routeName, [
            'password.request', 'password.email', 'password.reset',
            'password.update', 'password.confirm',
        ], true);

        if ($authenticationPage || $passwordRoute) {
            return $next($request);
        }

        if (!$request->isSecure()) {
            $this->access->fail(400, 'inventory_https_required', 'El acceso al inventario requiere HTTPS.');
        }

        $user = Auth::guard('web')->user();
        if (!$user) {
            if (!$request->expectsJson() && !$request->is('livewire/*')) {
                return redirect()->guest(route('login'));
            }
            $this->access->fail(401, 'inventory_actor_denied', 'Inicie sesión para acceder al inventario.');
        }

        $this->access->authorize($user);
        $response = $next($request);
        // Protected pages and reports must not survive revocation in shared caches.
        $response->headers->set('Cache-Control', 'no-store, private, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Vary', 'Cookie');

        return $response;
    }
}
