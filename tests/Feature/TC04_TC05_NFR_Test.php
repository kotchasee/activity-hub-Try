<?php

use App\Models\Activity;
use App\Models\Registration;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// ═══════════════════════════════════════════════════════
// FR-04: Search & Filter
// ═══════════════════════════════════════════════════════

// TC-04-1: ค้นหาด้วยชื่อกิจกรรม
test('TC-04-1: search by title returns only matching activities', function () {
    $user = User::factory()->create();
    Activity::factory()->create(['title' => 'Coding Workshop', 'status' => 'approved']);
    Activity::factory()->create(['title' => 'กีฬาสี',          'status' => 'approved']);

    $response = $this->actingAs($user)->get('/activities?search=Coding');

    $response->assertStatus(200);
    $response->assertViewHas('activities', function ($activities) {
        return $activities->count() === 1
            && str_contains($activities->first()->title, 'Coding');
    });
});

// TC-04-2: กรองด้วย Tag
test('TC-04-2: filter by tag returns only activities with that tag', function () {
    $user      = User::factory()->create();
    $tagSport  = Tag::factory()->create(['name' => 'กีฬา']);
    $tagAcad   = Tag::factory()->create(['name' => 'วิชาการ']);

    $sport = Activity::factory()->create(['status' => 'approved']);
    $acad  = Activity::factory()->create(['status' => 'approved']);
    $sport->tags()->attach($tagSport);
    $acad->tags()->attach($tagAcad);

    $response = $this->actingAs($user)->get("/activities?tag={$tagSport->id}");

    $response->assertStatus(200);
    $response->assertViewHas('activities', function ($activities) use ($sport) {
        return $activities->count() === 1
            && $activities->first()->id === $sport->id;
    });
});

// TC-04-3: กรองด้วยวันที่
test('TC-04-3: filter by date returns only activities on that date', function () {
    $user       = User::factory()->create();
    $targetDate = now()->addDays(5)->format('Y-m-d');

    Activity::factory()->create(['status' => 'approved', 'date' => $targetDate]);
    Activity::factory()->create(['status' => 'approved', 'date' => now()->addDays(20)->format('Y-m-d')]);

    $response = $this->actingAs($user)->get("/activities?date={$targetDate}");

    $response->assertStatus(200);
    $response->assertViewHas('activities', fn ($a) => $a->count() === 1);
});

// TC-04-4: ค้นหาที่ไม่พบผลลัพธ์ → ไม่ Error
test('TC-04-4: search with no results returns empty without error', function () {
    $user = User::factory()->create();
    Activity::factory()->create(['title' => 'Real Activity', 'status' => 'approved']);

    $response = $this->actingAs($user)->get('/activities?search=xyznotexist99999');

    $response->assertStatus(200);
    $response->assertViewHas('activities', fn ($a) => $a->count() === 0);
});

// TC-04-5: Search + Filter Tag พร้อมกัน
test('TC-04-5: combining search keyword and tag filter returns intersection', function () {
    $user     = User::factory()->create();
    $tagSport = Tag::factory()->create(['name' => 'กีฬา']);

    $match   = Activity::factory()->create(['title' => 'วิ่งมาราธอน', 'status' => 'approved']);
    $noTag   = Activity::factory()->create(['title' => 'วิ่งเปี้ยว',   'status' => 'approved']);
    $noTitle = Activity::factory()->create(['title' => 'ว่ายน้ำ',       'status' => 'approved']);

    $match->tags()->attach($tagSport);
    $noTitle->tags()->attach($tagSport);

    $response = $this->actingAs($user)->get("/activities?search=วิ่ง&tag={$tagSport->id}");

    $response->assertStatus(200);
    $response->assertViewHas('activities', function ($activities) use ($match) {
        return $activities->count() === 1
            && $activities->first()->id === $match->id;
    });
});

// ═══════════════════════════════════════════════════════
// FR-05: Analytics
// ═══════════════════════════════════════════════════════

// TC-05-1: Staff เข้า Review Dashboard ได้
test('TC-05-1: staff can access review dashboard with all required data', function () {
    $staff = User::factory()->create(['role' => 'staff']);

    $this->actingAs($staff)->get('/reviews')
        ->assertStatus(200)
        ->assertViewHasAll(['tagStats', 'topViewedActivities', 'tagViewStats', 'topRegistrationActivities', 'stats']);
});

// TC-05-2: Student เข้า /reviews ถูกปฏิเสธ
test('TC-05-2: student is forbidden from review dashboard', function () {
    $student = User::factory()->create(['role' => 'student']);

    $response = $this->actingAs($student)->get('/reviews');

    expect($response->status())->toBeIn([302, 403]);
});

