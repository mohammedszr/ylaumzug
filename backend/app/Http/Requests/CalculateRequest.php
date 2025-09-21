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
            
            // Moving details validation - matching frontend structure
            'movingDetails' => 'nullable|array',
            'movingDetails.rooms' => 'nullable|numeric|min:1|max:20',
            'movingDetails.fromAddress' => 'nullable|array',
            'movingDetails.fromAddress.street' => 'nullable|string|max:255',
            'movingDetails.fromAddress.postalCode' => 'nullable|string|max:32',
            'movingDetails.fromAddress.city' => 'nullable|string|max:100',
            'movingDetails.toAddress' => 'nullable|array',
            'movingDetails.toAddress.street' => 'nullable|string|max:255',
            'movingDetails.toAddress.postalCode' => 'nullable|string|max:32',
            'movingDetails.toAddress.city' => 'nullable|string|max:100',
            
            // Cleaning details validation - matching frontend structure
            'cleaningDetails' => 'nullable|array',
            'cleaningDetails.objectType' => 'nullable|string|in:apartment,house,office',
            'cleaningDetails.size' => 'nullable|string|in:1-room,2-rooms,3-rooms,4-rooms,5-rooms,6-rooms',
            'cleaningDetails.cleaningIntensity' => 'nullable|string|in:normal,deep,construction',
            
            // Declutter details validation - matching frontend structure
            'declutterDetails' => 'nullable|array',
            'declutterDetails.objectType' => 'nullable|string|in:apartment,house,office,basement,garage',
            'declutterDetails.size' => 'nullable|string|in:small,medium,large,very-large',
            'declutterDetails.address' => 'nullable|array',
            'declutterDetails.address.street' => 'nullable|string|max:255',
            'declutterDetails.address.postalCode' => 'nullable|string|max:32',
            'declutterDetails.address.city' => 'nullable|string|max:100',
            
            // General info validation
            'generalInfo' => 'nullable|array',
            'generalInfo.urgency' => 'nullable|string|in:normal,express'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'selectedServices.required' => 'Bitte wählen Sie mindestens einen Service aus',
            'selectedServices.array' => 'Services müssen als Array übermittelt werden',
            'selectedServices.min' => 'Bitte wählen Sie mindestens einen Service aus',
            'selectedServices.*.string' => 'Service-Name muss ein Text sein',
            'selectedServices.*.in' => 'Ungültiger Service ausgewählt',
            
            // Moving details messages
            'movingDetails.array' => 'Umzugsdetails müssen als Objekt übermittelt werden',
            'movingDetails.rooms.numeric' => 'Zimmeranzahl muss eine Zahl sein',
            'movingDetails.rooms.min' => 'Mindestens 1 Zimmer erforderlich',
            'movingDetails.rooms.max' => 'Maximal 20 Zimmer erlaubt',
            
            'movingDetails.fromAddress.array' => 'Von-Adresse muss als Objekt übermittelt werden',
            'movingDetails.fromAddress.street.string' => 'Straße muss ein Text sein',
            'movingDetails.fromAddress.street.max' => 'Straße darf höchstens 255 Zeichen haben',
            'movingDetails.fromAddress.postalCode.string' => 'Postleitzahl muss ein Text sein',
            'movingDetails.fromAddress.postalCode.max' => 'Postleitzahl darf höchstens 32 Zeichen haben',
            'movingDetails.fromAddress.city.string' => 'Stadt muss ein Text sein',
            'movingDetails.fromAddress.city.max' => 'Stadt darf höchstens 100 Zeichen haben',
            
            'movingDetails.toAddress.array' => 'Nach-Adresse muss als Objekt übermittelt werden',
            'movingDetails.toAddress.street.string' => 'Straße muss ein Text sein',
            'movingDetails.toAddress.street.max' => 'Straße darf höchstens 255 Zeichen haben',
            'movingDetails.toAddress.postalCode.string' => 'Postleitzahl muss ein Text sein',
            'movingDetails.toAddress.postalCode.max' => 'Postleitzahl darf höchstens 32 Zeichen haben',
            'movingDetails.toAddress.city.string' => 'Stadt muss ein Text sein',
            'movingDetails.toAddress.city.max' => 'Stadt darf höchstens 100 Zeichen haben',
            
            // Cleaning details messages
            'cleaningDetails.array' => 'Putzservice-Details müssen als Objekt übermittelt werden',
            'cleaningDetails.objectType.string' => 'Objekttyp muss ein Text sein',
            'cleaningDetails.objectType.in' => 'Ungültiger Objekttyp ausgewählt',
            'cleaningDetails.size.string' => 'Größe muss ein Text sein',
            'cleaningDetails.size.in' => 'Ungültige Größe ausgewählt',
            'cleaningDetails.cleaningIntensity.string' => 'Reinigungsintensität muss ein Text sein',
            'cleaningDetails.cleaningIntensity.in' => 'Ungültige Reinigungsintensität ausgewählt',
            
            // Declutter details messages
            'declutterDetails.array' => 'Entrümpelung-Details müssen als Objekt übermittelt werden',
            'declutterDetails.objectType.string' => 'Objekttyp muss ein Text sein',
            'declutterDetails.objectType.in' => 'Ungültiger Objekttyp ausgewählt',
            'declutterDetails.size.string' => 'Größe muss ein Text sein',
            'declutterDetails.size.in' => 'Ungültige Größe ausgewählt',
            
            'declutterDetails.address.array' => 'Adresse muss als Objekt übermittelt werden',
            'declutterDetails.address.street.string' => 'Straße muss ein Text sein',
            'declutterDetails.address.street.max' => 'Straße darf höchstens 255 Zeichen haben',
            'declutterDetails.address.postalCode.string' => 'Postleitzahl muss ein Text sein',
            'declutterDetails.address.postalCode.max' => 'Postleitzahl darf höchstens 32 Zeichen haben',
            'declutterDetails.address.city.string' => 'Stadt muss ein Text sein',
            'declutterDetails.address.city.max' => 'Stadt darf höchstens 100 Zeichen haben',
            
            // General info messages
            'generalInfo.array' => 'Allgemeine Informationen müssen als Objekt übermittelt werden',
            'generalInfo.urgency.string' => 'Dringlichkeit muss ein Text sein',
            'generalInfo.urgency.in' => 'Ungültige Dringlichkeit ausgewählt',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'selectedServices' => 'Ausgewählte Services',
            'movingDetails.rooms' => 'Zimmeranzahl',
            'movingDetails.fromAddress.street' => 'Von-Straße',
            'movingDetails.fromAddress.postalCode' => 'Von-Postleitzahl',
            'movingDetails.fromAddress.city' => 'Von-Stadt',
            'movingDetails.toAddress.street' => 'Nach-Straße',
            'movingDetails.toAddress.postalCode' => 'Nach-Postleitzahl',
            'movingDetails.toAddress.city' => 'Nach-Stadt',
            'cleaningDetails.objectType' => 'Objekttyp',
            'cleaningDetails.size' => 'Größe',
            'cleaningDetails.cleaningIntensity' => 'Reinigungsintensität',
            'declutterDetails.objectType' => 'Objekttyp',
            'declutterDetails.size' => 'Größe',
            'declutterDetails.address.street' => 'Straße',
            'declutterDetails.address.postalCode' => 'Postleitzahl',
            'declutterDetails.address.city' => 'Stadt',
            'generalInfo.urgency' => 'Dringlichkeit',
        ];
    }
}