<?php

namespace Tests\Feature;

use App\Models\User;
use App\Mail\FeedbackSubmission;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_an_email_verification_notification(): void
    {
        Notification::fake();

        $this->postJson('/api/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'verify@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'terms' => true,
            'rules' => true,
        ])->assertCreated();

        $user = User::where('email', 'verify@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification) use ($user): bool {
            $mail = $notification->toMail($user);

            return $mail->subject === 'Apstiprini savu e-pasta adresi | Radu Burziņš'
                && $mail->view === [
                    'html' => 'emails.verify-email',
                    'text' => 'emails.verify-email-text',
                ]
                && $mail->viewData['userName'] === $user->first_name;
        });
    }

    public function test_signed_verification_link_marks_email_verified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'is_approved' => false,
        ]);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $this->get($verificationUrl)
            ->assertRedirect(config('app.frontend_url').'/email-verified');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_unverified_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'password' => 'SecurePassword123!',
            'email_verified_at' => null,
            'is_approved' => true,
        ]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'SecurePassword123!',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_verification_email_can_be_resent_without_disclosing_unknown_addresses(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->postJson('/api/email/verification-notification', ['email' => $user->email])
            ->assertOk()
            ->assertJsonPath('message', 'Ja ar šo e-pastu ir neapstiprināts konts, uz to ir nosūtīta apstiprinājuma saite.');

        Notification::assertSentTo($user, VerifyEmail::class);

        $this->postJson('/api/email/verification-notification', ['email' => 'unknown@example.com'])
            ->assertOk()
            ->assertJsonPath('message', 'Ja ar šo e-pastu ir neapstiprināts konts, uz to ir nosūtīta apstiprinājuma saite.');
    }

    public function test_changing_email_requires_verification_of_the_new_address(): void
    {
        Notification::fake();
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'email_verified_at' => now(),
            'is_approved' => true,
        ]);

        $this->actingAs($user, 'sanctum')
            ->putJson('/api/profile', [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'nickname' => $user->nickname,
                'phone' => $user->phone,
                'date_of_birth' => $user->date_of_birth?->format('Y-m-d'),
                'email' => 'new@example.com',
            ])
            ->assertOk()
            ->assertJsonPath('user.email', 'new@example.com')
            ->assertJsonPath('user.email_verified_at', null);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_approved_user_can_send_feedback_to_the_configured_inbox(): void
    {
        Mail::fake();
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_approved' => true,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/feedback', [
                'type' => 'Ideja',
                'message' => 'Pievienot paziņojumus par ģimenes pasākumiem.',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Paldies! Tava ziņa ir nosūtīta.');

        Mail::assertSent(FeedbackSubmission::class, function (FeedbackSubmission $mail) use ($user): bool {
            return $mail->hasTo(config('mail.feedback_to'))
                && $mail->userEmail === $user->email
                && $mail->type === 'Ideja'
                && $mail->feedbackMessage === 'Pievienot paziņojumus par ģimenes pasākumiem.';
        });
    }

    public function test_feedback_requires_valid_type_and_message(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_approved' => true,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/feedback', [
                'type' => 'Other',
                'message' => 'No',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['type', 'message']);
    }
}
