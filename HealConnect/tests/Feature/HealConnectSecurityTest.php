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
 *   Fix 1 — Double-booking prevention (with DB lock)
 *   Fix 2 — EMR write ownership check
 *   Fix 3 — Appointment status update ownership check
 *   Fix 4 — Login brute-force throttling
 *   Fix 5 — Video room URL authorization
 *
 * Run: php artisan test --filter HealConnectSecurityTest
 */
class HealConnectSecurityTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────

    /** Create a verified/active patient user */
    private function makePatient(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role'                  => 'patient',
            'is_verified_by_admin'  => true,
            'status'                => 'Active',
            'subscription_status'   => 'active',
        ], $attrs));
    }

    /**
     * Create a verified/active independent therapist.
     * subscription_status='active' is required to pass CheckSubscription middleware.
     */
    private function makeTherapist(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role'                  => 'therapist',
            'is_verified_by_admin'  => true,
            'status'                => 'Active',
            'subscription_status'   => 'active',
        ], $attrs));
    }

    /**
     * Create an active availability slot for a therapist on a specific date.
     * day_of_week is NOT NULL in the schema, so we compute it from the date.
     */
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
            'day_of_week'   => Carbon::parse($date)->format('l'), // e.g. "Saturday"
            'start_time'    => $start,
            'end_time'      => $end,
            'is_active'     => true,
        ]);
    }

    /** Create an appointment */
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

    // ──────────────────────────────────────────────────
    // Fix 1 — Double-Booking Prevention
    // ──────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_a_booking_for_an_already_taken_slot(): void
    {
        $therapist = $this->makeTherapist();
        $patient1  = $this->makePatient();
        $patient2  = $this->makePatient();

        $date = now()->addDay()->toDateString();
        $time = '10:00:00';

        $this->makeAvailability($therapist, $date, '10:00:00', '11:00:00');

        // Patient 1 already holds this slot
        $this->makeAppointment($patient1, $therapist, $date, $time, 'pending');

        // Patient 2 tries to book the same slot
        $response = $this->actingAs($patient2)->post(route('patient.appointments.store'), [
            'therapist_id'     => $therapist->id,
            'appointment_type' => 'Consultation',
            'appointment_date' => $date,
            'appointment_time' => $time,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('appointments', 1);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_booking_a_different_time_slot(): void
    {
        $therapist = $this->makeTherapist();
        $patient1  = $this->makePatient();
        $patient2  = $this->makePatient();

        $date = now()->addDay()->toDateString();

        $this->makeAvailability($therapist, $date, '09:00:00', '12:00:00');

        $this->makeAppointment($patient1, $therapist, $date, '09:00:00', 'pending');

        // Patient 2 books a different time inside the same availability window
        $this->actingAs($patient2)->post(route('patient.appointments.store'), [
            'therapist_id'     => $therapist->id,
            'appointment_type' => 'Consultation',
            'appointment_date' => $date,
            'appointment_time' => '10:00:00',
        ]);

        $this->assertDatabaseCount('appointments', 2);
    }

    // ──────────────────────────────────────────────────
    // Fix 2 — EMR Write Ownership
    // ──────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function therapist_cannot_update_ehr_of_unlinked_patient(): void
    {
        $therapistA = $this->makeTherapist();
        $therapistB = $this->makeTherapist();
        $patientA   = $this->makePatient();

        // Patient A is linked ONLY to Therapist A
        $this->makeAppointment($patientA, $therapistA, now()->addDay()->toDateString(), '10:00:00');

        // Therapist B tries to update Patient A's EHR
        $response = $this->actingAs($therapistB)->put(
            route('therapist.ehr.update', $patientA->id),
            ['diagnosis' => 'Unauthorized update attempt']
        );

        $response->assertForbidden(); // Expect 403
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function therapist_can_update_ehr_of_linked_patient(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function therapist_cannot_update_treatment_of_unlinked_patient(): void
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
    public function therapist_cannot_update_progress_of_unlinked_patient(): void
    {
        $therapistA = $this->makeTherapist();
        $therapistB = $this->makeTherapist();
        $patientA   = $this->makePatient();

        $this->makeAppointment($patientA, $therapistA, now()->addDay()->toDateString(), '10:00:00');

        $response = $this->actingAs($therapistB)->put(
            route('therapist.progress.update', $patientA->id),
            ['notes' => 'Unauthorized progress note']
        );

        $response->assertForbidden();
    }

    // ──────────────────────────────────────────────────
    // Fix 3 — Appointment Status Update Ownership
    // ──────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function therapist_cannot_update_status_of_another_therapists_appointment(): void
    {
        $therapistA = $this->makeTherapist();
        $therapistB = $this->makeTherapist();
        $patient    = $this->makePatient();

        // Appointment belongs to Therapist A
        $appointment = $this->makeAppointment($patient, $therapistA, now()->addDay()->toDateString(), '10:00:00');

        // Therapist B tries to update its status — should 404 (firstOrFail with provider scope)
        $response = $this->actingAs($therapistB)->patch(
            route('therapist.appointments.updateStatus', $appointment->id),
            ['status' => 'approved']
        );

        $response->assertNotFound();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function therapist_can_update_status_of_own_appointment(): void
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

    // ──────────────────────────────────────────────────
    // Fix 4 — Login Brute-Force Throttling
    // ──────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_endpoint_throttles_after_5_failed_attempts(): void
    {
        $this->makePatient(['email' => 'test@example.com']);

        // 5 wrong attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.submit'), [
                'email'    => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // 6th attempt should be throttled
        $response = $this->post(route('login.submit'), [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    // ──────────────────────────────────────────────────
    // Fix 5 — Video Room URL Authorization
    // ──────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthorized_user_cannot_join_video_room_via_url(): void
    {
        $intruder = $this->makePatient();

        // Room name contains IDs 999 and 888 — neither matches $intruder->id
        $room = 'healconnect-999-888-' . time();

        $response = $this->actingAs($intruder)->get(route('video.room', ['room' => $room]));

        $response->assertForbidden(); // Expect 403
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function caller_can_join_their_own_video_room(): void
    {
        $caller   = $this->makePatient();
        $receiver = $this->makeTherapist();

        $room = "healconnect-{$caller->id}-{$receiver->id}-" . time();

        $response = $this->actingAs($caller)->get(route('video.room', ['room' => $room]));

        // Should not 403; 200 expected (video page renders without API key in test env)
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

    // ──────────────────────────────────────────────────
    // Fix 6 — Freemium Booking Bypass Fix
    // ──────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function patient_cannot_book_maxed_out_trial_therapist(): void
    {
        // Therapist is on free trial (inactive)
        $therapist = User::factory()->create([
            'role'                  => 'therapist',
            'is_verified_by_admin'  => true,
            'status'                => 'Active',
            'subscription_status'   => 'inactive',
        ]);

        $date = now()->addDay()->toDateString();
        $this->makeAvailability($therapist, $date, '08:00:00', '15:00:00');

        // Create 2 completely different patients to fill the freemium limit
        $existingPatient1 = $this->makePatient();
        $existingPatient2 = $this->makePatient();

        $this->makeAppointment($existingPatient1, $therapist, $date, '09:00:00');
        $this->makeAppointment($existingPatient2, $therapist, $date, '10:00:00');

        // The therapist's customer_count is now organically 2

        // A brand new 3rd patient tries to book
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
