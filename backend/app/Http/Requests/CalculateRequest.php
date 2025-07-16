<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateRequest extends FormRequest
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
            'selectedServices' => 'required|array|min:1',
            'selectedServices.*' => 'string|in:umzug,entruempelung,putzservice',
            
            // Moving details validation
            'movingDetails.apartmentSize' => 'nullable|numeric|min:10|max:1000',
            'movingDetails.rooms' => 'nullable|numeric|min:1|max:20',
            'movingDetails.boxes' => 'nullable|numeric|min:0|max:200',
            'movingDetails.fromAddress.street' => 'nullable|string|max:255',
            'movingDetails.fromAddress.postalCode' => 'nullable|string|max:10',
            'movingDetails.fromAddress.city' => 'nullable|string|max:100',
            'movingDetails.toAddress.street' => 'nullable|string|max:255',
            'movingDetails.toAddress.postalCode' => 'nullable|string|max:10',
            'movingDetails.toAddress.city' => 'nullable|string|max:100',
            'movingDetails.additionalServices' => 'nullable|array',
            'movingDetails.additionalServices.*' => 'string|in:assembly,packing,parking,storage,disposal',
            
            // Cleaning details validation
            'cleaningDetails.size' => 'nullable|numeric|min:10|max:1000',
            'cleaningDetails.cleaningIntensity' => 'nullable|string|in:normal,deep,construction',
            'cleaningDetails.frequency' => 'nullable|string|in:once,weekly,biweekly,monthly',
            'cleaningDetails.rooms' => 'nullable|array',
            'cleaningDetails.rooms.*' => 'string|in:kitchen,bathroom,livingRooms,windows',
            
            // Declutter details validation
            'declutterDetails.volume' => 'nullable|string|in:low,medium,high,extreme',
            'declutterDetails.wasteTypes' => 'nullable|array',
            'declutterDetails.wasteTypes.*' => 'string|in:furniture,electronics,hazardous,household,construction',
            'declutterDetails.cleanHandover' => 'nullable|string|in:yes,no',
            
            // General info validation
            'generalInfo.urgency' => 'nullable|string|in:normal,express'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'selectedServices.required' => 'Bitte wählen Sie mindestens einen Service aus.',
            'selectedServices.min' => 'Bitte wählen Sie mindestens einen Service aus.',
            'selectedServices.*.in' => 'Ungültiger Service ausgewählt.',
            'movingDetails.apartmentSize.numeric' => 'Die Wohnungsgröße muss eine Zahl sein.',
            'movingDetails.apartmentSize.min' => 'Die Wohnungsgröße muss mindestens 10m² betragen.',
            'movingDetails.apartmentSize.max' => 'Die Wohnungsgröße darf maximal 1000m² betragen.',
            'cleaningDetails.size.numeric' => 'Die Größe muss eine Zahl sein.',
            'cleaningDetails.size.min' => 'Die Größe muss mindestens 10m² betragen.',
        ];
    }
}