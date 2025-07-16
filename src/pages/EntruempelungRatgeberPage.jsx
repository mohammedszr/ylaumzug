import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { CheckCircle, Clock, Users, Trash2, ArrowRight, MapPin } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { NavLink } from 'react-router-dom';
import Breadcrumbs from '@/components/ui/breadcrumbs';

const pageVariants = {
  initial: { opacity: 0, x: -100 },
  in: { opacity: 1, x: 0 },
  out: { opacity: 0, x: 100 },
};

const pageTransition = {
  type: 'tween',
  ease: 'anticipate',
  duration: 0.5,
};

const steps = [
  {
    number: 1,
    title: "Planen & Ziele setzen",
    description: "Termin festlegen, Budget kalkulieren und realistische Ziele definieren",
    details: "Bestimmen Sie den Umfang der Entrümpelung und setzen Sie sich ein realistisches Budget. Planen Sie genügend Zeit ein - eine gründliche Entrümpelung dauert meist länger als erwartet."
  },
  {
    number: 2,
    title: "Sortieren nach System",
    description: "Kategorien: Behalten, Verkaufen, Spenden, Entsorgen",
    details: "Arbeiten Sie systematisch Raum für Raum. Verwenden Sie vier Kisten oder Bereiche für die verschiedenen Kategorien. Seien Sie ehrlich bei der Bewertung - was haben Sie das letzte Jahr wirklich genutzt?"
  },
  {
    number: 3,
    title: "Helfer organisieren",
    description: "Freunde einladen oder Profi-Team von YLA Umzug buchen",
    details: "Entrümpelung ist Teamarbeit. Organisieren Sie Helfer oder beauftragen Sie unser erfahrenes Team in Saarbrücken, Trier oder Kaiserslautern für eine professionelle und stressfreie Abwicklung."
  },
  {
    number: 4,
    title: "Profi-Entsorgung beauftragen",
    description: "Sperrmüll, Sondermüll und Grüngut getrennt abholen lassen",
    details: "Wir kümmern uns um die fachgerechte Entsorgung aller Materialien. Elektroschrott, Sperrmüll und Sondermüll werden umweltgerecht entsorgt - Sie müssen sich um nichts kümmern."
  },
  {
    number: 5,
    title: "Neu einrichten & genießen",
    description: "Möbel arrangieren und Endreinigung durchführen",
    details: "Nach der Entrümpelung folgt die Endreinigung. Arrangieren Sie Ihre verbliebenen Möbel neu und genießen Sie Ihren aufgeräumten, neuen Lebensraum."
  }
];

const regions = [
  "Saarbrücken", "Trier", "Kaiserslautern", "Mainz", "Koblenz", "Ludwigshafen"
];

