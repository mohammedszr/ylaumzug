<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitQuoteRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'preferredDate' => 'nullable|date|after:today',
            'preferredContact' => 'nullable|string|in:email,phone',
            'message' => 'nullable|string|max:2000',
            'selectedServices' => 'required|array|min:1',
            'selectedServices.*' => 'string|in:umzug,entruempelung,putzservice',
            'pricingData' => 'nullable|array',
            'pricingData.total' => 'nullable|numeric|min:0',
            'pricingData.breakdown' => 'nullable|array',
            
            // Service details (same as CalculateRequest)
            'movingDetails' => 'nullable|array',
            'cleaningDetails' => 'nullable|array',
            'declutterDetails' => 'nullable|array'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Bitte geben Sie Ihren Namen ein.',
            'email.required' => 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'phone.required' => 'Bitte geben Sie Ihre Telefonnummer ein.',
            'selectedServices.required' => 'Bitte wählen Sie mindestens einen Service aus.',
            'selectedServices.min' => 'Bitte wählen Sie mindestens einen Service aus.',
            'preferredDate.after' => 'Das Datum muss in der Zukunft liegen.',
        ];
    }
}