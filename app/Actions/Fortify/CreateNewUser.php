<?php

namespace App\Actions\Fortify;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $invitation = Invitation::valid()->where('token', $input['invitation'] ?? null)->first();

        if (! config('app.registration_open') && ! $invitation) {
            throw ValidationException::withMessages([
                'email' => 'This registration link is invalid or has expired.',
            ]);
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role' => $invitation->role ?? 'customer',
        ]);

        $invitation?->update(['accepted_at' => now()]);

        return $user;
    }
}
