import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { CheckCircle, Sparkles, Clock, Shield, ArrowRight, MapPin, Droplets } from 'lucide-react';
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

const reinigungSteps = [
  {
    number: 1,
    title: "Vorbereitung & Planung",
    description: "Reinigungsumfang festlegen und Termine koordinieren",
    details: "Bestimmen Sie den Umfang der Reinigung: Grundreinigung, Endreinigung oder regelmäßige Hausreinigung. Planen Sie ausreichend Zeit ein."
  },
  {
    number: 2,
    title: "Räume entrümpeln",
    description: "Alle Räume von persönlichen Gegenständen befreien",
    details: "Für eine gründliche Reinigung müssen alle Oberflächen frei zugänglich sein. Räumen Sie Möbel ab und schaffen Sie Platz."
  },
  {
    number: 3,
    title: "Professionelle Grundreinigung",
    description: "Systematische Reinigung von oben nach unten",
    details: "Wir beginnen mit Decken und Wänden, reinigen dann Fenster, Böden und alle Oberflächen mit professionellen Geräten und Reinigungsmitteln."
  },
  {
    number: 4,
    title: "Spezialreinigung",
    description: "Bad, Küche und schwer zugängliche Bereiche",
    details: "Intensive Reinigung von Sanitäranlagen, Küchengeräten, Fliesen und Fugen. Entfernung von Kalk, Fett und hartnäckigen Verschmutzungen."
  },
  {
    number: 5,
    title: "Endkontrolle & Übergabe",
    description: "Qualitätskontrolle und besenreine Übergabe",
    details: "Abschließende Kontrolle aller gereinigten Bereiche. Die Wohnung wird besenrein und bezugsfertig übergeben."
  }
];

const reinigungsServices = [
  {
    icon: <Sparkles className="h-8 w-8 text-violet-400" />,
    title: "Endreinigung",
    description: "Professionelle Endreinigung nach Umzug für Wohnungsübergabe"
  },
  {
    icon: <Droplets className="h-8 w-8 text-violet-400" />,
    title: "Grundreinigung",
    description: "Intensive Tiefenreinigung für Wohnungen und Häuser"
  },
  {
    icon: <Shield className="h-8 w-8 text-violet-400" />,
    title: "Fensterreinigung",
    description: "Streifenfreie Fensterreinigung innen und außen"
  }
];

const regions = [
  "Saarbrücken", "Trier", "Kaiserslautern", "Mainz", "Koblenz", "Ludwigshafen"
];

