import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { Trash2, Home, Shield, Users } from 'lucide-react';
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

const services = [
  {
    icon: <Home className="h-10 w-10 text-violet-400" />,
    title: "Haushaltsauflösungen",
    description: "Komplette, besenreine Räumung von Wohnungen und Häusern. Ideal für Erbfälle oder Umzüge ins Pflegeheim.",
  },
  {
    icon: <Users className="h-10 w-10 text-violet-400" />,
    title: "Hilfe bei Messi-Haushalten",
    description: "Wir arbeiten diskret, einfühlsam und professionell, um wieder bewohnbaren Raum zu schaffen. Ein Neuanfang ohne Urteile.",
  },
  {
    icon: <Trash2 className="h-10 w-10 text-violet-400" />,
    title: "Entsorgung & Sperrmüll",
    description: "Fachgerechte und umweltfreundliche Entsorgung von Sperrmüll, Elektroschrott und anderen Abfällen.",
  },
  {
    icon: <Shield className="h-10 w-10 text-violet-400" />,
    title: "Keller- & Dachbodenentrümpelung",
    description: "Schaffen Sie endlich wieder Platz. Wir räumen Ihren Keller oder Dachboden schnell und effizient.",
  },
];

const DeclutterPage = () => {
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
        <title>Entrümpelung & Entsorgung | Saarland & RLP | YLA Umzug</title>
        <meta name="description" content="YLA Umzug: Ihr Spezialist für Haushaltsauflösungen, Messi-Haushalte und Entsorgung in Saarbrücken, Trier und Kaiserslautern. Diskret & Zuverlässig." />
      </Helmet>

      <div className="text-center mb-16">
        <h1 className="text-4xl md:text-5xl font-extrabold text-white mb-4">Profi für Entrümpelung & Entsorgung</h1>
        <p className="text-lg text-violet-200 max-w-3xl mx-auto">
          Wir sind Ihr zuverlässiger Partner für jede Art von Entrümpelung im Saarland und Rheinland-Pfalz. Wir schaffen Ordnung – schnell, diskret und zum fairen Preis.
        </p>
      </div>

      <div className="grid md:grid-cols-2 lg:grid-cols-2 gap-8 mb-16">
        {services.map((service, index) => (
          <motion.div
            key={service.title}
            initial={{ opacity: 0, y: 50 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.3 }}
            transition={{ duration: 0.5, delay: index * 0.1 }}
            className="bg-gray-800/60 p-8 rounded-xl shadow-lg flex items-start space-x-6 hover:bg-gray-700/80 transition-colors duration-300"
          >
            <div className="flex-shrink-0 bg-violet-500/20 p-4 rounded-full">
              {service.icon}
            </div>
            <div>
              <h3 className="text-2xl font-bold text-white mb-2">{service.title}</h3>
              <p className="text-gray-400">{service.description}</p>
            </div>
          </motion.div>
        ))}
      </div>
      
      <div className="text-center">
         <h2 className="text-3xl font-bold text-white mb-4">Bereit für einen Neuanfang?</h2>
         <p className="text-lg text-violet-200 max-w-2xl mx-auto mb-8">
            Kontaktieren Sie uns noch heute für eine kostenlose und unverbindliche Besichtigung vor Ort.
         </p>
         <Button asChild size="lg" className="bg-violet-600 hover:bg-violet-700 text-white font-bold py-4 px-8 rounded-full text-lg transition-transform transform hover:scale-105">
            <NavLink to="/kontakt">Jetzt Angebot anfordern</NavLink>
         </Button>
      </div>

    </motion.div>
  );
};

export default DeclutterPage;