// TC-05-3: Monthly Activities แสดงเฉพาะเดือนปัจจุบัน
test('TC-05-3: dashboard shows only current month approved activities', function () {
    $user = User::factory()->create();
    Activity::factory()->create(['status' => 'approved', 'date' => now()->format('Y-m-d')]);
    Activity::factory()->create(['status' => 'approved', 'date' => now()->subMonths(2)->format('Y-m-d')]);

    $this->actingAs($user)->get('/dashboard')
        ->assertStatus(200)
        ->assertViewHas('monthlyActivities', fn ($m) => $m->count() === 1);
});

// TC-05-4: Hot Activities แสดง Top 3 ตาม view_count
test('TC-05-4: dashboard shows top 3 activities by view count', function () {
    $user = User::factory()->create();
    Activity::factory()->create(['status' => 'approved', 'view_count' => 100, 'title' => 'Top 1']);
    Activity::factory()->create(['status' => 'approved', 'view_count' => 80,  'title' => 'Top 2']);
    Activity::factory()->create(['status' => 'approved', 'view_count' => 60,  'title' => 'Top 3']);
    Activity::factory()->create(['status' => 'approved', 'view_count' => 5,   'title' => 'Not shown']);

    $this->actingAs($user)->get('/dashboard')
        ->assertStatus(200)
        ->assertViewHas('hotActivities', function ($hot) {
            return $hot->count() === 3 && $hot->first()->title === 'Top 1';
        });
});

// ═══════════════════════════════════════════════════════
// Non-Functional Requirements
// ═══════════════════════════════════════════════════════

// TC-NFR-2: เข้า /admin/activities โดยไม่มี role=staff → ถูกปฏิเสธ
test('TC-NFR-2: accessing admin route without staff role is rejected', function () {
    $student = User::factory()->create(['role' => 'student']);

    $response = $this->actingAs($student)->get('/admin/activities');

    expect($response->status())->toBeIn([302, 403]);
});

// TC-NFR-3: Password ใน Database ต้องเป็น bcrypt hash
test('TC-NFR-3: password is stored as bcrypt hash not plaintext', function () {
    $this->post('/register', [
        'name'                  => 'Hash Test',
        'email'                 => 'hash@kmutnb.ac.th',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'hash@kmutnb.ac.th')->first();

    expect($user->password)->not->toBe('password');
    expect(Hash::check('password', $user->password))->toBeTrue();
    expect(str_starts_with($user->password, '$2y$'))->toBeTrue();
});

// TC-NFR-4: Mass Assignment — ส่ง role=staff ใน Register Form ต้องถูก block
test('TC-NFR-4: role cannot be set via mass assignment in register form', function () {
    $this->post('/register', [
        'name'                  => 'Attacker',
        'email'                 => 'attacker@kmutnb.ac.th',
        'password'              => 'password',
        'password_confirmation' => 'password',
        'role'                  => 'staff',
    ]);

    $user = User::where('email', 'attacker@kmutnb.ac.th')->first();

    expect($user)->not->toBeNull();
    expect($user->role)->toBe('student');
});

// TC-NFR-5: Response time < 3 วินาที กับ Activity 100 รายการ
test('TC-NFR-5: dashboard loads within 3 seconds with 100 activities', function () {
    $user = User::factory()->create();
    Activity::factory()->count(100)->create(['status' => 'approved']);

    $start   = microtime(true);
    $this->actingAs($user)->get('/dashboard')->assertStatus(200);
    $elapsed = microtime(true) - $start;

    expect($elapsed)->toBeLessThan(3.0);
})->group('performance');

// TC-NFR-6: ไม่มี N+1 Query ใน ActivityController::index()
test('TC-NFR-6: activity index uses eager loading with tags to avoid N+1', function () {
    $user = User::factory()->create();
    Activity::factory()->count(10)->create(['status' => 'approved']);

    $queryCount = 0;
    DB::listen(function () use (&$queryCount) {
        $queryCount++;
    });

    $this->actingAs($user)->get('/dashboard');

    // Eager loading: query count ควรน้อยกว่า 15 แม้จะมี 10 activities
    // ถ้าเป็น N+1 จะมี 10+ queries จาก tags
    expect($queryCount)->toBeLessThan(15);
})->group('performance');

// TC-NFR-8: สมัครกิจกรรมสำเร็จ → Flash Message แสดง
test('TC-NFR-8: successful activity registration shows flash message', function () {
    $student  = User::factory()->create(['role' => 'student']);
    $activity = Activity::factory()->create([
        'status'           => 'approved',
        'max_participants' => 10,
    ]);

    $this->actingAs($student)
        ->post("/activities/{$activity->id}/register")
        ->assertSessionHas('success');
});
