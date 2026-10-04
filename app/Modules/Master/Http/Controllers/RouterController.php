<?php

namespace App\Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Master\Http\Requests\StoreRouterRequest;
use App\Modules\Master\Http\Requests\UpdateRouterRequest;
use App\Modules\Master\Models\Router;
use App\Modules\Master\Services\RouterConnectionTester;
use Illuminate\Http\JsonResponse;

class RouterController extends Controller
{
    public function index()
    {
        $routers = Router::withCount('customers')->orderBy('name')->get();

        return view('mockup.dashboard.routers', ['routers' => $routers]);
    }

    public function create()
    {
        return view('mockup.dashboard.routers-create');
    }

    public function store(StoreRouterRequest $request)
    {
        $data = $request->safe()->except(['api_username', 'api_password']);
        $data['api_username_enc'] = $request->input('api_username');
        $data['api_password_enc'] = $request->input('api_password');

        Router::create($data);

        return redirect()->route('routers.index')->with('status', 'Router berhasil ditambahkan.');
    }

    public function edit(Router $router)
    {
        return view('mockup.dashboard.routers-edit', ['router' => $router]);
    }

    public function update(UpdateRouterRequest $request, Router $router)
    {
        $data = $request->safe()->except(['api_username', 'api_password']);

        if ($request->filled('api_username')) {
            $data['api_username_enc'] = $request->input('api_username');
        }
        if ($request->filled('api_password')) {
            $data['api_password_enc'] = $request->input('api_password');
        }

        $router->update($data);

        return redirect()->route('routers.index')->with('status', 'Router berhasil diperbarui.');
    }

    /**
     * Hard delete: customers.router_id is nullOnDelete (Task 3 migration),
     * so any customer still pointing at this router loses that association
     * instead of the delete being blocked. Acceptable for S1 — flagged here
     * for whoever builds the Router Sinkronkan-Secret/isolir flow in S3,
     * since that flow needs every customer to have a live router_id.
     */
    public function destroy(Router $router): \Illuminate\Http\RedirectResponse
    {
        $router->delete();

        return redirect()->route('routers.index')->with('status', 'Router dihapus.');
    }

    public function testConnection(Router $router, RouterConnectionTester $tester): JsonResponse
    {
        $result = $tester->test($router->host, $router->api_port);

        if ($result->blocked) {
            return response()->json(['ok' => false, 'message' => $result->message], 422);
        }

        $router->update([
            'status' => $result->reachable ? 'online' : 'offline',
            'last_seen_at' => $result->reachable ? now() : $router->last_seen_at,
        ]);

        return response()->json([
            'ok' => true,
            'reachable' => $result->reachable,
            'message' => $result->message,
            'latency_ms' => $result->latencyMs,
        ]);
    }
}
