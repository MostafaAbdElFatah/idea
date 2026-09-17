<?php

declare(strict_types=1);

namespace App\Http\Requests;

class DeleteAccountRequest extends ProfileDialogRequest
{
    /**
     * The error bag used for this form.
     *
     * @var string
     */
    protected $errorBag = 'deleteAccount';

    protected string $dialog = 'delete-account';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'delete_password' => ['required', 'string', 'current_password'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['delete_password' => 'password'];
    }
}
