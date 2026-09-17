<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;

class UpdateIdeaRequest extends StoreIdeaRequest
{
    /**
     * Determine if the user is authorized to update the idea.
     *
     * Ideas owned by other users are reported as not found.
     */
    public function authorize(): Response
    {
        return Gate::forUser($this->user())->inspect('update', $this->route('idea'));
    }

    /**
     * Handle a failed validation attempt by reopening the edit dialog.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'edit-idea')
        );
    }
}
