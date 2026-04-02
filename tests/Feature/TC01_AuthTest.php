<?php

use App\Models\User;

// ═══════════════════════════════════════════════════════
// FR-01: Authentication & Authorization
// ═══════════════════════════════════════════════════════

// TC-01-1: Register ด้วยข้อมูลถูกต้อง
test('TC-01-1: register with valid data creates account with student role', function () {
    $response = $this->post('/register', [
        'name'                  => 'Test User',
        'email'                 => 'test@kmutnb.ac.th',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertDatabaseHas('users', [
        'email' => 'test@kmutnb.ac.th',
        'role'  => 'student',
    ]);
});

// TC-01-2: Register ด้วย Email ซ้ำ
test('TC-01-2: register with duplicate email shows validation error', function () {
    User::factory()->create(['email' => 'existing@kmutnb.ac.th']);

    $response = $this->post('/register', [
        'name'                  => 'Another User',
        'email'                 => 'existing@kmutnb.ac.th',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertDatabaseCount('users', 1);
});

// TC-01-3: Login ด้วยข้อมูลถูกต้อง
test('TC-01-3: login with correct credentials redirects to dashboard', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

// TC-01-4: Login ด้วยรหัสผ่านผิด
test('TC-01-4: login with wrong password is rejected', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email'    => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

// TC-01-5: เข้าถึง /dashboard โดยไม่ Login
test('TC-01-5: accessing dashboard without login redirects to login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

// TC-01-6: Student เข้าถึง /create-activity ถูกปฏิเสธ
test('TC-01-6: student cannot access create-activity page', function () {
    $student = User::factory()->create(['role' => 'student']);

    $response = $this->actingAs($student)->get('/create-activity');

    expect($response->status())->toBeIn([302, 403]);
});

// TC-01-7: Admin เข้าถึง /admin/activities ถูกปฏิเสธ
test('TC-01-7: admin role cannot access staff-only admin activities page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin/activities');

    expect($response->status())->toBeIn([302, 403]);
});

// TC-01-8: Staff เข้าถึง /admin/activities ได้ปกติ
test('TC-01-8: staff can access admin activities page', function () {
    $staff = User::factory()->create(['role' => 'staff']);

    $this->actingAs($staff)->get('/admin/activities')->assertStatus(200);
});