const HausreinigungRatgeberPage = () => {
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
        <title>Hausreinigung & Endreinigung Ratgeber - Rheinland-Pfalz & Saarland | YLA</title>
        <meta name="description" content="Hausreinigung Ratgeber: Endreinigung nach Umzug, Grundreinigung & Fenster putzen in Rheinland-Pfalz & Saarland. Professionell & günstig. Jetzt informieren!" />
        <meta name="keywords" content="Hausreinigung Rheinland-Pfalz, Endreinigung nach Umzug, Grundreinigung Saarland, Fenster putzen, Wohnungsreinigung Trier, Hausreinigung Kaiserslautern" />
        <link rel="canonical" href="/ratgeber-hausreinigung-endreinigung" />
        <script type="application/ld+json">
          {JSON.stringify({
            "@context": "https://schema.org",
            "@type": "HowTo",
            "name": "Professionelle Hausreinigung in 5 Schritten",
            "description": "Anleitung für Hausreinigung und Endreinigung in Rheinland-Pfalz und Saarland",
            "image": "https://yla-umzug.de/images/hausreinigung-5-schritte.jpg",
            "totalTime": "P1D",
            "estimatedCost": {
              "@type": "MonetaryAmount",
              "currency": "EUR",
              "value": "80-300"
            },
            "step": [
              {
                "@type": "HowToStep",
                "name": "Vorbereitung & Planung",
                "text": "Reinigungsumfang festlegen und Termine koordinieren"
              },
              {
                "@type": "HowToStep",
                "name": "Räume entrümpeln",
                "text": "Alle Räume von persönlichen Gegenständen befreien"
              },
              {
                "@type": "HowToStep",
                "name": "Professionelle Grundreinigung",
                "text": "Systematische Reinigung von oben nach unten"
              },
              {
                "@type": "HowToStep",
                "name": "Spezialreinigung",
                "text": "Bad, Küche und schwer zugängliche Bereiche"
              },
              {
                "@type": "HowToStep",
                "name": "Endkontrolle & Übergabe",
                "text": "Qualitätskontrolle und besenreine Übergabe"
              }
            ]
          })}
        </script>
      </Helmet>

      {/* Hero Section */}
      <div className="text-center mb-16">
        <h1 className="text-4xl md:text-5xl font-extrabold text-white mb-6">
          Hausreinigung & Endreinigung: Ihr Profi-Ratgeber
        </h1>
        <p className="text-xl text-violet-200 max-w-4xl mx-auto mb-8">
          Benötigen Sie eine professionelle Hausreinigung in <strong>Rheinland-Pfalz oder dem Saarland</strong>? 
          Unser Ratgeber zeigt Ihnen, wie Sie mit der richtigen Endreinigung Zeit sparen und Ihre Kaution zurückbekommen.
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

      {/* Reinigung in 5 Schritten */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Professionelle Hausreinigung in 5 Schritten</h2>
        <div className="space-y-8">
          {reinigungSteps.map((step, index) => (
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

      {/* Endreinigung nach Umzug */}
      <motion.div
        initial={{ opacity: 0, y: 50 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, amount: 0.3 }}
        transition={{ duration: 0.5 }}
        className="bg-gradient-to-r from-violet-600/20 to-purple-600/20 p-8 rounded-xl mb-16"
      >
        <h2 className="text-3xl font-bold text-white mb-6">🏠 Endreinigung nach Umzug: Kaution zurückbekommen</h2>
        <div className="grid md:grid-cols-2 gap-8">
          <div>
            <h3 className="text-xl font-semibold text-violet-300 mb-4">Was gehört zur Endreinigung?</h3>
            <ul className="space-y-2 text-gray-300">
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Alle Räume gründlich reinigen</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Küche: Herd, Backofen, Kühlschrank</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Bad: Sanitäranlagen, Fliesen, Fugen</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Fenster innen und außen putzen</li>
              <li className="flex items-center"><CheckCircle size={16} className="text-green-400 mr-2" />Böden wischen und saugen</li>
            </ul>
          </div>
          <div>
            <h3 className="text-xl font-semibold text-violet-300 mb-4">Profi-Tipps für die Wohnungsübergabe:</h3>
            <ul className="space-y-2 text-gray-300">
              <li>• Endreinigung 1-2 Tage vor Übergabe</li>
              <li>• Alle Schäden dokumentieren</li>
              <li>• Übergabeprotokoll sorgfältig prüfen</li>
              <li>• Professionelle Reinigung spart Zeit</li>
              <li>• Garantie auf unsere Reinigungsleistung</li>
            </ul>
          </div>
        </div>
      </motion.div>

      {/* Services */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Unsere Reinigungsleistungen</h2>
        <div className="grid md:grid-cols-3 gap-6">
          {reinigungsServices.map((service, index) => (
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

      {/* Reinigungsarten */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Verschiedene Arten der Hausreinigung</h2>
        <div className="grid md:grid-cols-2 gap-8">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-4">Grundreinigung vs. Endreinigung</h3>
            <div className="space-y-4">
              <div>
                <h4 className="font-semibold text-violet-300">Grundreinigung:</h4>
                <p className="text-gray-300 text-sm">Intensive Tiefenreinigung für bewohnte Räume. Ideal für Frühjahrsputz oder vor besonderen Anlässen.</p>
              </div>
              <div>
                <h4 className="font-semibold text-violet-300">Endreinigung:</h4>
                <p className="text-gray-300 text-sm">Komplette Reinigung nach Auszug. Besenrein für die Wohnungsübergabe und Kautionsrückgabe.</p>
              </div>
            </div>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-4">Spezialreinigungen</h3>
            <ul className="space-y-3 text-gray-300">
              <li>• <strong>Teppichreinigung:</strong> Tiefenreinigung mit Spezialgeräten</li>
              <li>• <strong>Polsterreinigung:</strong> Schonende Reinigung von Möbeln</li>
              <li>• <strong>Fensterreinigung:</strong> Streifenfrei innen und außen</li>
              <li>• <strong>Büroreinigung:</strong> Regelmäßige Reinigung von Geschäftsräumen</li>
            </ul>
          </div>
        </div>
      </div>

      {/* Kosten & Preise */}
      <motion.div
        initial={{ opacity: 0, y: 50 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, amount: 0.3 }}
        transition={{ duration: 0.5 }}
        className="bg-gradient-to-r from-violet-600/20 to-purple-600/20 p-8 rounded-xl mb-16"
      >
        <h2 className="text-3xl font-bold text-white mb-6">💰 Hausreinigung Kosten: Transparente Preise</h2>
        <div className="grid md:grid-cols-3 gap-6">
          <div className="bg-gray-800/40 p-6 rounded-xl text-center">
            <h3 className="text-lg font-bold text-white mb-2">1-Zimmer Wohnung</h3>
            <p className="text-2xl font-bold text-violet-400 mb-2">ab 80€</p>
            <p className="text-gray-300 text-sm">Endreinigung inkl. Bad & Küche</p>
          </div>
          <div className="bg-gray-800/40 p-6 rounded-xl text-center">
            <h3 className="text-lg font-bold text-white mb-2">3-Zimmer Wohnung</h3>
            <p className="text-2xl font-bold text-violet-400 mb-2">ab 180€</p>
            <p className="text-gray-300 text-sm">Komplette Endreinigung</p>
          </div>
          <div className="bg-gray-800/40 p-6 rounded-xl text-center">
            <h3 className="text-lg font-bold text-white mb-2">Einfamilienhaus</h3>
            <p className="text-2xl font-bold text-violet-400 mb-2">ab 300€</p>
            <p className="text-gray-300 text-sm">Grundreinigung nach Vereinbarung</p>
          </div>
        </div>
        <p className="text-center text-gray-300 mt-6">
          *Preise sind Richtwerte und können je nach Verschmutzungsgrad und Zusatzleistungen variieren
        </p>
      </motion.div>

      {/* Regionale Services */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Hausreinigung in Ihrer Region</h2>
        <div className="grid md:grid-cols-3 gap-6">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Hausreinigung Saarland</h3>
            <p className="text-gray-300 mb-4">Professionelle Endreinigung und Grundreinigung in Saarbrücken und Umgebung.</p>
            <NavLink to="/kontakt" className="text-violet-400 hover:text-violet-300 flex items-center">
              Angebot anfordern <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Hausreinigung Trier</h3>
            <p className="text-gray-300 mb-4">Wohnungsreinigung und Fensterreinigung in Trier und der Region.</p>
            <NavLink to="/kontakt" className="text-violet-400 hover:text-violet-300 flex items-center">
              Angebot anfordern <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Hausreinigung Kaiserslautern</h3>
            <p className="text-gray-300 mb-4">Büroreinigung und Hausreinigung in Kaiserslautern und Umgebung.</p>
            <NavLink to="/kontakt" className="text-violet-400 hover:text-violet-300 flex items-center">
              Angebot anfordern <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
        </div>
      </div>

      {/* FAQ */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Häufige Fragen zur Hausreinigung</h2>
        <div className="space-y-6">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Wie lange dauert eine Endreinigung?</h3>
            <p className="text-gray-300">Je nach Wohnungsgröße 4-8 Stunden. Eine 3-Zimmer-Wohnung benötigt etwa 6 Stunden.</p>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Muss ich bei der Reinigung anwesend sein?</h3>
            <p className="text-gray-300">Nein, Sie können uns die Schlüssel überlassen. Wir arbeiten zuverlässig und diskret.</p>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Bringen Sie eigene Reinigungsmittel mit?</h3>
            <p className="text-gray-300">Ja, wir verwenden professionelle, umweltfreundliche Reinigungsmittel und alle notwendigen Geräte.</p>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Gibt es eine Garantie auf die Reinigung?</h3>
            <p className="text-gray-300">Ja, wir garantieren für unsere Arbeit. Bei Beanstandungen kommen wir kostenfrei nach.</p>
          </div>
        </div>
      </div>

      {/* Related Articles */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Weitere hilfreiche Ratgeber</h2>
        <div className="grid md:grid-cols-2 gap-6">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Entrümpelung vor der Reinigung</h3>
            <p className="text-gray-300 mb-4">Vor der Hausreinigung sollten Sie entrümpeln. Unser 5-Schritte-Guide zeigt, wie es richtig geht.</p>
            <NavLink to="/ratgeber-entruempelung-5-schritte" className="text-violet-400 hover:text-violet-300 flex items-center">
              Entrümpelung Ratgeber lesen <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Umzug planen</h3>
            <p className="text-gray-300 mb-4">Nach der Reinigung steht der Umzug an? Unsere Checkliste hilft bei der stressfreien Planung.</p>
            <NavLink to="/ratgeber-umzug-checkliste" className="text-violet-400 hover:text-violet-300 flex items-center">
              Umzug Ratgeber lesen <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
        </div>
      </div>

      {/* CTA */}
      <div className="text-center bg-gradient-to-r from-violet-600/20 to-purple-600/20 p-8 rounded-xl">
        <h2 className="text-3xl font-bold text-white mb-4">Bereit für eine professionelle Hausreinigung?</h2>
        <p className="text-lg text-violet-200 max-w-2xl mx-auto mb-8">
          Sparen Sie Zeit und Stress - lassen Sie Profis ran! Kostenlose Beratung und transparente Preise.
        </p>
        <Button asChild size="lg" className="bg-violet-600 hover:bg-violet-700 text-white font-bold py-4 px-8 rounded-full text-lg transition-transform transform hover:scale-105">
          <NavLink to="/kontakt">Kostenloses Angebot für Hausreinigung</NavLink>
        </Button>
      </div>
    </motion.div>
  );
};

export default HausreinigungRatgeberPage;