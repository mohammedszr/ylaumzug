import React from 'react';
import { Helmet } from 'react-helmet-async';

const EntruempelungRegional = () => {
  const regions = {
    saarland: [
      {
        city: 'Saarbrücken',
        description: 'Entrümpelung und Haushaltsauflösung in der Landeshauptstadt des Saarlandes. Schnelle Anfahrt, faire Preise.',
        districts: ['Alt-Saarbrücken', 'St. Johann', 'Dudweiler'],
        specialty: 'Wohnungsentrümpelung',
        travelTime: '30-45 Minuten'
      },
      {
        city: 'St. Ingbert',
        description: 'Professionelle Entrümpelung in St. Ingbert und Umgebung. Erfahrenes Team für alle Arten von Räumungen.',
        districts: ['Mitte', 'Rohrbach', 'Oberwürzbach'],
        specialty: 'Kellerentrümpelung',
        travelTime: '25-35 Minuten'
      },
      {
        city: 'Homburg',
        description: 'Zuverlässige Entrümpelung in Homburg. Von der Wohnung bis zum kompletten Haus - wir räumen alles.',
        districts: ['Homburg-Mitte', 'Erbach', 'Schwarzenacker'],
        specialty: 'Dachbodenentrümpelung',
        travelTime: '20-30 Minuten'
      },
      {
        city: 'Völklingen',
        description: 'Entrümpelung in Völklingen mit Fokus auf Nachhaltigkeit und umweltgerechte Entsorgung.',
        districts: ['Völklingen-Mitte', 'Wehrden', 'Ludweiler'],
        specialty: 'Gewerbeobjekte',
        travelTime: '35-45 Minuten'
      },
      {
        city: 'Neunkirchen',
        description: 'Schnelle Entrümpelung in Neunkirchen. Auch kurzfristige Termine und Notfälle möglich.',
        districts: ['Neunkirchen-Mitte', 'Wellesweiler'],
        specialty: 'Schnellräumung',
        travelTime: '40-50 Minuten'
      },
      {
        city: 'Merzig',
        description: 'Grenznahe Entrümpelung in Merzig. Auch für Kunden aus Luxemburg und Frankreich erreichbar.',
        districts: ['Merzig-Mitte', 'Brotdorf', 'Hilbringen'],
        specialty: 'Grenzregion',
        travelTime: '50-60 Minuten'
      }
    ],
    rheinlandPfalz: [
      {
        city: 'Kaiserslautern',
        description: 'Unser Hauptsitz in Kaiserslautern. Hier sind wir besonders schnell und flexibel für Sie da.',
        districts: ['Alle Stadtteile abgedeckt'],
        specialty: 'Alle Services',
        travelTime: 'Sofort verfügbar'
      },
      {
        city: 'Trier',
        description: 'Entrümpelung in der ältesten Stadt Deutschlands. Besondere Erfahrung mit historischen Gebäuden.',
        districts: ['Trier-Mitte', 'Ehrang', 'Kürenz'],
        specialty: 'Historische Gebäude',
        travelTime: '45-60 Minuten'
      },
      {
        city: 'Mainz',
        description: 'Entrümpelung in der Landeshauptstadt von Rheinland-Pfalz. Professionell und diskret.',
        districts: ['Altstadt', 'Neustadt', 'Gonsenheim'],
        specialty: 'Stadtwohnungen',
        travelTime: '60-75 Minuten'
      },
      {
        city: 'Koblenz',
        description: 'Entrümpelung am Deutschen Eck. Auch für schwer zugängliche Objekte in der Altstadt.',
        districts: ['Altstadt', 'Ehrenbreitstein', 'Lützel'],
        specialty: 'Altstadtobjekte',
        travelTime: '70-85 Minuten'
      },
      {
        city: 'Ludwigshafen',
        description: 'Industriestadt-Entrümpelung in Ludwigshafen. Auch Gewerbe- und Industrieobjekte.',
        districts: ['Mitte', 'Süd', 'Oggersheim'],
        specialty: 'Gewerbeobjekte',
        travelTime: '75-90 Minuten'
      },
      {
        city: 'Speyer',
        description: 'Entrümpelung in der Domstadt Speyer. Respektvoller Umgang mit historischem Ambiente.',
        districts: ['Altstadt', 'West', 'Nord'],
        specialty: 'Denkmalschutz',
        travelTime: '80-95 Minuten'
      }
    ]
  };

  const services = [
    {
      title: 'Komplette Entrümpelung',
      description: 'Vollständige Räumung von Wohnungen, Häusern, Kellern und Dachböden mit fachgerechter Entsorgung.',
      icon: '🏠'
    },
    {
      title: 'Haushaltsauflösung',
      description: 'Professionelle Auflösung kompletter Haushalte mit Wertanrechnung und Verkauf verwertbarer Gegenstände.',
      icon: '📦'
    },
    {
      title: 'Messi-Syndrom Hilfe',
      description: 'Diskrete und einfühlsame Hilfe bei Messie-Wohnungen mit psychologischem Verständnis.',
      icon: '🤝'
    }
  ];

  const structuredData = {
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "YLA Umzug - Entrümpelung Saarland & Rheinland-Pfalz",
    "description": "Professionelle Entrümpelung, Haushaltsauflösung und Umzüge in Saarland und Rheinland-Pfalz. Schnell, zuverlässig und fair.",
    "url": "https://yla-umzug.de/entrümpelung-regional",
    "telephone": "+4963418959162",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Kaiserslautern",
      "addressRegion": "Rheinland-Pfalz",
      "addressCountry": "DE"
    },
    "areaServed": [
      "Saarbrücken", "St. Ingbert", "Homburg", "Völklingen", "Neunkirchen", "Merzig",
      "Kaiserslautern", "Trier", "Mainz", "Koblenz", "Ludwigshafen", "Speyer"
    ],
    "serviceType": ["Entrümpelung", "Haushaltsauflösung", "Umzug", "Messi-Wohnung Räumung"],
    "priceRange": "€€"
  };

  return (
    <>
      <Helmet>
        <title>Entrümpelung Saarland & Rheinland-Pfalz | YLA Umzug - Professionell & Günstig</title>
        <meta name="description" content="Professionelle Entrümpelung in Saarbrücken, Kaiserslautern, Trier & allen Städten in Saarland & Rheinland-Pfalz. ✓ Faire Preise ✓ Schnelle Termine ✓ Wertanrechnung" />
        <meta name="keywords" content="Entrümpelung Saarbrücken, Entrümpelung Kaiserslautern, Haushaltsauflösung Saarland, Entrümpelung Trier, Messi Wohnung Hilfe" />
        <link rel="canonical" href="https://yla-umzug.de/entrümpelung-regional" />
        <script type="application/ld+json">
          {JSON.stringify(structuredData)}
        </script>
      </Helmet>

      <div className="entrümpelung-regional">
        {/* Hero Section */}
        <section className="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
          <div className="container mx-auto px-4">
            <div className="max-w-4xl mx-auto text-center">
              <h1 className="text-4xl md:text-5xl font-bold mb-6">
                Professionelle Entrümpelung in Saarland & Rheinland-Pfalz
              </h1>
              <p className="text-xl md:text-2xl mb-8 opacity-90">
                Ihr zuverlässiger Partner für Entrümpelung, Haushaltsauflösung und Umzüge in der Region
              </p>
              <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <a 
                  href="tel:+4963418959162" 
                  className="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors"
                >
                  Sofort anrufen: 06341 895 91 62
                </a>
                <a 
                  href="https://wa.me/4963418959162?text=Hallo! Ich benötige einen Kostenvoranschlag für eine Entrümpelung..." 
                  className="bg-green-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-600 transition-colors"
                >
                  WhatsApp Beratung
                </a>
              </div>
            </div>
          </div>
        </section>

        {/* Breadcrumb */}
        <nav className="bg-gray-50 py-3" aria-label="Breadcrumb">
          <div className="container mx-auto px-4">
            <ol className="flex items-center space-x-2 text-sm">
              <li><a href="/" className="text-blue-600 hover:underline">Startseite</a></li>
              <li className="text-gray-500">/</li>
              <li className="text-gray-900">Entrümpelung Regional</li>
            </ol>
          </div>
        </nav>

        {/* Service Overview */}
        <section className="py-16 bg-white">
          <div className="container mx-auto px-4">
            <div className="max-w-6xl mx-auto">
              <h2 className="text-3xl font-bold text-center mb-12">Unsere Entrümpelung-Services in der Region</h2>
              
              <div className="grid md:grid-cols-3 gap-8 mb-16">
                {services.map((service, index) => (
                  <div key={index} className="text-center p-6 bg-gray-50 rounded-lg">
                    <div className="text-4xl mb-4">{service.icon}</div>
                    <h3 className="text-xl font-semibold mb-3">{service.title}</h3>
                    <p className="text-gray-600">{service.description}</p>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </section>        {/* 
Regional Coverage */}
        <section className="py-16 bg-gray-50">
          <div className="container mx-auto px-4">
            <div className="max-w-6xl mx-auto">
              <h2 className="text-3xl font-bold text-center mb-12">Unsere Servicegebiete in Saarland & Rheinland-Pfalz</h2>
              
              {/* Saarland Section */}
              <div className="mb-12">
                <h3 className="text-2xl font-semibold mb-8 text-blue-600">🏛️ Saarland - Unser Fokusgebiet</h3>
                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {regions.saarland.map((region, index) => (
                    <div key={index} className="bg-white p-6 rounded-lg shadow-sm">
                      <h4 className="text-xl font-semibold mb-4 text-blue-600">{region.city}</h4>
                      <p className="text-gray-600 mb-4">{region.description}</p>
                      <ul className="text-sm text-gray-500 space-y-1">
                        <li>• Stadtteile: {region.districts.join(', ')}</li>
                        <li>• Spezialisierung: {region.specialty}</li>
                        <li>• Anfahrtszeit: {region.travelTime}</li>
                      </ul>
                    </div>
                  ))}
                </div>
              </div>

              {/* Rheinland-Pfalz Section */}
              <div>
                <h3 className="text-2xl font-semibold mb-8 text-green-600">🌲 Rheinland-Pfalz - Erweiterte Abdeckung</h3>
                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {regions.rheinlandPfalz.map((region, index) => (
                    <div key={index} className="bg-white p-6 rounded-lg shadow-sm">
                      <h4 className="text-xl font-semibold mb-4 text-green-600">{region.city}</h4>
                      <p className="text-gray-600 mb-4">{region.description}</p>
                      <ul className="text-sm text-gray-500 space-y-1">
                        <li>• Stadtteile: {region.districts.join(', ')}</li>
                        <li>• Spezialisierung: {region.specialty}</li>
                        <li>• Anfahrtszeit: {region.travelTime}</li>
                      </ul>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Why Choose Us */}
        <section className="py-16 bg-white">
          <div className="container mx-auto px-4">
            <div className="max-w-4xl mx-auto">
              <h2 className="text-3xl font-bold text-center mb-12">Warum YLA Umzug für Ihre Entrümpelung wählen?</h2>
              
              <div className="grid md:grid-cols-2 gap-8">
                <div className="space-y-6">
                  <div className="flex items-start space-x-4">
                    <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                      <span className="text-blue-600 font-bold">✓</span>
                    </div>
                    <div>
                      <h3 className="font-semibold mb-2">Regionale Expertise</h3>
                      <p className="text-gray-600">Über 10 Jahre Erfahrung in Saarland und Rheinland-Pfalz. Wir kennen die lokalen Gegebenheiten und Entsorgungsmöglichkeiten.</p>
                    </div>
                  </div>

                  <div className="flex items-start space-x-4">
                    <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                      <span className="text-blue-600 font-bold">✓</span>
                    </div>
                    <div>
                      <h3 className="font-semibold mb-2">Faire Preisgestaltung</h3>
                      <p className="text-gray-600">Transparente Kostenvoranschläge ohne versteckte Gebühren. Wertanrechnung für verwertbare Gegenstände reduziert Ihre Kosten.</p>
                    </div>
                  </div>

                  <div className="flex items-start space-x-4">
                    <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                      <span className="text-blue-600 font-bold">✓</span>
                    </div>
                    <div>
                      <h3 className="font-semibold mb-2">Schnelle Verfügbarkeit</h3>
                      <p className="text-gray-600">Kurzfristige Termine möglich. In Notfällen sind wir oft schon am nächsten Tag vor Ort.</p>
                    </div>
                  </div>
                </div>

                <div className="space-y-6">
                  <div className="flex items-start space-x-4">
                    <div className="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                      <span className="text-green-600 font-bold">✓</span>
                    </div>
                    <div>
                      <h3 className="font-semibold mb-2">Umweltgerechte Entsorgung</h3>
                      <p className="text-gray-600">Fachgerechte Trennung und Entsorgung aller Materialien. Recycling und Spenden wo immer möglich.</p>
                    </div>
                  </div>

                  <div className="flex items-start space-x-4">
                    <div className="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                      <span className="text-green-600 font-bold">✓</span>
                    </div>
                    <div>
                      <h3 className="font-semibold mb-2">Diskrete Abwicklung</h3>
                      <p className="text-gray-600">Besonders bei sensiblen Fällen wie Messi-Wohnungen arbeiten wir diskret und einfühlsam.</p>
                    </div>
                  </div>

                  <div className="flex items-start space-x-4">
                    <div className="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                      <span className="text-green-600 font-bold">✓</span>
                    </div>
                    <div>
                      <h3 className="font-semibold mb-2">Vollservice-Paket</h3>
                      <p className="text-gray-600">Von der Beratung über die Räumung bis zur Endreinigung - alles aus einer Hand.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* FAQ Section */}
        <section className="py-16 bg-gray-50">
          <div className="container mx-auto px-4">
            <div className="max-w-4xl mx-auto">
              <h2 className="text-3xl font-bold text-center mb-12">Häufig gestellte Fragen zur Entrümpelung</h2>
              
              <div className="space-y-6">
                <div className="bg-white p-6 rounded-lg shadow-sm">
                  <h3 className="font-semibold mb-3">Was kostet eine Entrümpelung in Saarbrücken oder Kaiserslautern?</h3>
                  <p className="text-gray-600">Die Kosten hängen von der Größe des Objekts, der Menge des Inventars und der Zugänglichkeit ab. Eine 3-Zimmer-Wohnung kostet durchschnittlich 800-1.500€. Wertgegenstände werden angerechnet und reduzieren die Kosten erheblich.</p>
                </div>

                <div className="bg-white p-6 rounded-lg shadow-sm">
                  <h3 className="font-semibold mb-3">Wie schnell können Sie eine Entrümpelung durchführen?</h3>
                  <p className="text-gray-600">In der Regel können wir innerhalb von 3-7 Tagen einen Termin anbieten. Bei Notfällen oder dringenden Fällen sind wir oft schon am nächsten Tag verfügbar. Rufen Sie uns einfach an!</p>
                </div>

                <div className="bg-white p-6 rounded-lg shadow-sm">
                  <h3 className="font-semibold mb-3">Fahren Sie auch nach Trier, Mainz oder andere entferntere Städte?</h3>
                  <p className="text-gray-600">Ja, wir decken ganz Saarland und Rheinland-Pfalz ab. Für entferntere Städte wie Mainz oder Koblenz berechnen wir eine faire Anfahrtspauschale, die bei größeren Aufträgen oft entfällt.</p>
                </div>

                <div className="bg-white p-6 rounded-lg shadow-sm">
                  <h3 className="font-semibold mb-3">Helfen Sie auch bei Messi-Wohnungen diskret?</h3>
                  <p className="text-gray-600">Absolut. Wir haben viel Erfahrung mit sensiblen Situationen und arbeiten immer diskret und einfühlsam. Unser Team ist geschult im Umgang mit besonderen Herausforderungen.</p>
                </div>

                <div className="bg-white p-6 rounded-lg shadow-sm">
                  <h3 className="font-semibel mb-3">Übernimmt das Jobcenter die Kosten für eine Entrümpelung?</h3>
                  <p className="text-gray-600">Bei Bürgergeld-Empfängern können die Kosten unter bestimmten Umständen übernommen werden. Wir helfen Ihnen gerne bei der Antragstellung und erstellen die nötigen Kostenvoranschläge.</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* CTA Section */}
        <section className="py-16 bg-blue-600 text-white">
          <div className="container mx-auto px-4">
            <div className="max-w-4xl mx-auto text-center">
              <h2 className="text-3xl font-bold mb-6">Bereit für Ihre Entrümpelung?</h2>
              <p className="text-xl mb-8 opacity-90">
                Kontaktieren Sie uns noch heute für einen kostenlosen Kostenvoranschlag. 
                Wir sind in ganz Saarland und Rheinland-Pfalz für Sie da!
              </p>
              
              <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <a 
                  href="tel:+4963418959162" 
                  className="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors text-lg"
                >
                  📞 Jetzt anrufen: 06341 895 91 62
                </a>
                <a 
                  href="https://wa.me/4963418959162?text=Hallo! Ich benötige einen Kostenvoranschlag für eine Entrümpelung in [Ihre Stadt]. Können Sie mir helfen?" 
                  className="bg-green-500 text-white px-8 py-4 rounded-lg font-semibold hover:bg-green-600 transition-colors text-lg"
                >
                  💬 WhatsApp Beratung
                </a>
              </div>

              <div className="mt-8 text-sm opacity-75">
                <p>Kostenlose Beratung • Faire Preise • Schnelle Termine • Diskrete Abwicklung</p>
              </div>
            </div>
          </div>
        </section>
      </div>
    </>
  );
};

export default EntruempelungRegional;