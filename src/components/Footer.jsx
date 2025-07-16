import React from 'react';
import { Sparkles } from 'lucide-react';

const Footer = () => {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-gray-800 text-gray-400 py-8">
      <div className="container mx-auto px-6 text-center">
        <div className="flex justify-center items-center mb-4">
          <Sparkles className="h-6 w-6 text-violet-400 mr-2" />
          <p className="font-bold text-lg text-white">YLA Umzug</p>
        </div>
        <p className="text-sm">Ihr Profi für Entrümpelung & Entsorgung im Saarland und Rheinland-Pfalz.</p>
        <p className="text-sm mt-2">&copy; {currentYear} YLA Umzug. Alle Rechte vorbehalten.</p>
      </div>
    </footer>
  );
};

export default Footer;