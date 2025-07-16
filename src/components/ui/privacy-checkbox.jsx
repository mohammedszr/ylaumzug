import React from 'react';
import { Link } from 'react-router-dom';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';

const PrivacyCheckbox = ({ checked, onChange, required = true, className = "" }) => {
  return (
    <div className={`flex items-start space-x-3 ${className}`}>
      <Checkbox
        id="privacy-checkbox"
        checked={checked}
        onCheckedChange={onChange}
        required={required}
        className="mt-1 flex-shrink-0"
      />
      <Label 
        htmlFor="privacy-checkbox" 
        className="text-sm text-gray-300 leading-relaxed cursor-pointer"
      >
        Ich habe die{' '}
        <Link 
          to="/datenschutz" 
          className="text-violet-400 hover:text-violet-300 underline"
          target="_blank"
          rel="noopener noreferrer"
        >
          Datenschutzerklärung
        </Link>{' '}
        gelesen und stimme der Verarbeitung meiner personenbezogenen Daten zu.
        {required && <span className="text-red-400 ml-1">*</span>}
      </Label>
    </div>
  );
};

export default PrivacyCheckbox;