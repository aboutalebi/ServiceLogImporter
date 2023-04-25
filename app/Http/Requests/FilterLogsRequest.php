<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterLogsRequest extends FormRequest
{
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
     * @return array<string, array|string>
     */
    public function rules(): array
    {
        return [
            'serviceName' => 'nullable|string',
            'statusCode' => 'nullable|numeric',
            'startDate' => 'nullable|string',
            'endDate' => 'nullable|string',
        ];
    }
}
