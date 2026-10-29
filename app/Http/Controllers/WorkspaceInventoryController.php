<?php

namespace App\Http\Controllers;

use App\Services\WorkspaceInventoryAccess;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkspaceInventoryController extends Controller
{
    private $access;

    public function __construct(WorkspaceInventoryAccess $access)
    {
        $this->access = $access;
    }

    public function callAction($method, $parameters)
    {
        try {
            return parent::callAction($method, $parameters);
        } catch (QueryException $exception) {
            // Routing Pipeline renders action exceptions before outer middleware sees them.
            $this->access->fail(503, 'inventory_data_unavailable', 'No se pudo consultar el inventario.');
        }
    }

    public function bootstrap(Request $request)
    {
        $user = $request->attributes->get('workspace_inventory_user');
        $capabilities = $request->attributes->get('workspace_inventory_capabilities');
        $warehouses = DB::table('warehouses as w')->select('w.id', 'w.nombre');
        if (!$capabilities['stocks']) {
            // A requester may discover only warehouses attached to their own requests.
            $warehouses->whereExists(function ($query) use ($user) {
                $query->select(DB::raw(1))->from('reqhead as r')
                    ->whereColumn('r.bodega', 'w.id')->where('r.user_id', $user->id);
            });
        }

        return $this->access->response([
            'schema_version' => 1,
            'source' => 'mendoza',
            'user' => ['email' => strtolower(trim($user->email)), 'name' => (string) $user->name],
            'capabilities' => $capabilities,
            'warehouses' => $warehouses->orderBy('w.nombre')->orderBy('w.id')->get()->map(function ($row) {
                return ['id' => (int) $row->id, 'name' => (string) $row->nombre];
            })->all(),
        ]);
    }

    public function products(Request $request)
    {
        $this->requireCapability($request, 'catalog');
        $input = $this->filters($request);
        $query = DB::table('products as p')
            ->select('p.id', 'p.barra', 'p.nombre', 'p.presentacion', 'p.estatus')
            ->where('p.tipo', 'PRODUCTO');
        $this->search($query, $input['q'] ?? '', ['p.nombre', 'p.barra', 'p.presentacion']);
        $query->orderBy('p.nombre')->orderBy('p.id');

        return $this->paginate($query, $input, function ($row) {
            return [
                'id' => (int) $row->id,
                'code' => $row->barra === null ? null : (string) $row->barra,
                'name' => (string) $row->nombre,
                'presentation' => $row->presentacion === null ? null : (string) $row->presentacion,
                'active' => (string) $row->estatus === '0',
            ];
        });
    }

    public function stocks(Request $request)
    {
        $this->requireCapability($request, 'stocks');
        $input = $this->filters($request, true);
        $query = DB::table('productstocks as s')
            ->join('products as p', 'p.id', '=', 's.producto_id')
            ->join('warehouses as w', 'w.id', '=', 's.bodega')
            ->select('s.id', 's.producto_id', 'p.nombre as product_name', 'p.barra',
                's.bodega', 'w.nombre as warehouse_name', 's.existencia')
            ->where('p.tipo', 'PRODUCTO');
        $this->search($query, $input['q'] ?? '', ['p.nombre', 'p.barra']);
        if (isset($input['warehouse_id'])) {
            $query->where('s.bodega', $input['warehouse_id']);
        }
        $query->orderBy('p.nombre')->orderBy('s.bodega')->orderBy('s.id');

        return $this->paginate($query, $input, function ($row) {
            return [
                'product_id' => (int) $row->producto_id,
                'product_name' => (string) $row->product_name,
                'code' => $row->barra === null ? null : (string) $row->barra,
                'warehouse_id' => (int) $row->bodega,
                'warehouse_name' => (string) $row->warehouse_name,
                // This is the recorded balance, not a computed availability estimate.
                'quantity' => $this->quantity($row->existencia),
            ];
        });
    }

    public function movements(Request $request)
    {
        $this->requireCapability($request, 'movements');
        $input = $this->filters($request, true, true);
        $query = DB::table('movdetails as m')
            ->leftJoin('products as p', 'p.id', '=', 'm.producto_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'm.bodega')
            ->select('m.id', 'm.fecha', 'm.tipodoc', 'm.movimiento', 'm.producto_id',
                'm.producto', 'm.bodega', 'w.nombre as warehouse_name', 'm.cantidad', 'm.numerodoc');
        $this->search($query, $input['q'] ?? '', ['m.producto', 'p.nombre', 'p.barra', 'm.tipodoc']);
        if (isset($input['warehouse_id'])) {
            $query->where('m.bodega', $input['warehouse_id']);
        }
        if (isset($input['product_id'])) {
            $query->where('m.producto_id', $input['product_id']);
        }
        if (isset($input['from'])) {
            $query->where('m.fecha', '>=', $input['from']);
        }
        if (isset($input['to'])) {
            $query->where('m.fecha', '<=', $input['to']);
        }
        $query->orderByDesc('m.fecha')->orderByDesc('m.id');

        return $this->paginate($query, $input, function ($row) {
            return [
                'id' => (int) $row->id,
                'date' => (string) $row->fecha,
                'type' => (string) $row->tipodoc,
                'direction' => (string) $row->movimiento,
                'product_id' => (int) $row->producto_id,
                'product_name' => (string) $row->producto,
                'warehouse_id' => (int) $row->bodega,
                'warehouse_name' => (string) $row->warehouse_name,
                'quantity' => $this->quantity($row->cantidad),
                'document_id' => (int) $row->numerodoc,
            ];
        });
    }

    public function requests(Request $request)
    {
        $this->requireCapability($request, 'requests');
        $input = $this->filters($request, true);
        $user = $request->attributes->get('workspace_inventory_user');
        $capabilities = $request->attributes->get('workspace_inventory_capabilities');
        $query = DB::table('reqhead as r')
            ->leftJoin('warehouses as w', 'w.id', '=', 'r.bodega')
            ->leftJoin('users as u', 'u.id', '=', 'r.user_id')
            ->select('r.id', 'r.fecha', 'r.tipodoc', 'r.bodega', 'w.nombre as warehouse_name',
                'u.name as requester_name', 'r.estatus', 'r.despachado');
        if (!$capabilities['stocks']) {
            $query->where('r.user_id', $user->id);
        }
        $this->search($query, $input['q'] ?? '', ['r.tipodoc', 'w.nombre', 'u.name']);
        if (isset($input['warehouse_id'])) {
            $query->where('r.bodega', $input['warehouse_id']);
        }
        $query->orderByDesc('r.fecha')->orderByDesc('r.id');

        return $this->paginate($query, $input, function ($row) {
            $status = 'other';
            if (strtoupper(trim((string) $row->despachado)) === 'S') {
                $status = 'dispatched';
            } elseif ((string) $row->estatus === '1') {
                $status = 'authorized';
            } elseif ((string) $row->estatus === '0') {
                $status = 'pending';
            }

            return [
                'id' => (int) $row->id,
                'date' => (string) $row->fecha,
                'type' => (string) $row->tipodoc,
                'warehouse_id' => $row->bodega === null ? null : (int) $row->bodega,
                'warehouse_name' => $row->warehouse_name === null ? null : (string) $row->warehouse_name,
                'requester_name' => (string) $row->requester_name,
                'status' => $status,
            ];
        });
    }

    private function requireCapability(Request $request, string $capability): void
    {
        $capabilities = $request->attributes->get('workspace_inventory_capabilities', []);
        if (($capabilities[$capability] ?? false) !== true) {
            $this->access->fail(403, 'inventory_role_denied', 'Su rol no permite consultar esta sección.');
        }
    }

    private function filters(Request $request, bool $warehouse = false, bool $movements = false): array
    {
        $rules = [
            'q' => 'nullable|string|max:100',
            'page' => 'sometimes|integer|min:1|max:10000',
            'per_page' => 'sometimes|integer|min:1|max:50',
        ];
        if ($warehouse) {
            $rules['warehouse_id'] = 'sometimes|integer|min:1';
        }
        if ($movements) {
            $rules['product_id'] = 'sometimes|integer|min:1';
            $rules['from'] = 'sometimes|date_format:Y-m-d';
            $rules['to'] = 'sometimes|date_format:Y-m-d';
            if ($request->filled('from') && $request->filled('to')) {
                $rules['to'] .= '|after_or_equal:from';
            }
        }

        $validator = Validator::make($request->query(), $rules);
        if ($validator->fails()) {
            $this->access->fail(422, 'inventory_filter_invalid', 'Revise los filtros de consulta.', [
                'errors' => $validator->errors()->toArray(),
            ]);
        }

        return $validator->validated();
    }

    private function search($query, string $term, array $columns): void
    {
        $term = trim($term);
        if ($term === '') {
            return;
        }

        $query->where(function ($nested) use ($term, $columns) {
            foreach ($columns as $column) {
                $nested->orWhere($column, 'like', '%' . $term . '%');
            }
        });
    }

    private function paginate($query, array $input, callable $map)
    {
        $page = isset($input['page']) ? (int) $input['page'] : 1;
        $perPage = isset($input['per_page']) ? (int) $input['per_page'] : 20;
        $total = (int) (clone $query)->count();
        $items = $query->offset(($page - 1) * $perPage)->limit($perPage)->get()->map($map)->all();

        return $this->access->response([
            'items' => $items,
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ]);
    }

    private function quantity($value): string
    {
        if (!is_scalar($value) || !is_numeric((string) $value) || !is_finite((float) $value)) {
            $this->access->fail(503, 'inventory_data_unavailable', 'Existe un saldo o movimiento que no se puede interpretar.');
        }

        // Decimal strings avoid a lossy float conversion of the source balance.
        return (string) $value;
    }
}
