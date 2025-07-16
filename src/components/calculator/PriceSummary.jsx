import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Calculator, Euro, CheckCircle } from 'lucide-react';

const PriceSummary = ({ data, updateData }) => {
  const [estimatedPrice, setEstimatedPrice] = useState(null);
  const [isCalculating, setIsCalculating] = useState(false);

  // Simple price estimation logic (will be replaced with Laravel API)
  const calculateEstimate = () => {
    setIsCalculating(true);
    
    // Simulate API call
    setTimeout(() => {
      let basePrice = 0;
      let breakdown = [];

      // Calculate based on selected services
      if (data.selectedServices.includes('umzug')) {
        const movingPrice = calculateMovingPrice();
        basePrice += movingPrice.total;
        breakdown.push({ service: 'Umzug', price: movingPrice.total, details: movingPrice.details });
      }

      if (data.selectedServices.includes('entruempelung')) {
        const declutterPrice = calculateDeclutterPrice();
        basePrice += declutterPrice.total;
        breakdown.push({ service: 'Entrümpelung', price: declutterPrice.total, details: declutterPrice.details });
      }

      if (data.selectedServices.includes('putzservice')) {
        const cleaningPrice = calculateCleaningPrice();
        basePrice += cleaningPrice.total;
        breakdown.push({ service: 'Putzservice', price: cleaningPrice.total, details: cleaningPrice.details });
      }

      // Express surcharge
      if (data.generalInfo.urgency === 'express') {
        const expressFee = Math.round(basePrice * 0.2);
        basePrice += expressFee;
        breakdown.push({ service: 'Express-Zuschlag', price: expressFee, details: '20% Aufschlag' });
      }

      setEstimatedPrice({
        total: basePrice,
        breakdown: breakdown,
        currency: 'EUR'
      });
      setIsCalculating(false);
    }, 2000);
  };

  const calculateMovingPrice = () => {
    const movingData = data.movingDetails || {};
    let price = 0;
    let details = [];

    // Base price by apartment size
    const size = parseInt(movingData.apartmentSize) || 50;
    const basePrice = Math.max(300, size * 8);
    price += basePrice;
    details.push(`Grundpreis (${size}m²): ${basePrice}€`);

    // Distance calculation (simplified)
    const distancePrice = 150; // Placeholder
    price += distancePrice;
    details.push(`Entfernung: ${distancePrice}€`);

    // Additional services
    if (movingData.additionalServices?.includes('packing')) {
      const packingPrice = 200;
      price += packingPrice;
      details.push(`Verpackungsservice: ${packingPrice}€`);
    }

    return { total: price, details };
  };

  const calculateDeclutterPrice = () => {
    const declutterData = data.declutterDetails || {};
    let price = 0;
    let details = [];

    // Base price by volume
    const volumePrices = {
      low: 300,
      medium: 600,
      high: 1200,
      extreme: 2000
    };
    
    const basePrice = volumePrices[declutterData.volume] || 400;
    price += basePrice;
    details.push(`Volumen (${declutterData.volume}): ${basePrice}€`);

    // Clean handover
    if (declutterData.cleanHandover === 'yes') {
      const cleaningPrice = 150;
      price += cleaningPrice;
      details.push(`Besenreine Übergabe: ${cleaningPrice}€`);
    }

    return { total: price, details };
  };

  const calculateCleaningPrice = () => {
    const cleaningData = data.cleaningDetails || {};
    let price = 0;
    let details = [];

    // Base price by size and intensity
    const size = parseInt(cleaningData.size) || 50;
    const intensityMultipliers = {
      normal: 3,
      deep: 5,
      construction: 7
    };
    
    const multiplier = intensityMultipliers[cleaningData.cleaningIntensity] || 3;
    const basePrice = size * multiplier;
    price += basePrice;
    details.push(`${cleaningData.cleaningIntensity} (${size}m²): ${basePrice}€`);

    return { total: price, details };
  };



  useEffect(() => {
    if (data.selectedServices.length > 0) {
      calculateEstimate();
    }
  }, [data]);

  return (
    <div className="space-y-6">
      <div className="text-center">
        <h2 className="text-2xl font-bold text-white mb-2">
          Ihre Kostenübersicht
        </h2>
        <p className="text-gray-300">
          Unverbindliche Schätzung basierend auf Ihren Angaben
        </p>
      </div>

      {/* Selected Services Summary */}
      <Card>
        <CardHeader>
          <CardTitle>Gewählte Services</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-2">
            {data.selectedServices.map(service => (
              <div key={service} className="flex items-center space-x-2">
                <CheckCircle className="h-5 w-5 text-green-500" />
                <span className="capitalize">{service}</span>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Price Calculation */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Calculator className="h-5 w-5 text-violet-600" />
            <span>Kostenaufstellung</span>
          </CardTitle>
        </CardHeader>
        <CardContent>
          {isCalculating ? (
            <div className="text-center py-8">
              <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-violet-600 mx-auto mb-4"></div>
              <p className="text-gray-600">Berechnung läuft...</p>
            </div>
          ) : estimatedPrice ? (
            <div className="space-y-4">
              {estimatedPrice.breakdown.map((item, index) => (
                <div key={index} className="flex justify-between items-start py-2 border-b border-gray-200">
                  <div>
                    <div className="font-medium">{item.service}</div>
                    {item.details && (
                      <div className="text-sm text-gray-600">
                        {Array.isArray(item.details) ? item.details.join(', ') : item.details}
                      </div>
                    )}
                  </div>
                  <div className="font-medium">{item.price}€</div>
                </div>
              ))}
              
              <div className="flex justify-between items-center pt-4 border-t-2 border-violet-200">
                <div className="text-xl font-bold">Geschätzter Gesamtpreis:</div>
                <div className="text-2xl font-bold text-violet-600 flex items-center">
                  <Euro className="h-6 w-6 mr-1" />
                  {estimatedPrice.total}
                </div>
              </div>
            </div>
          ) : (
            <p className="text-gray-600 text-center py-4">
              Keine Services ausgewählt
            </p>
          )}
        </CardContent>
      </Card>

      {/* Disclaimer */}
      {estimatedPrice && (
        <Card className="bg-yellow-50 border-yellow-200">
          <CardContent className="p-4">
            <p className="text-sm text-yellow-800">
              <strong>Wichtiger Hinweis:</strong> Dies ist eine unverbindliche Schätzung basierend auf Ihren Angaben. 
              Das finale Angebot erhalten Sie nach unserer kostenlosen Besichtigung vor Ort.
            </p>
          </CardContent>
        </Card>
      )}

      {/* Next Step Call-to-Action */}
      {estimatedPrice && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="text-center space-y-4"
        >
          <div className="bg-green-50 border border-green-200 rounded-lg p-6">
            <h3 className="text-lg font-semibold text-green-800 mb-2">
              Gefällt Ihnen unser Angebot?
            </h3>
            <p className="text-green-700 mb-4">
              Klicken Sie auf "Weiter" um Ihre Kontaktdaten einzugeben und ein kostenloses, 
              unverbindliches Angebot per E-Mail zu erhalten.
            </p>
            <div className="flex items-center justify-center space-x-2 text-sm text-green-600">
              <CheckCircle className="h-4 w-4" />
              <span>Kostenlose Besichtigung vor Ort</span>
              <CheckCircle className="h-4 w-4" />
              <span>Unverbindliches Angebot</span>
              <CheckCircle className="h-4 w-4" />
              <span>Festpreisgarantie</span>
            </div>
          </div>
        </motion.div>
      )}
    </div>
  );
};

export default PriceSummary;