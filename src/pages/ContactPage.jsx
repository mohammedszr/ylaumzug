import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/use-toast';
import { Phone, Mail, MapPin } from 'lucide-react';

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

const ContactPage = () => {
  const { toast } = useToast();

  const handleSubmit = (e) => {
    e.preventDefault();
    toast({
      title: "🚧 Funktion noch nicht implementiert",
      description: "Keine Sorge! Sie können diese Funktion in Ihrer nächsten Anfrage anfordern! 🚀",
    });
  };

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
        <title>Kontakt - YLA Umzug | Angebot für Entrümpelung anfordern</title>
        <meta name="description" content="Kontaktieren Sie YLA Umzug für ein unverbindliches Angebot für Ihre Entrümpelung in Saarbrücken, Kaiserslautern, Trier und Umgebung. Wir freuen uns auf Sie!" />
      </Helmet>

      <div className="text-center mb-12">
        <h1 className="text-4xl md:text-5xl font-extrabold text-white mb-4">Nehmen Sie Kontakt auf</h1>
        <p className="text-lg text-violet-200 max-w-2xl mx-auto">
          Haben Sie Fragen oder möchten Sie ein unverbindliches Angebot für Ihre Entrümpelung? Wir sind für Sie da!
        </p>
      </div>

      <div className="grid md:grid-cols-2 gap-12 items-start">
        <motion.div
          initial={{ opacity: 0, x: -50 }}
          whileInView={{ opacity: 1, x: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="space-y-8"
        >
          <div className="flex items-center space-x-4 p-4 bg-gray-800/60 rounded-lg">
            <div className="bg-violet-500/20 p-3 rounded-full"><Phone className="h-6 w-6 text-violet-400" /></div>
            <div>
              <p className="text-lg font-semibold text-white">Telefon</p>
              <a href="tel:+49123456789" className="text-gray-300 hover:text-violet-300">+49 123 456 789</a>
            </div>
          </div>
          <div className="flex items-center space-x-4 p-4 bg-gray-800/60 rounded-lg">
            <div className="bg-violet-500/20 p-3 rounded-full"><Mail className="h-6 w-6 text-violet-400" /></div>
            <div>
              <p className="text-lg font-semibold text-white">E-Mail</p>
              <a href="mailto:kontakt@yla-umzug.de" className="text-gray-300 hover:text-violet-300">kontakt@yla-umzug.de</a>
            </div>
          </div>
          <div className="flex items-center space-x-4 p-4 bg-gray-800/60 rounded-lg">
            <div className="bg-violet-500/20 p-3 rounded-full"><MapPin className="h-6 w-6 text-violet-400" /></div>
            <div>
              <p className="text-lg font-semibold text-white">Einsatzgebiet</p>
              <p className="text-gray-300">Saarland & Rheinland-Pfalz (u.a. Saarbrücken, Trier, Kaiserslautern)</p>
            </div>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, x: 50 }}
          whileInView={{ opacity: 1, x: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="bg-gray-800/60 p-8 rounded-xl shadow-lg"
        >
          <form onSubmit={handleSubmit} className="space-y-6">
            <div>
              <label htmlFor="name" className="block text-sm font-medium text-gray-300 mb-2">Name</label>
              <input type="text" id="name" name="name" className="w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-4 text-white focus:ring-violet-500 focus:border-violet-500" required />
            </div>
            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-300 mb-2">E-Mail</label>
              <input type="email" id="email" name="email" className="w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-4 text-white focus:ring-violet-500 focus:border-violet-500" required />
            </div>
            <div>
              <label htmlFor="message" className="block text-sm font-medium text-gray-300 mb-2">Ihre Nachricht (z.B. Art der Entrümpelung, Ort)</label>
              <textarea id="message" name="message" rows="4" className="w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-4 text-white focus:ring-violet-500 focus:border-violet-500" required></textarea>
            </div>
            <Button type="submit" size="lg" className="w-full bg-violet-600 hover:bg-violet-700 text-white font-bold py-3 px-4 rounded-md transition-transform transform hover:scale-105">
              Anfrage senden
            </Button>
          </form>
        </motion.div>
      </div>
    </motion.div>
  );
};

export default ContactPage;