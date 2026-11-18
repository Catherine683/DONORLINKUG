<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\DriveStatus;
use App\Enums\Role;
use App\Models\Appointment;
use App\Models\Drive;
use App\Models\Slot;
use App\Models\User;
use App\Support\DateHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_staff_calendar(): void
    {
        $response = $this->get(route('staff.calendar'));
        $response->assertRedirect(route('login'));
    }

    public function test_donor_cannot_access_staff_calendar(): void
    {
        $donor = User::factory()->create([
            'role' => Role::Donor,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($donor)->get(route('staff.calendar'));
        $response->assertForbidden();
    }

    public function test_staff_can_view_staff_calendar(): void
    {
        $staff = User::factory()->create([
            'role' => Role::Staff,
            'email_verified_at' => now(),
        ]);

        $today = DateHelper::today();
        $drive = Drive::factory()->create([
            'title' => 'Nakasero Regional Blood Drive',
            'venue' => 'Nakasero Blood Bank',
            'starts_on' => $today->toDateString(),
            'ends_on' => $today->toDateString(),
            'status' => DriveStatus::Published,
        ]);

        $response = $this->actingAs($staff)->get(route('staff.calendar'));

        $response->assertOk();
        $response->assertSee('Drives Schedule Calendar');
        $response->assertSee('Nakasero Regional Blood Drive');
    }

    public function test_staff_calendar_month_filtering(): void
    {
        $staff = User::factory()->create([
            'role' => Role::Staff,
            'email_verified_at' => now(),
        ]);

        Drive::factory()->create([
            'title' => 'December Festive Drive',
            'starts_on' => '2026-12-15',
            'ends_on' => '2026-12-16',
            'status' => DriveStatus::Published,
        ]);

        $response = $this->actingAs($staff)->get(route('staff.calendar', [
            'month' => '2026-12',
            'status' => 'published',
        ]));

        $response->assertOk();
        $response->assertSee('December 2026');
        $response->assertSee('December Festive Drive');
    }

    public function test_unauthenticated_user_cannot_access_donor_calendar(): void
    {
        $response = $this->get(route('donor.calendar'));
        $response->assertRedirect(route('login'));
    }

    public function test_donor_can_view_calendar_with_appointments(): void
    {
        $donor = User::factory()->create([
            'role' => Role::Donor,
            'email_verified_at' => now(),
        ]);

        $drive = Drive::factory()->create([
            'title' => 'Makerere University Blood Drive',
            'starts_on' => '2026-11-20',
            'ends_on' => '2026-11-20',
            'status' => DriveStatus::Published,
        ]);

        $slot = Slot::factory()->create([
            'drive_id' => $drive->id,
            'starts_at' => '2026-11-20 07:00:00',
            'ends_at' => '2026-11-20 07:30:00',
        ]);

        Appointment::factory()->create([
            'user_id' => $donor->id,
            'slot_id' => $slot->id,
            'status' => AppointmentStatus::Booked,
        ]);

        $response = $this->actingAs($donor)->get(route('donor.calendar', ['month' => '2026-11']));

        $response->assertOk();
        $response->assertSee('Donation Schedule Calendar');
        $response->assertSee('Makerere University Blood Drive');
        $response->assertSee('My Appointment');
    }

    public function test_donor_can_export_ics_calendar(): void
    {
        $donor = User::factory()->create([
            'role' => Role::Donor,
            'email_verified_at' => now(),
        ]);

        $drive = Drive::factory()->create([
            'title' => 'Mulago Blood Drive',
            'venue' => 'Mulago Complex',
            'starts_on' => '2026-11-25',
            'ends_on' => '2026-11-25',
            'status' => DriveStatus::Published,
        ]);

        $slot = Slot::factory()->create([
            'drive_id' => $drive->id,
            'starts_at' => '2026-11-25 08:00:00',
            'ends_at' => '2026-11-25 08:30:00',
        ]);

        $appointment = Appointment::factory()->create([
            'user_id' => $donor->id,
            'slot_id' => $slot->id,
            'status' => AppointmentStatus::Booked,
        ]);

        $response = $this->actingAs($donor)->get(route('donor.calendar.ics', $appointment));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $this->assertStringContainsString('BEGIN:VCALENDAR', $response->getContent());
        $this->assertStringContainsString('Mulago Blood Drive', $response->getContent());
    }

    public function test_donor_cannot_export_another_donors_ics(): void
    {
        $donorOne = User::factory()->create(['role' => Role::Donor]);
        $donorTwo = User::factory()->create(['role' => Role::Donor]);

        $drive = Drive::factory()->create(['status' => DriveStatus::Published]);
        $slot = Slot::factory()->create(['drive_id' => $drive->id]);
        $appointment = Appointment::factory()->create([
            'user_id' => $donorOne->id,
            'slot_id' => $slot->id,
            'status' => AppointmentStatus::Booked,
        ]);

        $response = $this->actingAs($donorTwo)->get(route('donor.calendar.ics', $appointment));
        $response->assertForbidden();
    }

    public function test_staff_layout_renders_calendar_navigation_link(): void
    {
        $staff = User::factory()->create([
            'role' => Role::Staff,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));
        $response->assertOk();
        $response->assertSee(route('staff.calendar'));
        $response->assertSee('Calendar');
    }

    public function test_donor_layout_renders_calendar_navigation_link(): void
    {
        $donor = User::factory()->create([
            'role' => Role::Donor,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($donor)->get(route('donor.home'));
        $response->assertOk();
        $response->assertSee(route('donor.calendar'));
        $response->assertSee('Calendar');
    }
}
