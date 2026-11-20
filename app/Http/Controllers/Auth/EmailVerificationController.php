<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailVerificationController extends Controller
{
    public function verify(int $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect(rtrim(config('app.frontend_url'), '/').'/email-verified');
    }

    public function resend(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
        ])->validate();

        $user = User::where('email', $validated['email'])->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json([
            'message' => 'Ja ar šo e-pastu ir neapstiprināts konts, uz to ir nosūtīta apstiprinājuma saite.',
        ]);
    }
}
