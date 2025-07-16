NEUE ANFRAGE EINGEGANGEN - {{ $quoteNumber }}

Services: {{ $services }}
@if($estimatedTotal)
Geschätzte Kosten: {{ number_format($estimatedTotal, 2, ',', '.') }}€
@endif

KUNDENDATEN:
Name: {{ $quote->name }}
E-Mail: {{ $quote->email }}
Telefon: {{ $quote->phone }}
Wunschtermin: {{ $quote->preferred_date ? $quote->preferred_date->format('d.m.Y') : 'Nicht angegeben' }}
Bevorzugter Kontakt: {{ $quote->preferred_contact_formatted }}
@if($quote->message)
Nachricht: {{ $quote->message }}
@endif

@foreach($serviceDetails as $serviceName => $details)
{{ strtoupper($serviceName) }}:
@foreach($details as $label => $value)
{{ $label }}: {{ $value }}
@endforeach

@endforeach

NÄCHSTE SCHRITTE:
- Kundenanfrage prüfen und bewerten
- Bei Bedarf Rückfragen stellen  
- Detailliertes Angebot erstellen
- Besichtigungstermin vereinbaren (empfohlen)

Admin-Link: {{ $adminUrl }}
Kunde antworten: {{ $quote->email }}

Eingegangen am: {{ $quote->created_at->format('d.m.Y H:i') }} Uhr