const EntruempelungRatgeberPage = () => {
  return (
    <motion.div
      initial="initial"
      animate="in"
      exit="out"
      variants={pageVariants}
      transition={pageTransition}
      className="container mx-auto px-6 py-16"
    >
      <Helmet>
        <title>Entrümpelung in 5 Schritten - Ratgeber für Saarbrücken & Trier | YLA Umzug</title>
        <meta name="description" content="Entrümpelung in 5 Schritten: Professioneller Ratgeber für Saarbrücken, Trier & Kaiserslautern. Kosten sparen mit Wertanrechnung. Jetzt kostenlose Beratung!" />
        <meta name="keywords" content="Entrümpelung Saarbrücken, Entrümpelung Trier, Haushaltsauflösung Kaiserslautern, Sperrmüll entsorgen, Kellerentrümpelung, Messi-Wohnung aufräumen" />
        <link rel="canonical" href="/ratgeber-entruempelung-5-schritte" />
        <script type="application/ld+json">
          {JSON.stringify({
            "@context": "https://schema.org",
            "@type": "HowTo",
            "name": "Entrümpelung in 5 Schritten",
            "description": "Professionelle Anleitung für eine erfolgreiche Entrümpelung in Saarbrücken, Trier und Kaiserslautern",
            "image": "https://yla-umzug.de/images/entruempelung-5-schritte.jpg",
            "totalTime": "P1D",
            "estimatedCost": {
              "@type": "MonetaryAmount",
              "currency": "EUR",
              "value": "200-800"
            },
            "supply": [
              {
                "@type": "HowToSupply",
                "name": "Umzugskartons"
              },
              {
                "@type": "HowToSupply", 
                "name": "Müllsäcke"
              }
            ],
            "step": [
              {
                "@type": "HowToStep",
                "name": "Planen & Ziele setzen",
                "text": "Termin festlegen, Budget kalkulieren und realistische Ziele definieren"
              },
              {
                "@type": "HowToStep",
                "name": "Sortieren nach System",
                "text": "Kategorien: Behalten, Verkaufen, Spenden, Entsorgen"
              },
              {
                "@type": "HowToStep",
                "name": "Helfer organisieren",
                "text": "Freunde einladen oder Profi-Team von YLA Umzug buchen"
              },
              {
                "@type": "HowToStep",
                "name": "Profi-Entsorgung beauftragen",
                "text": "Sperrmüll, Sondermüll und Grüngut getrennt abholen lassen"
              },
              {
                "@type": "HowToStep",
                "name": "Neu einrichten & genießen",
                "text": "Möbel arrangieren und Endreinigung durchführen"
              }
            ]
          })}
        </script>
      </Helmet>

      {/* Breadcrumbs */}
      <Breadcrumbs items={[
        { name: "Ratgeber", url: "/ratgeber" },
        { name: "Entrümpelung in 5 Schritten" }
      ]} />

      {/* Hero Section */}
      <div className="text-center mb-16">
        <h1 className="text-4xl md:text-5xl font-extrabold text-white mb-6">
          Entrümpelung in 5 Schritten: Ihr ultimativer Ratgeber
        </h1>
        <p className="text-xl text-violet-200 max-w-4xl mx-auto mb-8">
          Sie planen eine Entrümpelung in <strong>Saarbrücken, Trier oder Kaiserslautern</strong>? 
          Unser Experten-Guide zeigt Ihnen, wie Sie systematisch vorgehen und dabei noch Geld sparen können.
        </p>
        <div className="flex flex-wrap justify-center gap-2 mb-8">
          {regions.map((region) => (
            <span key={region} className="bg-violet-600/20 text-violet-300 px-3 py-1 rounded-full text-sm flex items-center">
              <MapPin size={14} className="mr-1" />
              {region}
            </span>
          ))}
        </div>
      </div>

      {/* 5-Schritte Anleitung */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Die 5 Schritte zur erfolgreichen Entrümpelung</h2>
        <div className="space-y-8">
          {steps.map((step, index) => (
            <motion.div
              key={step.number}
              initial={{ opacity: 0, x: -50 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true, amount: 0.3 }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className="bg-gray-800/60 p-8 rounded-xl shadow-lg"
            >
              <div className="flex items-start space-x-6">
                <div className="flex-shrink-0 bg-violet-600 text-white w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold">
                  {step.number}
                </div>
                <div className="flex-1">
                  <h3 className="text-2xl font-bold text-white mb-2">{step.title}</h3>
                  <p className="text-violet-300 text-lg mb-4">{step.description}</p>
                  <p className="text-gray-300">{step.details}</p>
                </div>
              </div>
            </motion.div>
          ))}
        </div>
      </div>

      {/* Kosten sparen Section */}
      <motion.div
        initial={{ opacity: 0, y: 50 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, amount: 0.3 }}
        transition={{ duration: 0.5 }}
        className="bg-gradient-to-r from-violet-600/20 to-purple-600/20 p-8 rounded-xl mb-16"
      >
        <h2 className="text-3xl font-bold text-white mb-6">💰 Geld sparen mit Wertanrechnung</h2>
        <div className="grid md:grid-cols-2 gap-8">
          <div>
            <h3 className="text-xl font-semibold text-violet-300 mb-4">Was wird angerechnet?</h3>
            <ul className="space-y-2 text-gray-300">
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Möbel in gutem Zustand</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Antiquitäten und Sammlerobjekte</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Funktionsfähige Elektrogeräte</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Schmuck und Edelmetalle</li>
            </ul>
          </div>
          <div>
            <h3 className="text-xl font-semibold text-violet-300 mb-4">So funktioniert's:</h3>
            <ol className="space-y-2 text-gray-300">
              <li>1. Kostenlose Vor-Ort-Besichtigung</li>
              <li>2. Professionelle Bewertung Ihrer Gegenstände</li>
              <li>3. Transparente Gutschrift auf Gesamtkosten</li>
              <li>4. Bis zu 50% Kostenersparnis möglich</li>
            </ol>
          </div>
        </div>
      </motion.div>

      {/* Regionale Services */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Entrümpelung in Ihrer Region</h2>
        <div className="grid md:grid-cols-3 gap-6">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Entrümpelung Saarbrücken</h3>
            <p className="text-gray-300 mb-4">Professionelle Entrümpelung im Saarland. Schnell, diskret und mit Wertanrechnung.</p>
            <NavLink to="/kontakt" className="text-violet-400 hover:text-violet-300 flex items-center">
              Angebot anfordern <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Entrümpelung Trier</h3>
            <p className="text-gray-300 mb-4">Haushaltsauflösung und Messi-Wohnung Hilfe in Trier und Umgebung.</p>
            <NavLink to="/kontakt" className="text-violet-400 hover:text-violet-300 flex items-center">
              Angebot anfordern <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Entrümpelung Kaiserslautern</h3>
            <p className="text-gray-300 mb-4">Kellerentrümpelung und Sperrmüll entsorgen in Kaiserslautern.</p>
            <NavLink to="/kontakt" className="text-violet-400 hover:text-violet-300 flex items-center">
              Angebot anfordern <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
        </div>
      </div>

      {/* FAQ Section */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Häufige Fragen zur Entrümpelung</h2>
        <div className="space-y-6">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Wie lange dauert eine Entrümpelung?</h3>
            <p className="text-gray-300">Je nach Größe der Wohnung und Menge der Gegenstände zwischen 4-8 Stunden für eine 3-Zimmer-Wohnung.</p>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Was kostet eine professionelle Entrümpelung?</h3>
            <p className="text-gray-300">Die Kosten variieren je nach Aufwand. Mit unserer Wertanrechnung können Sie jedoch erheblich sparen.</p>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Entsorgen Sie auch Sondermüll?</h3>
            <p className="text-gray-300">Ja, wir kümmern uns um die fachgerechte Entsorgung aller Materialien inklusive Elektroschrott und Sondermüll.</p>
          </div>
        </div>
      </div>

      {/* Related Articles */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Weitere hilfreiche Ratgeber</h2>
        <div className="grid md:grid-cols-2 gap-6">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Umzug Checkliste</h3>
            <p className="text-gray-300 mb-4">Nach der Entrümpelung steht der Umzug an? Unsere 8-Wochen-Checkliste hilft Ihnen dabei, stressfrei umzuziehen.</p>
            <NavLink to="/ratgeber-umzug-checkliste" className="text-violet-400 hover:text-violet-300 flex items-center">
              Umzug Ratgeber lesen <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Hausreinigung & Endreinigung</h3>
            <p className="text-gray-300 mb-4">Nach Entrümpelung und Umzug folgt die Endreinigung. Erfahren Sie, wie Sie Ihre Kaution zurückbekommen.</p>
            <NavLink to="/ratgeber-hausreinigung-endreinigung" className="text-violet-400 hover:text-violet-300 flex items-center">
              Reinigung Ratgeber lesen <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
        </div>
      </div>

      {/* CTA Section */}
      <div className="text-center bg-gradient-to-r from-violet-600/20 to-purple-600/20 p-8 rounded-xl">
        <h2 className="text-3xl font-bold text-white mb-4">Bereit für Ihre Entrümpelung?</h2>
        <p className="text-lg text-violet-200 max-w-2xl mx-auto mb-8">
          Vereinbaren Sie jetzt einen kostenlosen Beratungstermin und lassen Sie sich von unseren Experten beraten.
        </p>
        <Button asChild size="lg" className="bg-violet-600 hover:bg-violet-700 text-white font-bold py-4 px-8 rounded-full text-lg transition-transform transform hover:scale-105">
          <NavLink to="/kontakt">Kostenlose Beratung vereinbaren</NavLink>
        </Button>
      </div>
    </motion.div>
  );
};

export default EntruempelungRatgeberPage;