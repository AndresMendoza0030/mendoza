<?php

namespace Tests\Feature;

use App\Services\WorkspaceInventoryAccess;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WorkspaceInventoryTest extends TestCase
{
    private $operator;
    private $requester;
    private $other;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'app.key' => 'base64:' . base64_encode(str_repeat('k', 32)),
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.prefix' => '',
            'cache.default' => 'array',
            'session.driver' => 'array',
            'workspace_inventory.token' => str_repeat('g', 48),
            'workspace_inventory.permission_token' => str_repeat('p', 48),
            'workspace_inventory.access_url' => 'https://workspace.test/api/integrations/inventory/access',
            'workspace_inventory.access_client_id' => null,
            'workspace_inventory.access_client_secret' => null,
            'workspace_inventory.enforce_web_access' => false,
        ]);
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        $this->createFixtureSchema();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->operator = $this->makeUser('operator@example.test', 'bodega');
        $this->requester = $this->makeUser('requester@example.test', 'solicitante');
        $this->other = $this->makeUser('other@example.test', 'solicitante');
        $this->seedInventory();
        $this->fakeDecision(['allowed' => true, 'version' => 4]);
        Route::middleware(['web', 'auth'])->get('/workspace-inventory-test-page', function () {
            return response()->json(['protected' => true]);
        });
    }

    private function createFixtureSchema(): void
    {
        // Deliberately use isolated fixtures, never the incomplete production migrations.
        Schema::create('users', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->integer('estatus')->nullable()->default(0);
            $t->rememberToken();
            $t->timestamps();
        });
        foreach (['roles', 'permissions'] as $name) {
            Schema::create($name, function (Blueprint $t) {
                $t->bigIncrements('id');
                $t->string('name');
                $t->string('guard_name');
                $t->timestamps();
            });
        }
        Schema::create('model_has_roles', function (Blueprint $t) {
            $t->unsignedBigInteger('role_id');
            $t->string('model_type');
            $t->unsignedBigInteger('model_id');
            $t->primary(['role_id', 'model_id', 'model_type']);
        });
        Schema::create('model_has_permissions', function (Blueprint $t) {
            $t->unsignedBigInteger('permission_id');
            $t->string('model_type');
            $t->unsignedBigInteger('model_id');
            $t->primary(['permission_id', 'model_id', 'model_type']);
        });
        Schema::create('role_has_permissions', function (Blueprint $t) {
            $t->unsignedBigInteger('permission_id');
            $t->unsignedBigInteger('role_id');
            $t->primary(['permission_id', 'role_id']);
        });
        Schema::create('warehouses', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('nombre');
            $t->integer('estatus')->default(0);
        });
        Schema::create('products', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('nombre');
            $t->string('barra')->nullable();
            $t->string('presentacion')->nullable();
            $t->string('tipo')->default('PRODUCTO');
            $t->integer('estatus')->default(0);
            $t->decimal('costo', 15, 4)->default(0);
        });
        Schema::create('productstocks', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('producto_id');
            $t->unsignedBigInteger('bodega');
            $t->decimal('existencia', 15, 4)->nullable();
        });
        Schema::create('movdetails', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->date('fecha');
            $t->string('tipodoc');
            $t->string('movimiento');
            $t->unsignedBigInteger('producto_id');
            $t->string('producto');
            $t->unsignedBigInteger('bodega');
            $t->decimal('cantidad', 15, 4);
            $t->unsignedBigInteger('numerodoc');
            $t->unsignedBigInteger('paciente_id')->default(0);
        });
        Schema::create('reqhead', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->date('fecha');
            $t->string('tipodoc');
            $t->unsignedBigInteger('bodega')->nullable();
            $t->unsignedBigInteger('user_id');
            $t->integer('estatus')->default(0);
            $t->string('despachado')->default('N');
            $t->string('concepto')->nullable();
        });
    }

    private function makeUser(string $email, ?string $role = null): User
    {
        $user = User::create(['name' => $email, 'email' => $email, 'password' => 'test-only', 'estatus' => 0]);
        if ($role) {
            $user->assignRole(Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']));
        }
        return $user;
    }

    private function seedInventory(): void
    {
        DB::table('warehouses')->insert([
            ['id' => 1, 'nombre' => 'Bodega A', 'estatus' => 0],
            ['id' => 2, 'nombre' => 'Bodega B', 'estatus' => 0],
        ]);
        DB::table('products')->insert([
            ['id' => 1, 'nombre' => 'Guantes', 'barra' => 'G-1', 'presentacion' => 'Caja', 'tipo' => 'PRODUCTO', 'estatus' => 0, 'costo' => 99],
            ['id' => 2, 'nombre' => 'Gasas', 'barra' => 'G-2', 'presentacion' => null, 'tipo' => 'PRODUCTO', 'estatus' => 1, 'costo' => 88],
            ['id' => 3, 'nombre' => 'Servicio', 'barra' => null, 'presentacion' => null, 'tipo' => 'SERVICIO', 'estatus' => 0, 'costo' => 77],
        ]);
        DB::table('productstocks')->insert([
            ['id' => 1, 'producto_id' => 1, 'bodega' => 1, 'existencia' => '12.50'],
            ['id' => 2, 'producto_id' => 1, 'bodega' => 2, 'existencia' => '0'],
        ]);
        DB::table('movdetails')->insert([
            ['id' => 1, 'fecha' => '2026-04-01', 'tipodoc' => 'CPRA', 'movimiento' => 'E', 'producto_id' => 1, 'producto' => 'Guantes', 'bodega' => 1, 'cantidad' => 20, 'numerodoc' => 101, 'paciente_id' => 999],
            ['id' => 2, 'fecha' => '2026-04-02', 'tipodoc' => 'SGAS', 'movimiento' => 'S', 'producto_id' => 1, 'producto' => 'Guantes', 'bodega' => 1, 'cantidad' => '7.50', 'numerodoc' => 102, 'paciente_id' => 999],
            ['id' => 3, 'fecha' => '2026-04-03', 'tipodoc' => 'CPRA', 'movimiento' => 'E', 'producto_id' => 2, 'producto' => 'Gasas', 'bodega' => 2, 'cantidad' => 5, 'numerodoc' => 103, 'paciente_id' => 999],
        ]);
        DB::table('reqhead')->insert([
            ['id' => 1, 'fecha' => '2026-04-01', 'tipodoc' => 'PGAS', 'bodega' => 1, 'user_id' => $this->requester->id, 'estatus' => 0, 'despachado' => 'N', 'concepto' => 'Fixture privado'],
            ['id' => 2, 'fecha' => '2026-04-02', 'tipodoc' => 'PGAS', 'bodega' => 2, 'user_id' => $this->other->id, 'estatus' => 1, 'despachado' => 'N', 'concepto' => 'Fixture privado'],
            ['id' => 3, 'fecha' => '2026-04-03', 'tipodoc' => 'PGAS', 'bodega' => 1, 'user_id' => $this->requester->id, 'estatus' => 1, 'despachado' => 'S', 'concepto' => 'Fixture privado'],
        ]);
    }

    private function fakeDecision($decision, int $status = 200, string $contentType = 'application/json'): void
    {
        Http::swap(new Factory());
        Http::fake(function () use ($decision, $status, $contentType) {
            return Http::response($decision, $status, ['Content-Type' => $contentType]);
        });
    }

    private function gatewayHeaders(?string $email = null): array
    {
        return [
            'Authorization' => 'Bearer ' . str_repeat('g', 48),
            'X-Workspace-Actor' => $email ?? $this->operator->email,
        ];
    }

    private function api(string $resource, ?string $email = null)
    {
        return $this->getJson('https://inventory.test/api/workspace-inventory/' . $resource, $this->gatewayHeaders($email));
    }

    public function test_bootstrap_resolves_existing_actor_without_granting_roles(): void
    {
        $rolesBefore = DB::table('model_has_roles')->count();
        $this->api('bootstrap', '  OPERATOR@EXAMPLE.TEST  ')
            ->assertOk()->assertJsonPath('schema_version', 1)
            ->assertJsonPath('source', 'mendoza')->assertJsonPath('capabilities.write', false)
            ->assertJsonPath('capabilities.stocks', true)->assertJsonPath('user.email', $this->operator->email);
        $this->assertSame($rolesBefore, DB::table('model_has_roles')->count());
        Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                $request->url() === 'https://workspace.test/api/integrations/inventory/access' &&
                $request['email'] === $this->operator->email &&
                $request->hasHeader('Authorization', 'Bearer ' . str_repeat('p', 48));
        });
    }

    public function test_gateway_requires_https_valid_token_and_actor_header(): void
    {
        $this->getJson('http://inventory.test/api/workspace-inventory/bootstrap', $this->gatewayHeaders())->assertStatus(400);
        $this->getJson('https://inventory.test/api/workspace-inventory/bootstrap', [
            'Authorization' => 'Bearer invalid', 'X-Workspace-Actor' => $this->operator->email,
        ])->assertStatus(401);
        $this->getJson('https://inventory.test/api/workspace-inventory/bootstrap?email=' . $this->operator->email, [
            'Authorization' => 'Bearer ' . str_repeat('g', 48),
        ])->assertStatus(403);
        Http::assertNothingSent();
    }

    public function test_missing_inactive_or_unprivileged_accounts_are_denied_without_provisioning(): void
    {
        $this->api('bootstrap', 'missing@example.test')->assertStatus(403);
        DB::table('users')->where('id', $this->operator->id)->update(['estatus' => 1]);
        $this->api('bootstrap')->assertStatus(403);
        DB::table('users')->where('id', $this->operator->id)->update(['estatus' => null]);
        $this->api('bootstrap')->assertStatus(403);
        Http::assertNothingSent();
        $unprivileged = $this->makeUser('unprivileged@example.test');
        $this->api('bootstrap', $unprivileged->email)->assertStatus(403);
        $this->assertSame(4, DB::table('users')->count());
    }

    public function test_existing_named_permission_is_respected_without_role_promotion(): void
    {
        $user = $this->makeUser('permission@example.test');
        $user->givePermissionTo(Permission::create(['name' => 'bodega', 'guard_name' => 'web']));
        $this->api('bootstrap', $user->email)->assertOk()->assertJsonPath('capabilities.catalog', true);
        $this->assertSame(0, $user->fresh()->roles()->count());
    }

    public function test_requester_sees_only_own_requests_and_warehouses(): void
    {
        $this->api('bootstrap', $this->requester->email)->assertOk()
            ->assertJsonPath('capabilities.catalog', false)
            ->assertJsonPath('capabilities.requests', true)
            ->assertJsonCount(1, 'warehouses')->assertJsonPath('warehouses.0.id', 1);
        $response = $this->api('requests?user_id=' . $this->other->id, $this->requester->email)
            ->assertOk()->assertJsonPath('total', 2);
        $this->assertSame([3, 1], array_column($response->json('items'), 'id'));
        $this->api('requests?warehouse_id=2', $this->requester->email)->assertOk()->assertJsonPath('total', 0);
        foreach (['products', 'stocks', 'movements'] as $resource) {
            $this->api($resource, $this->requester->email)->assertStatus(403);
        }
    }

    public function test_filters_pagination_and_read_models_return_only_contract_fields(): void
    {
        $products = $this->api('products?per_page=1&page=1')->assertOk()->assertJsonPath('total', 2)
            ->assertJsonPath('pages', 2)->assertJsonPath('items.0.name', 'Gasas')
            ->assertJsonPath('items.0.active', false)->json('items');
        $this->assertSame(['id', 'code', 'name', 'presentation', 'active'], array_keys($products[0]));
        $this->api('products?q=G-1')->assertOk()->assertJsonPath('total', 1)->assertJsonPath('items.0.id', 1);
        $stocks = $this->api('stocks?warehouse_id=1&q=Guantes')->assertOk()->assertJsonPath('total', 1)->json('items');
        $this->assertSame(12.5, (float) $stocks[0]['quantity']);
        $this->api('stocks?q=Gasas')->assertOk()->assertJsonPath('total', 0); // Do not invent zero balances.
        $movements = $this->api('movements?product_id=1&warehouse_id=1&from=2026-04-02&to=2026-04-02')
            ->assertOk()->assertJsonPath('total', 1)->assertJsonPath('items.0.id', 2)->json('items');
        $this->assertArrayNotHasKey('paciente_id', $movements[0]);
        $requests = $this->api('requests')->assertOk()->assertJsonPath('total', 3)->json('items');
        $this->assertSame(['dispatched', 'authorized', 'pending'], array_column($requests, 'status'));
        $this->assertArrayNotHasKey('concepto', $requests[0]);
    }

    public function test_bad_filters_are_rejected_and_query_strings_cannot_escape_scope(): void
    {
        foreach (['per_page=51', 'page=0', 'page=10001', 'q=' . str_repeat('a', 101), 'q[]=x'] as $query) {
            $this->api('products?' . $query)->assertStatus(422);
        }
        foreach (['warehouse_id=-1', 'product_id=no', 'from=2026-02-30', 'from=2026-04-02&to=2026-04-01'] as $query) {
            $this->api('movements?' . $query)->assertStatus(422);
        }
        $this->api('products?q=' . urlencode("' OR 1=1 --"))->assertOk()->assertJsonPath('total', 0);
        $this->api('requests?q=' . urlencode($this->other->email), $this->requester->email)
            ->assertOk()->assertJsonPath('total', 0);
    }

    public function test_uninterpretable_balance_is_an_error_not_a_zero(): void
    {
        DB::table('productstocks')->where('id', 1)->update(['existencia' => null]);
        $this->api('stocks')->assertStatus(503)->assertJsonPath('code', 'inventory_data_unavailable');
    }

    public function test_revocation_is_checked_on_every_request_without_cached_allow(): void
    {
        Http::swap(new Factory());
        Http::fakeSequence()->push(['allowed' => true, 'version' => 4], 200, ['Content-Type' => 'application/json'])
            ->push(['allowed' => false, 'version' => 5], 200, ['Content-Type' => 'application/json']);
        $this->api('stocks')->assertOk();
        $this->api('stocks')->assertStatus(403)->assertJsonPath('code', 'inventory_access_denied');
        Http::assertSentCount(2);
    }

    public function test_callback_transport_is_private_bounded_and_never_follows_redirects(): void
    {
        config(['workspace_inventory.access_client_id' => 'test-client', 'workspace_inventory.access_client_secret' => 'test-client-secret']);
        Http::swap(new Factory());
        $captured = [];
        Http::fake(function ($request, $options) use (&$captured) {
            $captured = $options;
            return Http::response('', 302, ['Location' => 'https://untrusted.test/']);
        });
        $this->api('bootstrap')->assertStatus(503);
        $this->assertFalse($captured['allow_redirects']);
        $this->assertTrue($captured['verify']);
        $this->assertSame(5, $captured['timeout']);
        $this->assertSame(3, $captured['connect_timeout']);
        Http::assertSentCount(1);
        Http::assertSent(function ($request) {
            return $request->hasHeader('CF-Access-Client-Id', 'test-client') &&
                $request->hasHeader('CF-Access-Client-Secret', 'test-client-secret');
        });
    }

    public function test_callback_failure_malformed_allow_or_html_never_grants_access(): void
    {
        $cases = [
            [['allowed' => true, 'version' => 1], 500, 'application/json'],
            ['<html>Login</html>', 200, 'text/html'],
            ['{invalid', 200, 'application/json'],
            [['allowed' => 'true', 'version' => 1], 200, 'application/json'],
            [['allowed' => true, 'version' => '1'], 200, 'application/json'],
            [['allowed' => true], 200, 'application/json'],
        ];
        foreach ($cases as [$body, $status, $contentType]) {
            $this->fakeDecision($body, $status, $contentType);
            $this->api('bootstrap')->assertStatus(503);
        }
        Http::swap(new Factory());
        Http::fake(function () {
            throw new ConnectionException('test-only timeout');
        });
        $this->api('bootstrap')->assertStatus(503);
    }

    public function test_missing_shared_or_unsafe_callback_configuration_is_fail_closed(): void
    {
        config(['workspace_inventory.permission_token' => str_repeat('g', 48)]);
        $this->api('bootstrap')->assertStatus(503);
        config(['workspace_inventory.permission_token' => str_repeat('p', 48), 'workspace_inventory.access_url' => 'http://workspace.test/api/integrations/inventory/access']);
        $this->api('bootstrap')->assertStatus(503);
        config(['workspace_inventory.access_url' => 'https://workspace.test/api/integrations/inventory/access', 'workspace_inventory.access_client_id' => 'incomplete']);
        $this->api('bootstrap')->assertStatus(503);
        config(['workspace_inventory.token' => 'short']);
        $this->api('bootstrap')->assertStatus(503);
        Http::assertNothingSent();
    }

    public function test_all_successful_reads_leave_inventory_users_and_roles_unchanged(): void
    {
        $tables = ['products', 'productstocks', 'movdetails', 'reqhead', 'users', 'model_has_roles', 'model_has_permissions'];
        $before = [];
        foreach ($tables as $table) {
            $before[$table] = DB::table($table)->get()->toJson();
        }
        foreach (['bootstrap', 'products', 'stocks', 'movements', 'requests'] as $resource) {
            $response = $this->api($resource)->assertOk();
            $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        }
        $this->postJson('https://inventory.test/api/workspace-inventory/products', ['name' => 'Unauthorized write'], $this->gatewayHeaders())
            ->assertStatus(405);
        foreach ($tables as $table) {
            $this->assertSame($before[$table], DB::table($table)->get()->toJson());
        }
    }

    public function test_web_flag_preserves_legacy_behavior_until_activation(): void
    {
        $this->fakeDecision(['allowed' => false, 'version' => 5]);
        $this->actingAs($this->operator)->get('https://inventory.test/workspace-inventory-test-page')->assertOk();
        Http::assertNothingSent();
    }

    public function test_activated_web_guard_rechecks_existing_sessions_and_blocks_reports_and_livewire(): void
    {
        config(['workspace_inventory.enforce_web_access' => true]);
        $this->actingAs($this->operator)->get('https://inventory.test/workspace-inventory-test-page')->assertOk();
        $this->fakeDecision(['allowed' => false, 'version' => 5]);
        $this->get('https://inventory.test/workspace-inventory-test-page')->assertStatus(403);
        $this->get('https://inventory.test/detallemov/1')->assertStatus(403);
        $this->postJson('https://inventory.test/livewire/message/alertas-controller', [])->assertStatus(403);
        Http::assertSentCount(3);
        $this->post('https://inventory.test/logout')->assertStatus(302);
        Http::assertSentCount(3);
    }

    public function test_activated_web_guard_blocks_callback_outage_and_inactive_accounts(): void
    {
        config(['workspace_inventory.enforce_web_access' => true]);
        $this->fakeDecision('', 503);
        $this->actingAs($this->operator)->get('https://inventory.test/workspace-inventory-test-page')->assertStatus(503);
        $this->operator->estatus = 1;
        $this->actingAs($this->operator)->get('https://inventory.test/workspace-inventory-test-page')->assertStatus(403);
        Http::assertSentCount(1);
    }

    public function test_activated_web_guard_keeps_login_and_guest_redirect_available(): void
    {
        config(['workspace_inventory.enforce_web_access' => true]);
        $this->fakeDecision('', 503);
        $this->get('https://inventory.test/login')->assertOk();
        $this->get('https://inventory.test/products')->assertRedirect('https://inventory.test/login');
        $this->postJson('https://inventory.test/livewire/message/alertas-controller', [])->assertStatus(401);
        Http::assertNothingSent();
    }

    public function test_missing_user_status_is_not_treated_as_active(): void
    {
        $user = new User(['name' => 'Fixture', 'email' => 'fixture@example.test']);
        $this->expectException(\Illuminate\Http\Exceptions\HttpResponseException::class);
        app(WorkspaceInventoryAccess::class)->assertActive($user);
    }

    public function test_schema_failure_returns_a_service_error_without_sql_or_fake_empty_data(): void
    {
        Schema::drop('productstocks');
        $response = $this->api('stocks')->assertStatus(503)
            ->assertJsonPath('code', 'inventory_data_unavailable');
        $this->assertArrayNotHasKey('items', $response->json());
        $this->assertStringNotContainsString('select ', strtolower($response->getContent()));
    }
}
