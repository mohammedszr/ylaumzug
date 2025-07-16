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
    </motion.div>
  );
};

export default HomePage;