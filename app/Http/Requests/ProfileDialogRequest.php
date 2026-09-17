<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * A profile form submitted from a dialog.
 *
 * Errors go to the request's own error bag, and the dialog is reopened
 * so the user can see and fix them.
 */
abstract class ProfileDialogRequest extends FormRequest
{
    /**
     * The name of the dialog to reopen when validation fails.
     */
    protected string $dialog;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    abstract public function rules(): array;

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            back()
                ->withErrors($validator, $this->errorBag)
                ->withInput($this->except(['current_password', 'email_password', 'delete_password', 'password', 'password_confirmation']))
                ->with('open_modal', $this->dialog)
        );
    }
}
