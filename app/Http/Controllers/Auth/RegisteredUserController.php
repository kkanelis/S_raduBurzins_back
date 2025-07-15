<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'nickname' => 'required|string|max:50|unique:users,nickname',
            'phone' => 'required|string|max:12|unique:users,phone',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => 'required|boolean',
            'rules' => 'required|boolean',
            'date_of_birth' => 'nullable|date',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'nickname' => $request->nickname,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_approved' => false,
            'terms' => $request->terms,
            'rules' => $request->rules,
            'date_of_birth' => $request->date_of_birth,
        ]);

        // If date_of_birth is provided, add to special days
        if ($user->date_of_birth) {
            \App\Models\SpecialDay::create([
                'user_id' => $user->id,
                'title' => "{$user->first_name} {$user->last_name} svin dzimšanas dienu!",
                'description' => null,
                'date' => $user->date_of_birth,
                'repeats' => true,
            ]);
        }

        event(new Registered($user));

        return response()->json([
            'status' => 'success',
            'message' => 'Reģistrācija veiksmīga! Tavs lietotāja konts tika nosūtīts apstiprināšanai.',
        ], 201);
    }
}
