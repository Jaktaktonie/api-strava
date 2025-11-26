<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Throwable;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'locale' => ['nullable', 'string', 'in:pl,en'],
            'timezone' => ['nullable', 'timezone'],
        ]);

        $email = strtolower(trim($data['email']));
        $firstName = trim($data['first_name']);
        $lastName = trim($data['last_name']);
        $fullName = trim($firstName.' '.$lastName);

        $user = User::create([
            'name' => $fullName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'locale' => $data['locale'] ?? 'pl',
            'timezone' => $data['timezone'] ?? 'Europe/Warsaw',
            'email' => $email,
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        try {
            Mail::to($user->email)->send(new WelcomeUserMail($user));
        } catch (Throwable $e) {
            report($e);
        }

        Auth::login($user);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }
}
