import React from 'react';
import { Link } from 'react-router-dom';
import { Sparkles } from 'lucide-react';

const Footer = () => {
  return (
    <footer className="bg-gray-800 text-gray-400 py-8">
      <div className="container mx-auto px-6">
        <div className="text-center mb-6">
          <div className="flex justify-center items-center mb-4">
            <Sparkles className="h-6 w-6 text-violet-400 mr-2" />
            <p className="font-bold text-lg text-white">YLA Umzug</p>
          </div>
          <p className="text-sm">Ihr Profi für Entrümpelung & Entsorgung im Saarland und Rheinland-Pfalz.</p>
          <p className="text-sm mt-1">Seit 2017 Ihr zuverlässiger Partner</p>
        </div>
        
        <div className="flex flex-wrap justify-center items-center gap-4 mb-4 text-sm">
          <Link to="/impressum" className="hover:text-violet-300 transition-colors">
            Impressum
          </Link>
          <span className="text-gray-600">|</span>
          <Link to="/datenschutz" className="hover:text-violet-300 transition-colors">
            Datenschutz
          </Link>
          <span className="text-gray-600">|</span>
          <Link to="/agb" className="hover:text-violet-300 transition-colors">
            AGB
          </Link>
        </div>
        
        <div className="text-center">
          <p className="text-sm">&copy; 2025 YLA Umzug. Alle Rechte vorbehalten.</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;