<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Availability;
use Carbon\Carbon;

/**
 * HealConnect Security & Business Logic Tests
 *
 * Covers the 5 bugs fixed per the QA report:
 *   Fix 1 — Double-booking prevention
 *   Fix 2 — EMR write ownership
 *   Fix 3 — Appointment status updates
 *   Fix 4 — Login brute-force
 *   Fix 5 — Video room URL auth
 *
 * Run: php artisan test --filter HealConnectSecurityTest
 */
class HealConnectSecurityTest extends TestCase
{
    use RefreshDatabase;

    // --- Helpers ---

    private function makeAppointment(
        User $patient,
        User $therapist,
        string $date,
        string $time,
        string $status = 'pending'
    ): Appointment {
        return Appointment::create([
            'patient_id'       => $patient->id,
            'provider_id'      => $therapist->id,
            'provider_type'    => User::class,
            'appointment_type' => 'Consultation',
            'appointment_date' => $date,
            'appointment_time' => $time,
            'status'           => $status,
        ]);
    }

    private function makePatient(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role'                  => 'patient',
            'is_verified_by_admin'  => true,
            'status'                => 'Active',
            'subscription_status'   => 'active',
        ], $attrs));
    }

    private function makeTherapist(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role'                  => 'therapist',
            'is_verified_by_admin'  => true,
            'status'                => 'Active',
            'subscription_status'   => 'active',
        ], $attrs));
    }

    private function makeAvailability(
        User $therapist,
        string $date,
        string $start = '10:00:00',
        string $end = '11:00:00'
    ): Availability {
        return Availability::create([
            'provider_id'   => $therapist->id,
            'provider_type' => User::class,
            'date'          => $date,
            'day_of_week'   => Carbon::parse($date)->format('l'), 
            'start_time'    => $start,
            'end_time'      => $end,
            'is_active'     => true,
        ]);
    }

    /*
     * Fix 1 - Double-booking
     */

    /** @test */
    public function it_rejects_a_booking_for_an_already_taken_slot(): void
    {
        $therapist = $this->makeTherapist();
        $p1  = $this->makePatient();
        $p2  = $this->makePatient();

        $date = now()->addDay()->toDateString();
        $time = '10:00:00';

        $this->makeAvailability($therapist, $date, '10:00:00', '11:00:00');

        // Patient 1 already holds this slot
        $this->makeAppointment($p1, $therapist, $date, $time, 'pending');

        // Patient 2 tries to book the same slot
        $response = $this->actingAs($p2)->post(route('patient.appointments.store'), [
            'therapist_id'     => $therapist->id,
            'appointment_type' => 'Consultation',
            'appointment_date' => $date,
            'appointment_time' => $time,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('appointments', 1);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_allows_booking_a_different_time_slot(): void
    {
        $therapist = $this->makeTherapist();
        $user1  = $this->makePatient();
        $user2  = $this->makePatient();

        $date = now()->addDay()->toDateString();

        $this->makeAvailability($therapist, $date, '09:00:00', '12:00:00');

        $this->makeAppointment($user1, $therapist, $date, '09:00:00', 'pending');

        // Try booking a different time inside the window
        $this->actingAs($user2)->post(route('patient.appointments.store'), [
            'therapist_id'     => $therapist->id,
            'appointment_type' => 'Consultation',
            'appointment_date' => $date,
            'appointment_time' => '10:00:00',
        ]);

        $this->assertDatabaseCount('appointments', 2);
    }

    // Fix 2 - EMR Ownership

    /** @test */
    public function therapist_cannot_update_ehr_of_unlinked_patient(): void
    {
        $docA = $this->makeTherapist();
        $docB = $this->makeTherapist();
        $patient   = $this->makePatient();

        // Linked to A only
        $this->makeAppointment($patient, $docA, now()->addDay()->toDateString(), '10:00:00');

        // B tries to sneak an update
        $response = $this->actingAs($docB)->put(
            route('therapist.ehr.update', $patient->id),
            ['diagnosis' => 'Unauthorized update attempt']
        );

        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_therapist_can_update_ehr_of_linked_patient(): void
    {
        $therapist = $this->makeTherapist();
        $patient   = $this->makePatient();

        $this->makeAppointment($patient, $therapist, now()->addDay()->toDateString(), '10:00:00');

        $response = $this->actingAs($therapist)->put(
            route('therapist.ehr.update', $patient->id),
            ['diagnosis' => 'Knee injury', 'allergies' => 'None']
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function unauthorized_therapist_cannot_modify_treatment_records(): void
    {
        $therapistA = $this->makeTherapist();
        $therapistB = $this->makeTherapist();
        $patientA   = $this->makePatient();

        $this->makeAppointment($patientA, $therapistA, now()->addDay()->toDateString(), '10:00:00');

        $response = $this->actingAs($therapistB)->put(
            route('therapist.treatment.update', $patientA->id),
            ['session_date' => now()->toDateString(), 'description' => 'Unauthorized treatment']
        );

        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_therapist_cannot_update_progress_of_unlinked_patient(): void
    {
        $t1 = $this->makeTherapist();
        $t2 = $this->makeTherapist();
        $p   = $this->makePatient();

        $this->makeAppointment($p, $t1, now()->addDay()->toDateString(), '10:00:00');

        $response = $this->actingAs($t2)->put(
            route('therapist.progress.update', $p->id),
            ['notes' => 'Unauthorized progress note']
        );

        $response->assertForbidden();
    }

    // Fix 3 - Appointment Status Updates

    /** @test */
    public function therapist_cannot_update_status_of_another_therapists_appointment(): void
    {
        $therapistA = $this->makeTherapist();
        $therapistB = $this->makeTherapist();
        $patient    = $this->makePatient();

        $appointment = $this->makeAppointment($patient, $therapistA, now()->addDay()->toDateString(), '10:00:00');

        // B tries to approve A's appointment
        $response = $this->actingAs($therapistB)->patch(
            route('therapist.appointments.updateStatus', $appointment->id),
            ['status' => 'approved']
        );

        $response->assertNotFound();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_update_status_of_own_appointment(): void
    {
        $therapist = $this->makeTherapist();
        $patient   = $this->makePatient();

        $appointment = $this->makeAppointment($patient, $therapist, now()->addDay()->toDateString(), '10:00:00');

        $response = $this->actingAs($therapist)->patch(
            route('therapist.appointments.updateStatus', $appointment->id),
            ['status' => 'approved']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'approved']);
    }

    // Fix 4 - Throttling

    /** @test */
    public function login_throttles_after_five_failed_attempts(): void
    {
        $this->makePatient(['email' => 'test@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.submit'), [
                'email'    => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('login.submit'), [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }

    // Fix 5 - Video Auth

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthorized_user_cannot_join_video_room_via_url(): void
    {
        $intruder = $this->makePatient();
        $room = 'healconnect-999-888-' . time();

        $response = $this->actingAs($intruder)->get(route('video.room', ['room' => $room]));

        $response->assertForbidden();
    }

    /** @test */
    public function caller_can_join_their_own_video_room(): void
    {
        $caller   = $this->makePatient();
        $receiver = $this->makeTherapist();

        $room = "healconnect-{$caller->id}-{$receiver->id}-" . time();

        $response = $this->actingAs($caller)->get(route('video.room', ['room' => $room]));

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function receiver_can_join_video_room_created_by_caller(): void
    {
        $caller   = $this->makePatient();
        $receiver = $this->makeTherapist();

        $room = "healconnect-{$caller->id}-{$receiver->id}-" . time();

        $response = $this->actingAs($receiver)->get(route('video.room', ['room' => $room]));

        $response->assertStatus(200);
    }

    // Fix 6 - Trials

    /** @test */
    public function test_patient_cannot_book_maxed_out_trial_therapist(): void
    {
        $therapist = User::factory()->create([
            'role'                  => 'therapist',
            'is_verified_by_admin'  => true,
            'status'                => 'Active',
            'subscription_status'   => 'inactive',
        ]);

        $date = now()->addDay()->toDateString();
        $this->makeAvailability($therapist, $date, '08:00:00', '15:00:00');

        $p1 = $this->makePatient();
        $p2 = $this->makePatient();

        $this->makeAppointment($p1, $therapist, $date, '09:00:00');
        $this->makeAppointment($p2, $therapist, $date, '10:00:00');

        $newPatient = $this->makePatient();

        $response = $this->actingAs($newPatient)->post(route('patient.appointments.store'), [
            'therapist_id'     => $therapist->id,
            'appointment_type' => 'Consultation',
            'appointment_date' => $date,
            'appointment_time' => '11:00:00',
        ]);

        $response->assertSessionHas('error', 'This therapist has reached their patient capacity on the free trial and cannot accept new patients at this time.');
        $this->assertDatabaseMissing('appointments', [
            'patient_id' => $newPatient->id,
            'provider_id' => $therapist->id,
        ]);
    }
}
