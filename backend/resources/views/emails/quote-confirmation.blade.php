<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestätigung Ihrer Anfrage - {{ $quoteNumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
        }
        .content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .highlight-box {
            background: #e8f5e8;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .contact-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            margin: 20px 0;
        }
        .steps {
            counter-reset: step-counter;
        }
        .step {
            counter-increment: step-counter;
            margin: 15px 0;
            padding-left: 30px;
            position: relative;
        }
        .step::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            background: #667eea;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">✨ YLA Umzug</div>
        <h1>Vielen Dank für Ihre Anfrage!</h1>
        <p>Anfrage-Nr: <strong>{{ $quoteNumber }}</strong></p>
    </div>

    <div class="content">
        <p>Liebe/r {{ $customerName }},</p>
        
        <p>vielen Dank für Ihre Anfrage über unseren Online-Rechner! Wir haben Ihre Anfrage erfolgreich erhalten und werden sie schnellstmöglich bearbeiten.</p>

        <div class="highlight-box">
            <h3>📋 Ihre Anfrage im Überblick</h3>
            <p><strong>Services:</strong> {{ $services }}</p>
            @if($estimatedTotal)
            <p><strong>Geschätzter Preis:</strong> {{ number_format($estimatedTotal, 2, ',', '.') }}€</p>
            <p style="font-size: 14px; color: #6c757d; font-style: italic;">
                Dies ist eine unverbindliche Schätzung. Das finale Angebot erhalten Sie nach unserer Besichtigung vor Ort.
            </p>
            @endif
        </div>

        <h3>🚀 Was passiert jetzt?</h3>
        <div class="steps">
            <div class="step">
                <strong>Prüfung Ihrer Angaben</strong><br>
                Wir prüfen Ihre Anfrage und bereiten ein detailliertes Angebot vor.
            </div>
            <div class="step">
                <strong>Persönliche Kontaktaufnahme</strong><br>
                Wir melden uns innerhalb von {{ $responseTime }} bei Ihnen zurück.
            </div>
            <div class="step">
                <strong>Besichtigungstermin (empfohlen)</strong><br>
                Für ein präzises Angebot vereinbaren wir gerne einen kostenlosen Besichtigungstermin.
            </div>
            <div class="step">
                <strong>Detailliertes Angebot</strong><br>
                Sie erhalten ein schriftliches Angebot mit Festpreisgarantie.
            </div>
        </div>

        <div class="info-box">
            <h4>💡 Warum eine Besichtigung?</h4>
            <p>Eine kurze Besichtigung vor Ort hilft uns dabei:</p>
            <ul>
                <li>Den Aufwand genau zu kalkulieren</li>
                <li>Besondere Herausforderungen zu erkennen</li>
                <li>Ihnen den bestmöglichen Preis anzubieten</li>
                <li>Alle Ihre Fragen persönlich zu beantworten</li>
            </ul>
        </div>
    </div>

    <div class="contact-info">
        <h3>📞 Haben Sie Fragen?</h3>
        <p>Zögern Sie nicht, uns zu kontaktieren:</p>
        <p>
            <strong>Telefon:</strong> {{ $businessPhone }}<br>
            <strong>E-Mail:</strong> {{ $businessEmail }}
        </p>
        <p>Wir sind gerne für Sie da!</p>
    </div>

    <div class="highlight-box">
        <h4>✅ Ihre Vorteile bei YLA Umzug</h4>
        <ul style="margin: 0; padding-left: 20px;">
            <li><strong>Festpreisgarantie</strong> - Keine versteckten Kosten</li>
            <li><strong>Kostenlose Besichtigung</strong> - Unverbindlich und professionell</li>
            <li><strong>Erfahrenes Team</strong> - Schnell, sicher und zuverlässig</li>
            <li><strong>Komplettservice</strong> - Von der Planung bis zur Übergabe</li>
        </ul>
    </div>

    <div class="footer">
        <p>Mit freundlichen Grüßen<br>
        <strong>Ihr YLA Umzug Team</strong></p>
        
        <hr style="margin: 20px 0;">
        
        <p>Diese E-Mail wurde automatisch generiert.<br>
        Anfrage eingegangen am: {{ $quote->created_at->format('d.m.Y H:i') }} Uhr</p>
        
        <p style="font-size: 12px; color: #999;">
            YLA Umzug - Ihr Partner für Umzug, Entrümpelung und Putzservice<br>
            im Saarland und Rheinland-Pfalz
        </p>
    </div>
</body>
</html>