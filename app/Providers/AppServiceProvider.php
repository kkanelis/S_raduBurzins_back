<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);

        VerifyEmail::toMailUsing(function (User $user, string $verificationUrl): MailMessage {
            $viewData = [
                'userName' => $user->first_name ?: 'draugs',
                'verificationUrl' => $verificationUrl,
                'expirationMinutes' => config('auth.verification.expire', 60),
            ];

            return (new MailMessage)
                ->subject('Apstiprini savu e-pasta adresi | Radu Burziņš')
                ->view('emails.verify-email', $viewData)
                ->text('emails.verify-email-text', $viewData);
        });
    }
}
