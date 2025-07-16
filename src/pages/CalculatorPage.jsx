import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import Calculator from '@/components/calculator/Calculator';

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

const CalculatorPage = () => {
  return (
    <motion.div
      initial="initial"
      animate="in"
      exit="out"
      variants={pageVariants}
      transition={pageTransition}
      className="min-h-screen bg-gray-900 py-12"
    >
      <Helmet>
        <title>Umzugskosten berechnen - Kostenloses Angebot | YLA Umzug</title>
        <meta name="description" content="Berechnen Sie Ihre Umzugskosten online. Umzug, Entrümpelung, Putzservice im Saarland & RLP. Sofortiges Angebot, keine versteckten Kosten." />
        <meta name="keywords" content="umzugskosten berechnen, umzug rechner, entrümpelung kosten, putzservice preise, saarland, rheinland-pfalz" />
      </Helmet>
      
      <div className="container mx-auto px-6">
        <motion.div
          initial={{ opacity: 0, y: -30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, delay: 0.2 }}
          className="text-center mb-12"
        >
          <h1 className="text-4xl md:text-5xl font-extrabold text-white leading-tight mb-4">
            Umzugskosten berechnen
          </h1>
          <p className="text-lg md:text-xl text-violet-200 max-w-3xl mx-auto">
            Erhalten Sie in wenigen Minuten eine unverbindliche Kostenschätzung für Ihren Umzug, 
            Ihre Entrümpelung oder Ihren Putzservice.
          </p>
        </motion.div>

        <Calculator />

        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, delay: 0.8 }}
          className="text-center mt-12"
        >
          <div className="bg-gray-800/50 rounded-xl p-8 max-w-4xl mx-auto">
            <h2 className="text-2xl font-bold text-white mb-4">
              Warum YLA Umzug wählen?
            </h2>
            <div className="grid md:grid-cols-3 gap-6 text-gray-300">
              <div>
                <div className="text-violet-400 text-3xl mb-2">✓</div>
                <h3 className="font-semibold text-white mb-2">Festpreisgarantie</h3>
                <p className="text-sm">Keine versteckten Kosten, transparente Preise</p>
              </div>
              <div>
                <div className="text-violet-400 text-3xl mb-2">✓</div>
                <h3 className="font-semibold text-white mb-2">Kostenlose Besichtigung</h3>
                <p className="text-sm">Unverbindliche Vor-Ort-Beratung</p>
              </div>
              <div>
                <div className="text-violet-400 text-3xl mb-2">✓</div>
                <h3 className="font-semibold text-white mb-2">Schnelle Termine</h3>
                <p className="text-sm">Flexible Terminplanung nach Ihren Wünschen</p>
              </div>
            </div>
          </div>
        </motion.div>
      </div>
    </motion.div>
  );
};

export default CalculatorPage;