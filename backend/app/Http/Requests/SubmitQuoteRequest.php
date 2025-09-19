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
            'phone' => 'nullable|string|max:50',
            'preferredDate' => 'required|date|after:today',
            'preferredContact' => 'required|in:email,phone,whatsapp',
            'message' => 'nullable|string|max:1000',
            'selectedServices' => 'required|array|min:1',
            'selectedServices.*' => 'in:umzug,putzservice,entruempelung',
            'pricing' => 'nullable|array',
            'pricing.total' => 'nullable|numeric|min:0',
            'pricing.breakdown' => 'nullable|array',
            
            // Service details
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
            'name.required' => 'Name ist erforderlich',
            'name.string' => 'Name muss ein Text sein',
            'name.max' => 'Name darf höchstens 255 Zeichen haben',
            
            'email.required' => 'E-Mail ist erforderlich',
            'email.email' => 'Gültige E-Mail-Adresse erforderlich',
            'email.max' => 'E-Mail darf höchstens 255 Zeichen haben',
            
            'phone.string' => 'Telefonnummer muss ein Text sein',
            'phone.max' => 'Telefonnummer darf höchstens 50 Zeichen haben',
            
            'preferredDate.required' => 'Umzugsdatum ist erforderlich',
            'preferredDate.date' => 'Umzugsdatum muss ein gültiges Datum sein',
            'preferredDate.after' => 'Umzugsdatum muss in der Zukunft liegen',
            
            'preferredContact.required' => 'Bevorzugter Kontakt ist erforderlich',
            'preferredContact.in' => 'Ungültige Kontaktmethode ausgewählt',
            
            'message.string' => 'Nachricht muss ein Text sein',
            'message.max' => 'Nachricht darf höchstens 1000 Zeichen haben',
            
            'selectedServices.required' => 'Mindestens ein Service muss ausgewählt werden',
            'selectedServices.array' => 'Services müssen als Array übermittelt werden',
            'selectedServices.min' => 'Mindestens ein Service muss ausgewählt werden',
            'selectedServices.*.in' => 'Ungültiger Service ausgewählt',
            
            'pricing.array' => 'Preisdaten müssen als Objekt übermittelt werden',
            'pricing.total.numeric' => 'Gesamtpreis muss eine Zahl sein',
            'pricing.total.min' => 'Gesamtpreis muss mindestens 0 sein',
            'pricing.breakdown.array' => 'Preisaufschlüsselung muss als Array übermittelt werden',
            
            'movingDetails.array' => 'Umzugsdetails müssen als Objekt übermittelt werden',
            'cleaningDetails.array' => 'Putzservice-Details müssen als Objekt übermittelt werden',
            'declutterDetails.array' => 'Entrümpelung-Details müssen als Objekt übermittelt werden',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'email' => 'E-Mail',
            'phone' => 'Telefonnummer',
            'preferredDate' => 'Umzugsdatum',
            'preferredContact' => 'Bevorzugter Kontakt',
            'message' => 'Nachricht',
            'selectedServices' => 'Ausgewählte Services',
            'pricing.total' => 'Gesamtpreis',
            'movingDetails' => 'Umzugsdetails',
            'cleaningDetails' => 'Putzservice-Details',
            'declutterDetails' => 'Entrümpelung-Details',
        ];
    }
}