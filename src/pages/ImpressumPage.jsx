import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';

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

const ImpressumPage = () => {
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
        <title>Impressum - YLA Umzugservice</title>
        <meta name="description" content="Impressum und rechtliche Angaben der YLA Umzugservice" />
        <meta name="robots" content="noindex,follow" />
      </Helmet>

      <div className="max-w-4xl mx-auto">
        <motion.div
          initial={{ opacity: 0, y: -30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, delay: 0.2 }}
          className="text-center mb-12"
        >
          <h1 className="text-4xl md:text-5xl font-extrabold text-white leading-tight mb-4">
            Impressum
          </h1>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, delay: 0.4 }}
          className="bg-gray-800/60 rounded-xl p-8 text-gray-300 space-y-6"
        >
          <div>
            <h2 className="text-xl font-bold text-white mb-4">Angaben gemäß § 5 TMG</h2>
            <div className="space-y-2">
              <p><strong>Firmenname:</strong> YLA Umzugservice</p>
              <p><strong>Inhaberin:</strong> M. Hanifa</p>
              <p><strong>Anschrift:</strong> Hackstr. 4<br />67655 Kaiserslautern<br />Deutschland</p>
              <p><strong>Telefon:</strong> +49 1575 0693353</p>
              <p><strong>E-Mail:</strong> info@yla-umzug.de</p>
              <p><strong>Website:</strong> www.yla-umzug.de</p>
            </div>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
            <div className="space-y-2">
              <p>M. Hanifa<br />Hackstr. 4<br />67655 Kaiserslautern<br />Deutschland</p>
            </div>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">Haftungsausschluss</h2>
            <p>Trotz sorgfältiger inhaltlicher Kontrolle übernehmen wir keine Haftung für die Inhalte externer Links. Für den Inhalt der verlinkten Seiten sind ausschließlich deren Betreiber verantwortlich.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">Verbraucherstreitbeilegung / Universalschlichtungsstelle</h2>
            <p>Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>
          </div>

          <div className="pt-4 border-t border-gray-700">
            <p className="text-sm text-gray-400">Stand: Juli 2025</p>
          </div>
        </motion.div>
      </div>
    </motion.div>
  );
};

export default ImpressumPage;