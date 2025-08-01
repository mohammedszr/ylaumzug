<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ihr Angebot von YLA Umzug</title>
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
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f8fafc;
            padding: 30px 20px;
            border-radius: 0 0 8px 8px;
        }
        .quote-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2563eb;
        }
        .services-list {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .services-list h3 {
            color: #2563eb;
            margin-top: 0;
        }
        .service-item {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .service-item:last-child {
            border-bottom: none;
        }
        .total-amount {
            background: #dbeafe;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
            border: 2px solid #2563eb;
        }
        .total-amount .amount {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
        }
        .cta-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 10px;
        }
        .contact-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .highlight {
            background: #fef3c7;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #f59e0b;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚚 YLA Umzug</h1>
        <p>Ihr professionelles Angebot ist bereit!</p>
    </div>

    <div class="content">
        <p>Liebe/r {{ $customerName }},</p>

        <p>vielen Dank für Ihr Interesse an unseren Dienstleistungen! Wir haben Ihre Anfrage sorgfältig geprüft und freuen uns, Ihnen ein detailliertes Angebot unterbreiten zu können.</p>

        <div class="quote-info">
            <h3>📋 Angebotsinformationen</h3>
            <p><strong>Angebotsnummer:</strong> {{ $quoteNumber }}</p>
            <p><strong>Gewünschte Services:</strong> {{ $services }}</p>
            <p><strong>Gültig bis:</strong> {{ $validUntil }}</p>
        </div>

        @if($totalAmount)
        <div class="total-amount">
            <p><strong>Gesamtbetrag (inkl. 19% MwSt.)</strong></p>
            <div class="amount">{{ number_format($totalAmount, 2, ',', '.') }} €</div>
        </div>
        @endif

        <div class="highlight">
            <strong>📎 PDF-Angebot im Anhang</strong><br>
            Sie finden Ihr detailliertes Angebot als PDF-Datei im Anhang dieser E-Mail. Das PDF enthält alle Einzelheiten zu den gewünschten Leistungen, Preisen und unseren Geschäftsbedingungen.
        </div>

        <div class="cta-section">
            <h3>🎯 Bereit für den nächsten Schritt?</h3>
            <p>Wir stehen Ihnen gerne für Fragen zur Verfügung oder vereinbaren einen unverbindlichen Besichtigungstermin.</p>
            
            <a href="tel:+4968112345" class="cta-button">📞 Jetzt anrufen</a>
            <a href="mailto:info@yla-umzug.de" class="cta-button">✉️ E-Mail senden</a>
        </div>

        <div class="contact-info">
            <h3>📞 Kontakt & Beratung</h3>
            <p>
                <strong>Telefon:</strong> +49 (0) 681 123 456<br>
                <strong>E-Mail:</strong> info@yla-umzug.de<br>
                <strong>Website:</strong> www.yla-umzug.de
            </p>
            <p>
                <strong>Servicezeiten:</strong><br>
                Montag - Freitag: 8:00 - 18:00 Uhr<br>
                Samstag: 9:00 - 14:00 Uhr
            </p>
        </div>

        <div class="services-list">
            <h3>✅ Warum YLA Umzug?</h3>
            <div class="service-item">🛡️ Vollversichert und zertifiziert</div>
            <div class="service-item">👥 Erfahrenes und geschultes Team</div>
            <div class="service-item">📦 Professionelle Verpackung und Transport</div>
            <div class="service-item">🌍 Umweltfreundliche Entsorgung</div>
            <div class="service-item">💰 Transparente Preise ohne versteckte Kosten</div>
            <div class="service-item">⭐ Über 500 zufriedene Kunden</div>
        </div>

        <p>Wir freuen uns darauf, Ihren Umzug zu einem stressfreien Erlebnis zu machen!</p>

        <p>Mit freundlichen Grüßen<br>
        <strong>Ihr YLA Umzug Team</strong></p>
    </div>

    <div class="footer">
        <p>
            YLA Umzug • Saarbrücken • Trier • Kaiserslautern<br>
            Telefon: +49 (0) 681 123 456 • E-Mail: info@yla-umzug.de<br>
            Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.
        </p>
    </div>
</body>
</html>