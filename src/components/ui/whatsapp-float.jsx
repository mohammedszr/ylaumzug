import React, { useState } from 'react';
import { MessageCircle, X } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';

const WhatsAppFloat = () => {
  const [isOpen, setIsOpen] = useState(false);
  
  const whatsappNumber = "4915750693353";
  const defaultMessage = encodeURIComponent("Hallo! Ich interessiere mich für Ihre Dienstleistungen. Können Sie mir weiterhelfen?");
  
  const quickMessages = [
    {
      text: "Kostenvoranschlag anfordern",
      message: encodeURIComponent("Hallo! Ich benötige einen Kostenvoranschlag für einen Umzug/Entrümpelung. Können Sie mir ein Angebot erstellen?")
    },
    {
      text: "Hartz-IV Umzug",
      message: encodeURIComponent("Hallo! Ich benötige einen Kostenvoranschlag für einen Hartz-IV-Umzug für das Jobcenter. Können Sie mir dabei helfen?")
    },
    {
      text: "Terminvereinbarung",
      message: encodeURIComponent("Hallo! Ich möchte gerne einen Termin für eine Besichtigung vereinbaren. Wann haben Sie Zeit?")
    },
    {
      text: "Allgemeine Fragen",
      message: defaultMessage
    }
  ];

  const handleQuickMessage = (message) => {
    const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${message}`;
    window.open(whatsappUrl, '_blank');
    setIsOpen(false);
  };

  return (
    <div className="fixed bottom-6 right-6 z-50">
      <AnimatePresence>
        {isOpen && (
          <motion.div
            initial={{ opacity: 0, y: 20, scale: 0.8 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: 20, scale: 0.8 }}
            className="mb-4 bg-white rounded-lg shadow-xl p-4 w-72"
          >
            <div className="flex items-center justify-between mb-3">
              <h3 className="font-semibold text-gray-800">WhatsApp Kontakt</h3>
              <button
                onClick={() => setIsOpen(false)}
                className="text-gray-500 hover:text-gray-700"
              >
                <X size={18} />
              </button>
            </div>
            <p className="text-sm text-gray-600 mb-3">
              Wählen Sie eine Option oder schreiben Sie uns direkt:
            </p>
            <div className="space-y-2">
              {quickMessages.map((item, index) => (
                <button
                  key={index}
                  onClick={() => handleQuickMessage(item.message)}
                  className="w-full text-left p-2 text-sm bg-green-50 hover:bg-green-100 rounded-lg transition-colors duration-200 text-gray-700"
                >
                  {item.text}
                </button>
              ))}
            </div>
            <div className="mt-3 pt-3 border-t border-gray-200">
              <p className="text-xs text-gray-500 text-center">
                +49 1575 0693353
              </p>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
      
      <motion.button
        whileHover={{ scale: 1.1 }}
        whileTap={{ scale: 0.9 }}
        onClick={() => setIsOpen(!isOpen)}
        className="bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg transition-colors duration-200"
      >
        <MessageCircle size={24} />
      </motion.button>
    </div>
  );
};

export default WhatsAppFloat;