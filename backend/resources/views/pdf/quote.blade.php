<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Angebot {{ $quote->quote_number }}</title>
    <style>
        @page {
            margin: 2cm;
            @top-center {
                content: "YLA Umzug - Angebot {{ $quote->quote_number }}";
            }
            @bottom-center {
                content: "Seite " counter(page) " von " counter(pages);
            }
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-info {
            float: left;
            width: 50%;
        }
        
        .company-logo {
            font-size: 24pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .company-details {
            font-size: 9pt;
            color: #666;
            line-height: 1.3;
        }
        
        .quote-info {
            float: right;
            width: 45%;
            text-align: right;
        }
        
        .quote-number {
            font-size: 18pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .quote-date {
            font-size: 10pt;
            color: #666;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .customer-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .customer-info {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .services-section {
            margin-bottom: 30px;
        }
        
        .service-item {
            background-color: #f8fafc;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #2563eb;
        }
        
        .service-title {
            font-size: 12pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .service-details {
            font-size: 10pt;
            line-height: 1.5;
        }
        
        .service-details .detail-item {
            margin-bottom: 5px;
        }
        
        .pricing-section {
            margin-bottom: 30px;
        }
        
        .pricing-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .pricing-table th,
        .pricing-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .pricing-table th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }
        
        .pricing-table .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
            font-size: 12pt;
        }
        
        .total-row td {
            border-top: 2px solid #2563eb;
            border-bottom: 2px solid #2563eb;
        }
        
        .terms-section {
            margin-top: 40px;
            font-size: 9pt;
            color: #666;
            line-height: 1.4;
        }
        
        .terms-title {
            font-size: 11pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }
        
        .highlight {
            background-color: #fef3c7;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #f59e0b;
            margin: 20px 0;
        }
        
        .contact-cta {
            background-color: #dbeafe;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin: 30px 0;
        }
        
        .contact-cta h3 {
            color: #2563eb;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="company-info">
            <div class="company-logo">YLA Umzug</div>
            <div class="company-details">
                Ihr zuverlässiger Partner für Umzüge, Entrümpelung und Reinigung<br>
                Saarbrücken • Trier • Kaiserslautern<br><br>
                
                <strong>Kontakt:</strong><br>
                Telefon: +49 (0) 681 123 456<br>
                E-Mail: info@yla-umzug.de<br>
                Web: www.yla-umzug.de<br><br>
                
                <strong>Geschäftsführer:</strong> [Name]<br>
                <strong>Adresse:</strong> [Straße], [PLZ] [Stadt]<br>
                <strong>USt-IdNr.:</strong> [Umsatzsteuer-ID]
            </div>
        </div>
        
        <div class="quote-info">
            <div class="quote-number">Angebot {{ $quote->quote_number }}</div>
            <div class="quote-date">
                Erstellt am: {{ now()->format('d.m.Y') }}<br>
                Gültig bis: {{ now()->addDays(30)->format('d.m.Y') }}
            </div>
        </div>
    </div>

    <div class="customer-section">
        <h2 class="section-title">Kundeninformationen</h2>
        <div class="customer-info">
            <div class="info-row">
                <span class="info-label">Name:</span>
                {{ $quote->name }}
            </div>
            <div class="info-row">
                <span class="info-label">E-Mail:</span>
                {{ $quote->email }}
            </div>
            <div class="info-row">
                <span class="info-label">Telefon:</span>
                {{ $quote->phone }}
            </div>
            @if($quote->preferred_date)
            <div class="info-row">
                <span class="info-label">Wunschtermin:</span>
                {{ $quote->preferred_date->format('d.m.Y') }}
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Kontakt bevorzugt:</span>
                {{ $quote->preferred_contact_formatted }}
            </div>
        </div>
    </div>

    <div class="services-section">
        <h2 class="section-title">Gewünschte Leistungen</h2>
        
        @if(in_array('umzug', $quote->selected_services))
            @php 
                $movingDetails = $quote->service_details['movingDetails'] ?? [];
                $formatAddress = function($address) {
                    if (empty($address)) return 'Nicht angegeben';
                    $parts = array_filter([
                        $address['street'] ?? '',
                        $address['postalCode'] ?? '',
                        $address['city'] ?? ''
                    ]);
                    return implode(', ', $parts) ?: 'Nicht angegeben';
                };
                $formatAdditionalServices = function($services) {
                    if (empty($services)) return 'Keine';
                    $serviceNames = [
                        'assembly' => 'Möbelabbau & Aufbau',
                        'packing' => 'Verpackungsservice',
                        'parking' => 'Halteverbotszone',
                        'storage' => 'Einlagerung',
                        'disposal' => 'Entsorgung'
                    ];
                    $formatted = array_map(function($service) use ($serviceNames) {
                        return $serviceNames[$service] ?? $service;
                    }, $services);
                    return implode(', ', $formatted);
                };
            @endphp
            <div class="service-item">
                <div class="service-title">🚚 Umzugsservice</div>
                <div class="service-details">
                    <div class="detail-item"><strong>Von:</strong> {{ $formatAddress($movingDetails['fromAddress'] ?? []) }}</div>
                    <div class="detail-item"><strong>Nach:</strong> {{ $formatAddress($movingDetails['toAddress'] ?? []) }}</div>
                    <div class="detail-item"><strong>Wohnungsgröße:</strong> {{ ($movingDetails['apartmentSize'] ?? '') }} m²</div>
                    <div class="detail-item"><strong>Zimmeranzahl:</strong> {{ $movingDetails['rooms'] ?? 'Nicht angegeben' }}</div>
                    @if(!empty($movingDetails['boxes']))
                        <div class="detail-item"><strong>Geschätzte Kartons:</strong> {{ $movingDetails['boxes'] }}</div>
                    @endif
                    @if(!empty($movingDetails['additionalServices']))
                        <div class="detail-item"><strong>Zusatzleistungen:</strong> {{ $formatAdditionalServices($movingDetails['additionalServices']) }}</div>
                    @endif
                    @if(!empty($movingDetails['specialItems']))
                        <div class="detail-item"><strong>Besondere Gegenstände:</strong> {{ $movingDetails['specialItems'] }}</div>
                    @endif
                </div>
            </div>
        @endif

        @if(in_array('entruempelung', $quote->selected_services))
            @php 
                $declutterDetails = $quote->service_details['declutterDetails'] ?? [];
                $formatAddress = function($address) {
                    if (empty($address)) return 'Nicht angegeben';
                    $parts = array_filter([
                        $address['street'] ?? '',
                        $address['postalCode'] ?? '',
                        $address['city'] ?? ''
                    ]);
                    return implode(', ', $parts) ?: 'Nicht angegeben';
                };
                $translateObjectType = function($type) {
                    $types = [
                        'apartment' => 'Wohnung',
                        'house' => 'Haus',
                        'basement' => 'Keller',
                        'garage' => 'Garage',
                        'office' => 'Büro',
                        'attic' => 'Dachboden'
                    ];
                    return $types[$type] ?? $type;
                };
                $translateVolume = function($volume) {
                    $volumes = [
                        'low' => 'Wenig (1-2 Container)',
                        'medium' => 'Mittel (3-5 Container)',
                        'high' => 'Viel (6+ Container)',
                        'extreme' => 'Sehr viel (Messi-Haushalt)'
                    ];
                    return $volumes[$volume] ?? $volume;
                };
                $formatWasteTypes = function($wasteTypes) {
                    if (empty($wasteTypes)) return 'Nicht angegeben';
                    $typeNames = [
                        'furniture' => 'Sperrmüll',
                        'electronics' => 'Elektrogeräte',
                        'hazardous' => 'Sondermüll',
                        'household' => 'Hausrat',
                        'construction' => 'Bauschutt'
                    ];
                    $formatted = array_map(function($type) use ($typeNames) {
                        return $typeNames[$type] ?? $type;
                    }, $wasteTypes);
                    return implode(', ', $formatted);
                };
            @endphp
            <div class="service-item">
                <div class="service-title">🗑️ Entrümpelung</div>
                <div class="service-details">
                    <div class="detail-item"><strong>Adresse:</strong> {{ $formatAddress($declutterDetails['address'] ?? []) }}</div>
                    <div class="detail-item"><strong>Objektart:</strong> {{ $translateObjectType($declutterDetails['objectType'] ?? '') }}</div>
                    <div class="detail-item"><strong>Größe:</strong> {{ ($declutterDetails['size'] ?? '') }} m²</div>
                    <div class="detail-item"><strong>Volumen:</strong> {{ $translateVolume($declutterDetails['volume'] ?? '') }}</div>
                    @if(!empty($declutterDetails['wasteTypes']))
                        <div class="detail-item"><strong>Müllarten:</strong> {{ $formatWasteTypes($declutterDetails['wasteTypes']) }}</div>
                    @endif
                    <div class="detail-item"><strong>Besenreine Übergabe:</strong> {{ ($declutterDetails['cleanHandover'] ?? '') === 'yes' ? 'Ja' : 'Nein' }}</div>
                    @if(!empty($declutterDetails['additionalInfo']))
                        <div class="detail-item"><strong>Zusätzliche Informationen:</strong> {{ $declutterDetails['additionalInfo'] }}</div>
                    @endif
                </div>
            </div>
        @endif

        @if(in_array('putzservice', $quote->selected_services))
            @php 
                $cleaningDetails = $quote->service_details['cleaningDetails'] ?? [];
                $translateObjectType = function($type) {
                    $types = [
                        'apartment' => 'Wohnung',
                        'house' => 'Haus',
                        'basement' => 'Keller',
                        'garage' => 'Garage',
                        'office' => 'Büro',
                        'attic' => 'Dachboden'
                    ];
                    return $types[$type] ?? $type;
                };
                $translateCleaningIntensity = function($intensity) {
                    $intensities = [
                        'normal' => 'Normalreinigung',
                        'deep' => 'Grundreinigung',
                        'construction' => 'Bauschlussreinigung'
                    ];
                    return $intensities[$intensity] ?? $intensity;
                };
                $formatRooms = function($rooms) {
                    if (empty($rooms)) return 'Nicht angegeben';
                    $roomNames = [
                        'kitchen' => 'Küche',
                        'bathroom' => 'Badezimmer/WC',
                        'livingRooms' => 'Wohnräume',
                        'windows' => 'Fensterreinigung'
                    ];
                    $formatted = array_map(function($room) use ($roomNames) {
                        return $roomNames[$room] ?? $room;
                    }, $rooms);
                    return implode(', ', $formatted);
                };
                $translateFrequency = function($frequency) {
                    $frequencies = [
                        'once' => 'Einmalig',
                        'weekly' => 'Wöchentlich',
                        'biweekly' => '14-tägig',
                        'monthly' => 'Monatlich'
                    ];
                    return $frequencies[$frequency] ?? $frequency;
                };
                $translateKeyHandover = function($keyHandover) {
                    $options = [
                        'present' => 'Ich bin vor Ort',
                        'key' => 'Schlüsselübergabe nötig'
                    ];
                    return $options[$keyHandover] ?? 'Nicht angegeben';
                };
            @endphp
            <div class="service-item">
                <div class="service-title">🧽 Putzservice</div>
                <div class="service-details">
                    <div class="detail-item"><strong>Objektart:</strong> {{ $translateObjectType($cleaningDetails['objectType'] ?? '') }}</div>
                    <div class="detail-item"><strong>Größe:</strong> {{ ($cleaningDetails['size'] ?? '') }} m²</div>
                    <div class="detail-item"><strong>Reinigungsintensität:</strong> {{ $translateCleaningIntensity($cleaningDetails['cleaningIntensity'] ?? '') }}</div>
                    @if(!empty($cleaningDetails['rooms']))
                        <div class="detail-item"><strong>Bereiche:</strong> {{ $formatRooms($cleaningDetails['rooms']) }}</div>
                    @endif
                    <div class="detail-item"><strong>Häufigkeit:</strong> {{ $translateFrequency($cleaningDetails['frequency'] ?? '') }}</div>
                    <div class="detail-item"><strong>Schlüsselübergabe:</strong> {{ $translateKeyHandover($cleaningDetails['keyHandover'] ?? '') }}</div>
                </div>
            </div>
        @endif
    </div>

    @if($quote->message)
    <div class="services-section">
        <h2 class="section-title">Zusätzliche Anfrage</h2>
        <div class="customer-info">
            {{ $quote->message }}
        </div>
    </div>
    @endif

    <div class="pricing-section">
        <h2 class="section-title">Kostenvoranschlag</h2>
        
        @if($quote->pricing_data)
            <table class="pricing-table">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Beschreibung</th>
                        <th style="text-align: right;">Betrag</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($quote->pricing_data['umzug']))
                        <tr>
                            <td>Umzugsservice</td>
                            <td>
                                Grundpreis + Entfernung ({{ $quote->pricing_data['umzug']['distance'] ?? 0 }} km)<br>
                                <small>Inkl. Verpackung, Transport und Aufbau</small>
                            </td>
                            <td class="amount">{{ number_format($quote->pricing_data['umzug']['total'] ?? 0, 2, ',', '.') }} €</td>
                        </tr>
                    @endif
                    
                    @if(isset($quote->pricing_data['entruempelung']))
                        <tr>
                            <td>Entrümpelung</td>
                            <td>
                                Volumenbasierte Berechnung<br>
                                <small>Inkl. Entsorgung und Reinigung</small>
                            </td>
                            <td class="amount">{{ number_format($quote->pricing_data['entruempelung']['total'] ?? 0, 2, ',', '.') }} €</td>
                        </tr>
                    @endif
                    
                    @if(isset($quote->pricing_data['putzservice']))
                        <tr>
                            <td>Putzservice</td>
                            <td>
                                Flächenbasierte Berechnung<br>
                                <small>Professionelle Reinigung</small>
                            </td>
                            <td class="amount">{{ number_format($quote->pricing_data['putzservice']['total'] ?? 0, 2, ',', '.') }} €</td>
                        </tr>
                    @endif
                    
                    @if(isset($quote->pricing_data['discount']) && $quote->pricing_data['discount'] > 0)
                        <tr>
                            <td>Kombinationsrabatt</td>
                            <td>Mehrere Services gebucht</td>
                            <td class="amount">-{{ number_format($quote->pricing_data['discount'], 2, ',', '.') }} €</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2"><strong>Gesamtbetrag (inkl. 19% MwSt.)</strong></td>
                        <td class="amount"><strong>{{ number_format($quote->pricing_data['total'] ?? 0, 2, ',', '.') }} €</strong></td>
                    </tr>
                </tfoot>
            </table>
        @endif

        @if($quote->final_quote_amount && $quote->final_quote_amount != ($quote->pricing_data['total'] ?? 0))
            <div class="highlight">
                <strong>Finales Angebot nach Besichtigung:</strong><br>
                Nach einer detaillierten Besichtigung vor Ort können wir Ihnen ein finales Angebot von 
                <strong>{{ number_format($quote->final_quote_amount, 2, ',', '.') }} €</strong> unterbreiten.
            </div>
        @endif
    </div>

    <div class="contact-cta">
        <h3>Bereit für Ihren Umzug?</h3>
        <p>Kontaktieren Sie uns für eine unverbindliche Beratung oder um einen Besichtigungstermin zu vereinbaren.</p>
        <p><strong>Telefon:</strong> +49 (0) 681 123 456 | <strong>E-Mail:</strong> info@yla-umzug.de</p>
    </div>

    <div class="terms-section">
        <div class="terms-title">Allgemeine Geschäftsbedingungen</div>
        <p>
            <strong>Gültigkeit:</strong> Dieses Angebot ist 30 Tage ab Ausstellungsdatum gültig.<br>
            <strong>Zahlungsbedingungen:</strong> 50% Anzahlung bei Auftragserteilung, Restzahlung nach Abschluss der Arbeiten.<br>
            <strong>Leistungsumfang:</strong> Die Leistungen werden gemäß den vereinbarten Spezifikationen erbracht.<br>
            <strong>Haftung:</strong> Wir sind vollversichert und haften für Schäden gemäß unseren AGB.<br>
            <strong>Stornierung:</strong> Kostenlose Stornierung bis 48 Stunden vor dem Termin möglich.
        </p>
        
        <p>
            <strong>Zusätzliche Kosten:</strong> Eventuelle Zusatzkosten (z.B. für besondere Gegenstände, Parkgebühren, 
            oder zusätzliche Fahrten) werden separat berechnet und vorher mit Ihnen abgestimmt.
        </p>
        
        <p>
            <strong>Entsorgung:</strong> Die fachgerechte Entsorgung erfolgt gemäß den örtlichen Bestimmungen. 
            Sondermüll wird nach Aufwand separat berechnet.
        </p>
        
        <p>
            Es gelten unsere vollständigen Allgemeinen Geschäftsbedingungen, die Sie auf unserer Website 
            www.yla-umzug.de einsehen können.
        </p>
    </div>

    <div class="footer">
        YLA Umzug • Saarbrücken • Telefon: +49 (0) 681 123 456 • E-Mail: info@yla-umzug.de<br>
        Geschäftsführer: [Name] • USt-IdNr.: [Umsatzsteuer-ID] • Erstellt am {{ now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>