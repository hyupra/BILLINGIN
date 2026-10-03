<?php

use App\Modules\Identity\Models\LoginHistory;
use App\Modules\Identity\Models\User;
use App\Modules\Platform\Models\Tenant;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

// CSRF is disabled per test rather than simulating the GET-then-POST token
// round trip a real browser does — standard practice for a focused auth test.
beforeEach(fn () => $this->withoutMiddleware(PreventRequestForgery::class));

test('a tenant admin can log in and a login history row is recorded', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'email' => 'admin@login-test.example',
        'password' => 'correct-password',
    ]);

    $response = $this->post('/login', [
        'email' => 'admin@login-test.example',
        'password' => 'correct-password',
    ]);

    $response->assertRedirect('/home');
    $this->assertAuthenticatedAs($user);

    expect(LoginHistory::where('user_id', $user->id)->exists())->toBeTrue();
});

test('login fails with the wrong password', function () {
    $tenant = Tenant::factory()->create();
    User::factory()->create([
        'tenant_id' => $tenant->id,
        'email' => 'admin@login-test.example',
        'password' => 'correct-password',
    ]);

    $response = $this->post('/login', [
        'email' => 'admin@login-test.example',
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});
