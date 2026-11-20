<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:Ideja,Atsauksme'],
            'message' => ['required', 'string', 'min:5', 'max:3000'],
        ]);

        $user = $request->user();

        Mail::to(config('mail.feedback_to'))->send(new FeedbackSubmission(
            userName: $user->full_name,
            userEmail: $user->email,
            type: $validated['type'],
            feedbackMessage: $validated['message'],
        ));

        return response()->json([
            'message' => 'Paldies! Tava ziņa ir nosūtīta.',
        ]);
    }
}
