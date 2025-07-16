import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { CheckCircle, Truck, Package, Calendar, ArrowRight, MapPin, Clock } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { NavLink } from 'react-router-dom';

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

const umzugSteps = [
  {
    number: 1,
    title: "Umzugsplanung & Checkliste",
    description: "8 Wochen vor dem Umzug mit der Planung beginnen",
    details: "Erstellen Sie eine detaillierte Checkliste: Umzugstermin festlegen, Umzugsunternehmen vergleichen, Urlaub beantragen und Nachsendeauftrag stellen."
  },
  {
    number: 2,
    title: "Angebote vergleichen",
    description: "Mehrere Kostenvoranschläge von Umzugsfirmen einholen",
    details: "Holen Sie mindestens 3 Angebote ein. Achten Sie auf versteckte Kosten und prüfen Sie Versicherungsschutz. YLA Umzug bietet transparente Preise ohne Überraschungen."
  },
  {
    number: 3,
    title: "Umzugskartons & Verpackung",
    description: "Rechtzeitig Umzugsmaterial besorgen und systematisch packen",
    details: "Pro Zimmer ca. 10-15 Kartons einplanen. Schwere Gegenstände in kleine Kartons, leichte in große. Jeder Karton sollte beschriftet werden."
  },
  {
    number: 4,
    title: "Möbellift & Sonderleistungen",
    description: "Bei schweren Möbeln oder engen Treppenhäusern Möbellift mieten",
    details: "Für Klaviere, schwere Schränke oder bei engen Treppenhäusern ist ein Möbellift oft unverzichtbar. Wir organisieren das für Sie."
  },
  {
    number: 5,
    title: "Umzugstag & Nachbereitung",
    description: "Professionelle Abwicklung und Endreinigung",
    details: "Am Umzugstag sollten Sie vor Ort sein. Nach dem Umzug folgt die Endreinigung der alten Wohnung - auch das übernehmen wir gerne für Sie."
  }
];

const umzugServices = [
  {
    icon: <Truck className="h-8 w-8 text-violet-400" />,
    title: "Komplettumzug",
    description: "Von der Planung bis zur Endreinigung - alles aus einer Hand"
  },
  {
    icon: <Package className="h-8 w-8 text-violet-400" />,
    title: "Verpackungsservice",
    description: "Professionelles Verpacken Ihrer wertvollen Gegenstände"
  },
  {
    icon: <Calendar className="h-8 w-8 text-violet-400" />,
    title: "Terminumzug",
    description: "Pünktlich und zuverlässig zum vereinbarten Termin"
  }
];

const regions = [
  "Saarbrücken", "Trier", "Kaiserslautern", "Mainz", "Koblenz", "Ludwigshafen"
];

