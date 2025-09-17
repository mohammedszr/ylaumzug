import React from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Check, Shield } from 'lucide-react';

const PrivacyCheckbox = ({ checked, onCheckedChange, required = true, className = "", id = "privacy-checkbox" }) => {
  return (
    <div className={`flex items-start space-x-3 ${className}`}>
      {/* Custom animated checkbox */}
      <motion.button
        type="button"
        onClick={() => onCheckedChange(!checked)}
        whileHover={{ scale: 1.05 }}
        whileTap={{ scale: 0.95 }}
        className={`
          relative w-6 h-6 mt-1 flex-shrink-0 rounded-lg border-2 transition-all duration-300 cursor-pointer
          ${checked 
            ? 'bg-gradient-to-r from-green-500 to-emerald-600 border-green-500 shadow-lg shadow-green-500/25' 
            : 'bg-gray-700/50 border-gray-600/50 hover:border-gray-500/50 hover:bg-gray-700/70'
          }
        `}
        aria-checked={checked}
        role="checkbox"
        id={id}
      >
        {/* Checkmark animation */}
        <motion.div
          initial={{ scale: 0, opacity: 0 }}
          animate={{ 
            scale: checked ? 1 : 0, 
            opacity: checked ? 1 : 0 
          }}
          transition={{ 
            duration: 0.2, 
            ease: "backOut" 
          }}
          className="absolute inset-0 flex items-center justify-center"
        >
          <Check className="w-4 h-4 text-white" strokeWidth={3} />
        </motion.div>
        
        {/* Ripple effect on click */}
        {checked && (
          <motion.div
            initial={{ scale: 0, opacity: 0.5 }}
            animate={{ scale: 2, opacity: 0 }}
            transition={{ duration: 0.4 }}
            className="absolute inset-0 bg-green-400 rounded-lg"
          />
        )}
      </motion.button>
      
      {/* Enhanced label with better styling */}
      <label 
        htmlFor={id} 
        className="text-sm text-gray-300 leading-relaxed cursor-pointer select-none"
      >
        <div className="flex items-start space-x-2">
          <div className="bg-gradient-to-r from-blue-500 to-cyan-600 p-1 rounded-lg flex-shrink-0 mt-0.5">
            <Shield className="h-3 w-3 text-white" />
          </div>
          <div>
            Ich habe die{' '}
            <Link 
              to="/datenschutz" 
              className="text-violet-400 hover:text-violet-300 underline font-medium transition-colors duration-200"
              target="_blank"
              rel="noopener noreferrer"
            >
              Datenschutzerklärung
            </Link>{' '}
            gelesen und stimme der Verarbeitung meiner personenbezogenen Daten zu.
            {required && <span className="text-red-400 ml-1 font-bold">*</span>}
          </div>
        </div>
      </label>
    </div>
  );
};

export default PrivacyCheckbox;