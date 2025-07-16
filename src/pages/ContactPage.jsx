import React, { useState } from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/use-toast';
import PrivacyCheckbox from '@/components/ui/privacy-checkbox';
import { Phone, Mail, MapPin, MessageCircle } from 'lucide-react';

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
  const [privacyAccepted, setPrivacyAccepted] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    message: '',
    honeypot: '' // Spam protection
  });

  const handleInputChange = (field, value) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    
    // Spam protection - honeypot field should be empty
    if (formData.honeypot) {
      return; // Silent fail for bots
    }
    
    // Validate required fields
    if (!formData.name || !formData.email || !formData.message) {
      toast({
        title: "Fehlende Angaben",
        description: "Bitte füllen Sie alle Pflichtfelder aus.",
        variant: "destructive",
      });
      return;
    }
    
    if (!privacyAccepted) {
      toast({
        title: "Datenschutz erforderlich",
        description: "Bitte stimmen Sie der Datenschutzerklärung zu, um fortzufahren.",
        variant: "destructive",
      });
      return;
    }

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
        <script type="application/ld+json">
          {JSON.stringify({
            "@context": "https://schema.org",
            "@type": "LocalBusiness",
            "name": "YLA Umzug",
            "address": {
              "@type": "PostalAddress",
              "streetAddress": "Weißenburger Str. 15",
              "addressLocality": "Saarbrücken",
              "addressRegion": "Saarland",
              "postalCode": "66113",
              "addressCountry": "DE"
            },
            "geo": {
              "@type": "GeoCoordinates",
              "latitude": "49.2401",
              "longitude": "6.9969"
            },
            "telephone": "+49-1575-0693353",
            "email": "info@yla-umzug.de",
            "url": "https://yla-umzug.de",
            "areaServed": [
              {
                "@type": "City",
                "name": "Saarbrücken"
              },
              {
                "@type": "City", 
                "name": "Trier"
              },
              {
                "@type": "City",
                "name": "Kaiserslautern"
              },
              {
                "@type": "State",
                "name": "Saarland"
              },
              {
                "@type": "State",
                "name": "Rheinland-Pfalz"
              }
            ],
            "serviceType": [
              "Umzugsservice",
              "Entrümpelung", 
              "Haushaltsauflösung",
              "Hausreinigung",
              "Messi-Wohnung Räumung"
            ],
            "priceRange": "€€",
            "openingHours": "Mo-Sa 08:00-18:00"
          })}
        </script>
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
              <a href="tel:+4915750693353" className="text-gray-300 hover:text-violet-300">+49 1575 0693353</a>
            </div>
          </div>
          <div className="flex items-center space-x-4 p-4 bg-gray-800/60 rounded-lg">
            <div className="bg-violet-500/20 p-3 rounded-full"><Mail className="h-6 w-6 text-violet-400" /></div>
            <div>
              <p className="text-lg font-semibold text-white">E-Mail</p>
              <a href="mailto:info@yla-umzug.de" className="text-gray-300 hover:text-violet-300">info@yla-umzug.de</a>
            </div>
          </div>
          <div className="flex items-center space-x-4 p-4 bg-green-600/20 rounded-lg border border-green-500/30">
            <div className="bg-green-500/20 p-3 rounded-full"><MessageCircle className="h-6 w-6 text-green-400" /></div>
            <div>
              <p className="text-lg font-semibold text-white">WhatsApp</p>
              <a 
                href="https://wa.me/4915750693353?text=Hallo!%20Ich%20interessiere%20mich%20f%C3%BCr%20Ihre%20Dienstleistungen.%20K%C3%B6nnen%20Sie%20mir%20weiterhelfen%3F" 
                target="_blank" 
                rel="noopener noreferrer"
                className="text-green-300 hover:text-green-200 flex items-center"
              >
                +49 1575 0693353 <MessageCircle size={16} className="ml-2" />
              </a>
            </div>
          </div>
          <div className="flex items-center space-x-4 p-4 bg-gray-800/60 rounded-lg">
            <div className="bg-violet-500/20 p-3 rounded-full"><MapPin className="h-6 w-6 text-violet-400" /></div>
            <div>
              <p className="text-lg font-semibold text-white">Einsatzgebiet</p>
              <p className="text-gray-300">Saarland & Rheinland-Pfalz (u.a. Saarbrücken, Trier, Kaiserslautern)</p>
            </div>
          </div>
          
          {/* Google Maps Integration */}
          <div className="bg-blue-600/20 border border-blue-500/30 p-4 rounded-lg">
            <div className="flex items-center space-x-4">
              <div className="bg-blue-500/20 p-3 rounded-full"><MapPin className="h-6 w-6 text-blue-400" /></div>
              <div className="flex-1">
                <p className="text-lg font-semibold text-white">Unser Standort</p>
                <p className="text-gray-300 text-sm mb-3">Weißenburger Str. 15, 66113 Saarbrücken</p>
                <a 
                  href="https://www.google.com/maps/place/YLA+Umzug,+Weißenburger+Str.+15,+66113+Saarbrücken"
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200"
                >
                  <MapPin size={18} className="mr-2" />
                  Auf Google Maps öffnen
                </a>
              </div>
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
            {/* Honeypot field for spam protection - hidden from users */}
            <div style={{ display: 'none' }}>
              <label htmlFor="website">Website (leave empty)</label>
              <input 
                type="text" 
                id="website" 
                name="website" 
                value={formData.honeypot}
                onChange={(e) => handleInputChange('honeypot', e.target.value)}
                tabIndex="-1"
                autoComplete="off"
              />
            </div>
            
            <div>
              <label htmlFor="name" className="block text-sm font-medium text-gray-300 mb-2">Name *</label>
              <input 
                type="text" 
                id="name" 
                name="name" 
                value={formData.name}
                onChange={(e) => handleInputChange('name', e.target.value)}
                className="w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-4 text-white focus:ring-violet-500 focus:border-violet-500" 
                required 
              />
            </div>
            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-300 mb-2">E-Mail *</label>
              <input 
                type="email" 
                id="email" 
                name="email" 
                value={formData.email}
                onChange={(e) => handleInputChange('email', e.target.value)}
                className="w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-4 text-white focus:ring-violet-500 focus:border-violet-500" 
                required 
              />
            </div>
            <div>
              <label htmlFor="message" className="block text-sm font-medium text-gray-300 mb-2">Ihre Nachricht (z.B. Art der Entrümpelung, Ort) *</label>
              <textarea 
                id="message" 
                name="message" 
                rows="4" 
                value={formData.message}
                onChange={(e) => handleInputChange('message', e.target.value)}
                className="w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-4 text-white focus:ring-violet-500 focus:border-violet-500" 
                required
              ></textarea>
            </div>
            
            <PrivacyCheckbox
              checked={privacyAccepted}
              onCheckedChange={setPrivacyAccepted}
              id="contact-privacy"
            />
            
            <Button 
              type="submit" 
              size="lg" 
              disabled={!privacyAccepted || !formData.name || !formData.email || !formData.message}
              className="w-full bg-violet-600 hover:bg-violet-700 text-white font-bold py-3 px-4 rounded-md transition-transform transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
              Anfrage senden
            </Button>
          </form>
        </motion.div>
      </div>
    </motion.div>
  );
};

export default ContactPage;