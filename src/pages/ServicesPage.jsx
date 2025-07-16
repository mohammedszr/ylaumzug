import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { Package, Building, Box, Wrench } from 'lucide-react';

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
    icon: <Package className="h-10 w-10 text-violet-400" />,
    title: "Privatumzüge",
    description: "Auch wenn unser Fokus auf Entrümpelungen liegt, unterstützen wir Sie professionell bei Ihrem Privatumzug.",
  },
  {
    icon: <Building className="h-10 w-10 text-violet-400" />,
    title: "Firmenumzüge",
    description: "Wir sorgen für einen reibungslosen Betriebsübergang mit minimaler Ausfallzeit für Ihr Unternehmen.",
  },
  {
    icon: <Box className="h-10 w-10 text-violet-400" />,
    title: "Verpackungsservice",
    description: "Sparen Sie Zeit und Nerven. Unsere Profis verpacken Ihr Inventar sicher und professionell für den Transport.",
  },
  {
    icon: <Wrench className="h-10 w-10 text-violet-400" />,
    title: "Möbelmontage",
    description: "Wir demontieren und montieren Ihre Möbel fachgerecht am neuen Standort.",
  },
];

const ServicesPage = () => {
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
        <title>Umzüge & Zusatzleistungen - YLA Umzug</title>
        <meta name="description" content="Neben Entrümpelungen bietet YLA Umzug auch klassische Umzüge, Verpackungsservice und Möbelmontage im Saarland und Rheinland-Pfalz an." />
      </Helmet>

      <div className="text-center mb-16">
        <h1 className="text-4xl md:text-5xl font-extrabold text-white mb-4">Umzüge & Zusatzleistungen</h1>
        <p className="text-lg text-violet-200 max-w-2xl mx-auto">
          Wir bieten neben unserer Spezialisierung auf Entrümpelungen auch ein komplettes Servicepaket für Ihren Umzug an.
        </p>
      </div>

      <div className="grid md:grid-cols-2 lg:grid-cols-2 gap-8">
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
    </motion.div>
  );
};

export default ServicesPage;