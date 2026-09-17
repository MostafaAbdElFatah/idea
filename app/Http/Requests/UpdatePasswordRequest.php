<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends ProfileDialogRequest
{
    /**
     * The error bag used for this form.
     *
     * @var string
     */
    protected $errorBag = 'updatePassword';

    protected string $dialog = 'change-password';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'confirmed', Password::default()],
        ];
    }
}
