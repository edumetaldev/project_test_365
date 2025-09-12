<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ChangeReservationStatusRequest extends FormRequest
{
    private const POSSIBLE_STATUSES = [
        'PENDING',
        'CONFIRMED',
        'CANCELLED',
        'CHECKED_IN'
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:' . implode(',', self::POSSIBLE_STATUSES)
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'El estado es requerido.',
            'status.in' => 'El estado debe ser uno de los siguientes: ' . implode(', ', self::POSSIBLE_STATUSES) . '.',
            'status.string' => 'El estado debe ser una cadena de texto.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status' => 'estado'
        ];
    }

    /**
     * Get the validated status value.
     */
    public function getStatus(): string
    {
        return $this->validated('status');
    }

    /**
     * Get all possible statuses.
     *
     * @return array<string>
     */
    public static function getPossibleStatuses(): array
    {
        return self::POSSIBLE_STATUSES;
    }
}
