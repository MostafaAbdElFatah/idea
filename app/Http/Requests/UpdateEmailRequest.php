<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateEmailRequest extends ProfileDialogRequest
{
    /**
     * The error bag used for this form.
     *
     * @var string
     */
    protected $errorBag = 'updateEmail';

    protected string $dialog = 'change-email';

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['email' => trim((string) $this->input('email'))]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user())],
            'email_password' => ['required', 'string', 'current_password'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['email_password' => 'current password'];
    }
}
