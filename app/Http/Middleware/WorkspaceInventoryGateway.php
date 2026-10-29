<?php

namespace App\Http\Middleware;

use App\Services\WorkspaceInventoryAccess;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class WorkspaceInventoryGateway
{
    private $access;

    public function __construct(WorkspaceInventoryAccess $access)
    {
        $this->access = $access;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$request->isSecure()) {
            $this->access->fail(400, 'inventory_https_required', 'La integración requiere HTTPS.');
        }

        $expected = config('workspace_inventory.token');
        if (!$this->access->validSecret($expected)) {
            $this->access->fail(503, 'inventory_not_configured', 'La integración de inventario no está configurada.');
        }

        $provided = $request->bearerToken();
        if (!is_string($provided) || !hash_equals($expected, $provided)) {
            $this->access->fail(401, 'inventory_gateway_denied', 'La conexión de inventario no está autorizada.');
        }

        try {
            $user = $this->access->resolveActor($request->header('X-Workspace-Actor'));
            $decision = $this->access->authorize($user);
            $capabilities = $this->access->capabilities($user);
            if (!$capabilities['catalog'] && !$capabilities['requests']) {
                $this->access->fail(403, 'inventory_role_denied', 'La cuenta no tiene un rol de inventario permitido.');
            }

            // No session is created and no role is granted by the integration.
            $request->attributes->set('workspace_inventory_user', $user);
            $request->attributes->set('workspace_inventory_capabilities', $capabilities);
            $request->attributes->set('workspace_inventory_access_version', $decision['version']);

            return $next($request);
        } catch (QueryException $exception) {
            // A missing legacy column/table is not an empty inventory or a debug trace.
            $this->access->fail(503, 'inventory_data_unavailable', 'No se pudo consultar el inventario.');
        }
    }
}
