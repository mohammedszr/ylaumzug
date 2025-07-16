import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { Button } from '@/components/ui/button';
import { NavLink } from 'react-router-dom';
import { Star, ShieldCheck, Clock } from 'lucide-react';

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

const HomePage = () => {
  return (
    <motion.div
      initial="initial"
      animate="in"
      exit="out"
      variants={pageVariants}
      transition={pageTransition}
    >
      <Helmet>
        <title>YLA Umzug | Entrümpelung & Entsorgung im Saarland & RLP</title>
        <meta name="description" content="YLA Umzug: Profi für Entrümpelung, Entsorgung & Messi-Haushalte in Saarbrücken, Kaiserslautern, Trier. Fordern Sie jetzt Ihr kostenloses Angebot an!" />
        <meta name="keywords" content="Entrümpelung Saarbrücken, Haushaltsauflösung Trier, Messi-Wohnung Kaiserslautern, Umzug Rheinland-Pfalz, Entsorgung Saarland" />
        <script type="application/ld+json">
          {JSON.stringify({
            "@context": "https://schema.org",
            "@type": "LocalBusiness",
            "name": "YLA Umzug",
            "description": "Professionelle Entrümpelung, Entsorgung und Umzugsservice im Saarland und Rheinland-Pfalz",
            "url": "https://yla-umzug.de",
            "telephone": "+49-1575-0693353",
            "email": "info@yla-umzug.de",
            "address": {
              "@type": "PostalAddress",
              "addressRegion": "Rheinland-Pfalz, Saarland",
              "addressCountry": "DE"
            },
            "areaServed": [
              "Saarbrücken",
              "Trier", 
              "Kaiserslautern",
              "Mainz",
              "Koblenz",
              "Ludwigshafen"
            ],
            "serviceType": [
              "Entrümpelung",
              "Haushaltsauflösung", 
              "Messi-Wohnung Räumung",
              "Umzugsservice",
              "Entsorgung",
              "Endreinigung"
            ],
            "priceRange": "€€",
            "openingHours": "Mo-Sa 08:00-18:00"
          })}
        </script>
        <script type="application/ld+json">
          {JSON.stringify({
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
              {
                "@type": "Question",
                "name": "Was kostet eine Entrümpelung in Saarbrücken oder Trier?",
                "acceptedAnswer": {
                  "@type": "Answer",
                  "text": "Die Kosten variieren je nach Umfang und Aufwand. Eine 3-Zimmer-Wohnung kostet durchschnittlich 800-1500€. Mit unserer Wertanrechnung können Sie jedoch erheblich sparen."
                }
              },
              {
                "@type": "Question", 
                "name": "Wie lange dauert eine Haushaltsauflösung?",
                "acceptedAnswer": {
                  "@type": "Answer",
                  "text": "Je nach Größe der Wohnung und Menge der Gegenstände dauert eine komplette Haushaltsauflösung zwischen 4-8 Stunden."
                }
              },
              {
                "@type": "Question",
                "name": "Helfen Sie auch bei Messi-Wohnungen?", 
                "acceptedAnswer": {
                  "@type": "Answer",
                  "text": "Ja, wir sind spezialisiert auf die diskrete und einfühlsame Räumung von Messi-Haushalten in Kaiserslautern, Saarbrücken und der gesamten Region."
                }
              }
            ]
          })}
        </script>
      </Helmet>
      
      <section className="relative py-20 md:py-32 bg-gray-900 overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-violet-900/30 opacity-75"></div>
        <div className="container mx-auto px-6 text-center relative z-10">
          <motion.h1 
            initial={{ opacity: 0, y: -50 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.7, delay: 0.2 }}
            className="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-4"
          >
            Platz schaffen. Sorgenfrei.
          </motion.h1>
          <motion.p 
            initial={{ opacity: 0, y: -30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.7, delay: 0.4 }}
            className="text-lg md:text-xl text-violet-200 max-w-3xl mx-auto mb-8"
          >
            Ihre Experten für Entrümpelung, Entsorgung und die Sanierung von Messi-Haushalten im Saarland und Rheinland-Pfalz. Wir arbeiten schnell, diskret und zuverlässig.
          </motion.p>
          <motion.div
            initial={{ opacity: 0, scale: 0.8 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.5, delay: 0.6 }}
            className="space-y-4"
          >
            <Button asChild size="lg" className="bg-violet-600 hover:bg-violet-700 text-white font-bold py-4 px-8 rounded-full text-xl transition-transform transform hover:scale-105 mr-4">
              <NavLink to="/rechner">Jetzt Umzug berechnen</NavLink>
            </Button>
            <Button asChild variant="outline" size="lg" className="border-violet-400 text-violet-200 hover:bg-violet-600 hover:text-white font-bold py-4 px-8 rounded-full text-lg transition-transform transform hover:scale-105">
              <NavLink to="/kontakt">Kostenloses Angebot anfordern</NavLink>
            </Button>
          </motion.div>
        </div>
      </section>

      <section className="py-20 bg-gray-800">
        <div className="container mx-auto px-6">
          <h2 className="text-3xl font-bold text-center text-white mb-12">Ihr Partner in der Region</h2>
          <div className="grid md:grid-cols-3 gap-12">
            <motion.div 
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.5 }}
              transition={{ duration: 0.5 }}
              className="text-center p-6 bg-gray-700/50 rounded-xl shadow-lg"
            >
              <div className="flex justify-center mb-4">
                <div className="bg-violet-500/20 p-4 rounded-full">
                  <Star className="h-8 w-8 text-violet-400" />
                </div>
              </div>
              <h3 className="text-xl font-semibold text-white mb-2">Festpreisgarantie</h3>
              <p className="text-gray-400">Transparente Kosten ohne versteckte Gebühren. Darauf können Sie sich verlassen.</p>
            </motion.div>
            <motion.div 
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.5 }}
              transition={{ duration: 0.5, delay: 0.2 }}
              className="text-center p-6 bg-gray-700/50 rounded-xl shadow-lg"
            >
              <div className="flex justify-center mb-4">
                <div className="bg-violet-500/20 p-4 rounded-full">
                  <ShieldCheck className="h-8 w-8 text-violet-400" />
                </div>
              </div>
              <h3 className="text-xl font-semibold text-white mb-2">Diskret & Professionell</h3>
              <p className="text-gray-400">Besonders bei sensiblen Aufträgen wie Messi-Haushalten ist Diskretion unser oberstes Gebot.</p>
            </motion.div>
            <motion.div 
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.5 }}
              transition={{ duration: 0.5, delay: 0.4 }}
              className="text-center p-6 bg-gray-700/50 rounded-xl shadow-lg"
            >
              <div className="flex justify-center mb-4">
                <div className="bg-violet-500/20 p-4 rounded-full">
                  <Clock className="h-8 w-8 text-violet-400" />
                </div>
              </div>
              <h3 className="text-xl font-semibold text-white mb-2">Schnelle Termine</h3>
              <p className="text-gray-400">Wir sind für Sie da in Saarbrücken, Kaiserslautern, Trier und der gesamten Region.</p>
            </motion.div>
          </div>
        </div>
      </section>

      {/* FAQ Section */}
      <section className="py-20 bg-gray-900">
        <div className="container mx-auto px-6">
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.3 }}
            transition={{ duration: 0.5 }}
            className="text-center mb-16"
          >
            <h2 className="text-3xl md:text-4xl font-bold text-white mb-4">Häufig gestellte Fragen</h2>
            <p className="text-lg text-violet-200 max-w-2xl mx-auto">
              Hier finden Sie Antworten auf die wichtigsten Fragen zu unseren Dienstleistungen
            </p>
          </motion.div>
          
          <div className="max-w-4xl mx-auto space-y-6">
            <motion.div
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.3 }}
              transition={{ duration: 0.5, delay: 0.1 }}
              className="bg-gray-800/60 p-6 rounded-xl"
            >
              <h3 className="text-lg font-semibold text-white mb-2">Was kostet eine Entrümpelung in Saarbrücken oder Trier?</h3>
              <p className="text-gray-300">Die Kosten variieren je nach Umfang und Aufwand. Eine 3-Zimmer-Wohnung kostet durchschnittlich 800-1500€. Mit unserer Wertanrechnung können Sie jedoch erheblich sparen. Fordern Sie ein kostenloses Angebot an!</p>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.3 }}
              transition={{ duration: 0.5, delay: 0.2 }}
              className="bg-gray-800/60 p-6 rounded-xl"
            >
              <h3 className="text-lg font-semibold text-white mb-2">Wie lange dauert eine Haushaltsauflösung?</h3>
              <p className="text-gray-300">Je nach Größe der Wohnung und Menge der Gegenstände dauert eine komplette Haushaltsauflösung zwischen 4-8 Stunden. Wir arbeiten effizient und mit professionellem Equipment.</p>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.3 }}
              transition={{ duration: 0.5, delay: 0.3 }}
              className="bg-gray-800/60 p-6 rounded-xl"
            >
              <h3 className="text-lg font-semibold text-white mb-2">Helfen Sie auch bei Messi-Wohnungen?</h3>
              <p className="text-gray-300">Ja, wir sind spezialisiert auf die diskrete und einfühlsame Räumung von Messi-Haushalten. Unser Team arbeitet ohne Vorurteile und mit größter Diskretion in Kaiserslautern, Saarbrücken und der gesamten Region.</p>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.3 }}
              transition={{ duration: 0.5, delay: 0.4 }}
              className="bg-gray-800/60 p-6 rounded-xl"
            >
              <h3 className="text-lg font-semibold text-white mb-2">Übernimmt das Jobcenter die Umzugskosten bei Bürgergeld?</h3>
              <p className="text-gray-300">Ja, unter bestimmten Voraussetzungen übernimmt das Jobcenter die Umzugskosten für Bürgergeld-Empfänger. Wir erstellen Ihnen gerne einen rechtskonformen Kostenvoranschlag für das Jobcenter. Mehr dazu in unserem <NavLink to="/ratgeber-hartz-iv-umzug-jobcenter" className="text-violet-400 hover:text-violet-300">Bürgergeld-Umzug Ratgeber</NavLink>.</p>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.3 }}
              transition={{ duration: 0.5, delay: 0.5 }}
              className="bg-gray-800/60 p-6 rounded-xl"
            >
              <h3 className="text-lg font-semibold text-white mb-2">Entsorgen Sie auch Sondermüll und Elektroschrott?</h3>
              <p className="text-gray-300">Ja, wir kümmern uns um die fachgerechte Entsorgung aller Materialien inklusive Sondermüll, Elektroschrott und Sperrmüll. Alles wird umweltgerecht und nach gesetzlichen Vorschriften entsorgt.</p>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.3 }}
              transition={{ duration: 0.5, delay: 0.6 }}
              className="bg-gray-800/60 p-6 rounded-xl"
            >
              <h3 className="text-lg font-semibold text-white mb-2">Bieten Sie auch Endreinigung nach dem Umzug an?</h3>
              <p className="text-gray-300">Ja, wir bieten professionelle Endreinigung für die Wohnungsübergabe an. So bekommen Sie Ihre Kaution zurück und sparen Zeit. Mehr Infos in unserem <NavLink to="/ratgeber-hausreinigung-endreinigung" className="text-violet-400 hover:text-violet-300">Hausreinigung Ratgeber</NavLink>.</p>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.3 }}
              transition={{ duration: 0.5, delay: 0.7 }}
              className="bg-gray-800/60 p-6 rounded-xl"
            >
              <h3 className="text-lg font-semibold text-white mb-2">In welchen Regionen sind Sie tätig?</h3>
              <p className="text-gray-300">Wir sind in ganz Rheinland-Pfalz und dem Saarland tätig, mit Schwerpunkt auf Saarbrücken, Trier, Kaiserslautern, Mainz, Koblenz und Ludwigshafen. Kontaktieren Sie uns für Ihren Standort!</p>
            </motion.div>
          </div>
        </div>
      </section>
    </motion.div>
  );
};

export default HomePage;