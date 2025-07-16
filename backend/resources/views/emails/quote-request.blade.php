<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neue Anfrage - {{ $quoteNumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .quote-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .service-section {
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
        }
        .service-title {
            color: #495057;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        .detail-label {
            font-weight: bold;
            min-width: 150px;
            color: #6c757d;
        }
        .detail-value {
            flex: 1;
        }
        .pricing-section {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .total-price {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 10px 0;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 10px;
        }
        .btn:hover {
            background: #5a6fd8;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
        }
        @media (max-width: 600px) {
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Neue Anfrage eingegangen</h1>
        <p>Anfrage-Nr: <strong>{{ $quoteNumber }}</strong></p>
        <p>Services: <strong>{{ $services }}</strong></p>
    </div>

    <div class="quote-info">
        <h2>👤 Kundendaten</h2>
        <div class="detail-row">
            <span class="detail-label">Name:</span>
            <span class="detail-value">{{ $quote->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">E-Mail:</span>
            <span class="detail-value">{{ $quote->email }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Telefon:</span>
            <span class="detail-value">{{ $quote->phone }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Wunschtermin:</span>
            <span class="detail-value">{{ $quote->preferred_date ? $quote->preferred_date->format('d.m.Y') : 'Nicht angegeben' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Bevorzugter Kontakt:</span>
            <span class="detail-value">{{ $quote->preferred_contact_formatted }}</span>
        </div>
        @if($quote->message)
        <div class="detail-row">
            <span class="detail-label">Nachricht:</span>
            <span class="detail-value">{{ $quote->message }}</span>
        </div>
        @endif
    </div>

    @if($estimatedTotal)
    <div class="pricing-section">
        <h2>💰 Geschätzte Kosten</h2>
        <div class="total-price">{{ number_format($estimatedTotal, 2, ',', '.') }}€</div>
        <p style="text-align: center; font-style: italic; color: #6c757d;">
            Unverbindliche Schätzung basierend auf Kundenangaben
        </p>
    </div>
    @endif

    @foreach($serviceDetails as $serviceName => $details)
    <div class="service-section">
        <div class="service-title">{{ $serviceName }}</div>
        @foreach($details as $label => $value)
        <div class="detail-row">
            <span class="detail-label">{{ $label }}:</span>
            <span class="detail-value">{{ $value }}</span>
        </div>
        @endforeach
    </div>
    @endforeach

    <div class="action-buttons">
        <a href="{{ $adminUrl }}" class="btn">📋 Anfrage im Admin öffnen</a>
        <a href="mailto:{{ $quote->email }}?subject=Re: Ihre Anfrage {{ $quoteNumber }}" class="btn">📧 Kunde antworten</a>
    </div>

    <div class="footer">
        <p><strong>Nächste Schritte:</strong></p>
        <ul>
            <li>Kundenanfrage prüfen und bewerten</li>
            <li>Bei Bedarf Rückfragen stellen</li>
            <li>Detailliertes Angebot erstellen</li>
            <li>Besichtigungstermin vereinbaren (empfohlen)</li>
        </ul>
        
        <p><strong>Tipp:</strong> Antworten Sie schnell für bessere Conversion-Raten!</p>
        
        <hr>
        <p>Diese E-Mail wurde automatisch vom YLA Umzug System generiert.</p>
        <p>Eingegangen am: {{ $quote->created_at->format('d.m.Y H:i') }} Uhr</p>
    </div>
</body>
</html>