const UmzugRatgeberPage = () => {
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
        <title>Umzug Ratgeber: Checkliste & Tipps für Kaiserslautern, Trier & Saarbrücken | YLA</title>
        <meta name="description" content="Umzug Ratgeber: Komplette Checkliste für Ihren Umzug in Kaiserslautern, Trier & Saarbrücken. Möbellift, Verpackung & Kosten sparen. Jetzt informieren!" />
        <meta name="keywords" content="Umzug Kaiserslautern, Umzugsfirma Trier, Umzug Saarbrücken, Möbellift mieten, Umzugscheckliste, günstiger Umzug Rheinland-Pfalz" />
        <link rel="canonical" href="/ratgeber-umzug-checkliste" />
        <script type="application/ld+json">
          {JSON.stringify({
            "@context": "https://schema.org",
            "@type": "HowTo",
            "name": "Umzug Checkliste: Stressfrei umziehen in 8 Wochen",
            "description": "Komplette Umzug Checkliste für Kaiserslautern, Trier und Saarbrücken mit Profi-Tipps",
            "image": "https://yla-umzug.de/images/umzug-checkliste.jpg",
            "totalTime": "P8W",
            "estimatedCost": {
              "@type": "MonetaryAmount",
              "currency": "EUR",
              "value": "800-1500"
            },
            "step": [
              {
                "@type": "HowToStep",
                "name": "Umzugsplanung & Checkliste",
                "text": "8 Wochen vor dem Umzug mit der Planung beginnen"
              },
              {
                "@type": "HowToStep",
                "name": "Angebote vergleichen",
                "text": "Mehrere Kostenvoranschläge von Umzugsfirmen einholen"
              },
              {
                "@type": "HowToStep",
                "name": "Umzugskartons & Verpackung",
                "text": "Rechtzeitig Umzugsmaterial besorgen und systematisch packen"
              },
              {
                "@type": "HowToStep",
                "name": "Möbellift & Sonderleistungen",
                "text": "Bei schweren Möbeln oder engen Treppenhäusern Möbellift mieten"
              },
              {
                "@type": "HowToStep",
                "name": "Umzugstag & Nachbereitung",
                "text": "Professionelle Abwicklung und Endreinigung"
              }
            ]
          })}
        </script>
      </Helmet>

      {/* Hero Section */}
      <div className="text-center mb-16">
        <h1 className="text-4xl md:text-5xl font-extrabold text-white mb-6">
          Umzug Ratgeber: Ihre komplette Checkliste
        </h1>
        <p className="text-xl text-violet-200 max-w-4xl mx-auto mb-8">
          Planen Sie einen Umzug in <strong>Kaiserslautern, Trier oder Saarbrücken</strong>? 
          Unser Experten-Ratgeber zeigt Ihnen Schritt für Schritt, wie Sie stressfrei und günstig umziehen.
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

      {/* Umzug in 5 Schritten */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Umzug in 5 Schritten: So geht's richtig</h2>
        <div className="space-y-8">
          {umzugSteps.map((step, index) => (
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

      {/* Umzugscheckliste */}
      <motion.div
        initial={{ opacity: 0, y: 50 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, amount: 0.3 }}
        transition={{ duration: 0.5 }}
        className="bg-gradient-to-r from-violet-600/20 to-purple-600/20 p-8 rounded-xl mb-16"
      >
        <h2 className="text-3xl font-bold text-white mb-6">📋 Umzugscheckliste: 8 Wochen Countdown</h2>
        <div className="grid md:grid-cols-2 gap-8">
          <div>
            <h3 className="text-xl font-semibold text-violet-300 mb-4">8-6 Wochen vorher:</h3>
            <ul className="space-y-2 text-gray-300">
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Umzugstermin festlegen</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Umzugsunternehmen beauftragen</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Urlaub beantragen</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Kündigung alte Wohnung</li>
            </ul>
            <h3 className="text-xl font-semibold text-violet-300 mb-4 mt-6">4-2 Wochen vorher:</h3>
            <ul className="space-y-2 text-gray-300">
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Nachsendeauftrag stellen</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Ummeldungen (Strom, Gas, Internet)</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Umzugskartons besorgen</li>
            </ul>
          </div>
          <div>
            <h3 className="text-xl font-semibold text-violet-300 mb-4">1 Woche vorher:</h3>
            <ul className="space-y-2 text-gray-300">
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Packen beginnen</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Kühlschrank abtauen</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Halteverbotszone beantragen</li>
            </ul>
            <h3 className="text-xl font-semibold text-violet-300 mb-4 mt-6">Am Umzugstag:</h3>
            <ul className="space-y-2 text-gray-300">
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Zählerstände notieren</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Wohnungsübergabe</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Endreinigung organisieren</li>
            </ul>
          </div>
        </div>
      </motion.div>

      {/* Services */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Unsere Umzugsleistungen</h2>
        <div className="grid md:grid-cols-3 gap-6">
          {umzugServices.map((service, index) => (
            <motion.div
              key={service.title}
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.3 }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className="bg-gray-800/60 p-6 rounded-xl text-center"
            >
              <div className="bg-violet-500/20 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                {service.icon}
              </div>
              <h3 className="text-xl font-bold text-white mb-3">{service.title}</h3>
              <p className="text-gray-300">{service.description}</p>
            </motion.div>
          ))}
        </div>
      </div>

      {/* Kosten sparen */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">💰 Umzugskosten sparen: Profi-Tipps</h2>
        <div className="grid md:grid-cols-2 gap-8">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-4">Günstig umziehen:</h3>
            <ul className="space-y-3 text-gray-300">
              <li>• Umzug außerhalb der Hauptsaison (Sommer)</li>
              <li>• Werktags statt am Wochenende umziehen</li>
              <li>• Selbst packen, Transport vom Profi</li>
              <li>• Mehrere Angebote vergleichen</li>
              <li>• Umzugskosten steuerlich absetzen</li>
            </ul>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-4">Versteckte Kosten vermeiden:</h3>
            <ul className="space-y-3 text-gray-300">
              <li>• Anfahrtskosten im Angebot klären</li>
              <li>• Möbellift-Kosten vorab besprechen</li>
              <li>• Verpackungsmaterial-Preise erfragen</li>
              <li>• Versicherungsschutz prüfen</li>
              <li>• Festpreis statt Stundenlohn vereinbaren</li>
            </ul>
          </div>
        </div>
      </div>

      {/* Regionale Services */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Umzug in Ihrer Region</h2>
        <div className="grid md:grid-cols-3 gap-6">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Umzug Kaiserslautern</h3>
            <p className="text-gray-300 mb-4">Professionelle Umzugsfirma in Kaiserslautern. Günstig, schnell und zuverlässig.</p>
            <NavLink to="/kontakt" className="text-violet-400 hover:text-violet-300 flex items-center">
              Angebot anfordern <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Umzug Trier</h3>
            <p className="text-gray-300 mb-4">Umzugsservice in Trier mit Möbellift und Verpackungsservice.</p>
            <NavLink to="/kontakt" className="text-violet-400 hover:text-violet-300 flex items-center">
              Angebot anfordern <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Umzug Saarbrücken</h3>
            <p className="text-gray-300 mb-4">Komplettumzug im Saarland mit Endreinigung und Entsorgung.</p>
            <NavLink to="/kontakt" className="text-violet-400 hover:text-violet-300 flex items-center">
              Angebot anfordern <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
        </div>
      </div>

      {/* FAQ */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Häufige Fragen zum Umzug</h2>
        <div className="space-y-6">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Wann sollte ich eine Umzugsfirma beauftragen?</h3>
            <p className="text-gray-300">Idealerweise 6-8 Wochen vor dem Umzugstermin, besonders in der Hauptsaison (Sommer).</p>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Was kostet ein Umzug in Rheinland-Pfalz?</h3>
            <p className="text-gray-300">Die Kosten variieren je nach Entfernung und Aufwand. Eine 3-Zimmer-Wohnung kostet ca. 800-1500€.</p>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Brauche ich einen Möbellift?</h3>
            <p className="text-gray-300">Bei schweren Möbeln, engen Treppenhäusern oder hohen Stockwerken ist ein Möbellift oft unverzichtbar.</p>
          </div>
        </div>
      </div>

      {/* Related Articles */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Weitere hilfreiche Ratgeber</h2>
        <div className="grid md:grid-cols-2 gap-6">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Entrümpelung vor dem Umzug</h3>
            <p className="text-gray-300 mb-4">Vor dem Umzug entrümpeln spart Zeit und Geld. Erfahren Sie, wie Sie in 5 Schritten erfolgreich entrümpeln.</p>
            <NavLink to="/ratgeber-entruempelung-5-schritte" className="text-violet-400 hover:text-violet-300 flex items-center">
              Entrümpelung Ratgeber lesen <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Endreinigung nach Umzug</h3>
            <p className="text-gray-300 mb-4">Nach dem Umzug steht die Endreinigung an. So bekommen Sie Ihre Kaution zurück und sparen Zeit.</p>
            <NavLink to="/ratgeber-hausreinigung-endreinigung" className="text-violet-400 hover:text-violet-300 flex items-center">
              Reinigung Ratgeber lesen <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
        </div>
      </div>

      {/* CTA */}
      <div className="text-center bg-gradient-to-r from-violet-600/20 to-purple-600/20 p-8 rounded-xl">
        <h2 className="text-3xl font-bold text-white mb-4">Bereit für Ihren stressfreien Umzug?</h2>
        <p className="text-lg text-violet-200 max-w-2xl mx-auto mb-8">
          Lassen Sie sich von unseren Umzugsexperten beraten und erhalten Sie ein kostenloses, unverbindliches Angebot.
        </p>
        <Button asChild size="lg" className="bg-violet-600 hover:bg-violet-700 text-white font-bold py-4 px-8 rounded-full text-lg transition-transform transform hover:scale-105">
          <NavLink to="/kontakt">Kostenloses Umzugsangebot anfordern</NavLink>
        </Button>
      </div>
    </motion.div>
  );
};

export default UmzugRatgeberPage;