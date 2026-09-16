<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\IdeaStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreIdeaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3','max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(IdeaStatus::class)],
            'links' => ['nullable', 'array'],
            'links.*' => ['url', 'max:255'],
            'steps' => ['nullable', 'array'],
            'steps.*' => ['string', 'max:255'],
        ];
    }

        /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'create-idea')
        );
    }
}
