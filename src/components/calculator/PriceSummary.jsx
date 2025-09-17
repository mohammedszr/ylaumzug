import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Calculator, Euro, CheckCircle, AlertCircle, RefreshCw, Star, Sparkles, TrendingUp, Shield, Clock, Heart } from 'lucide-react';
import { calculatorApi, ApiError } from '@/lib/api';
import { toast } from '@/components/ui/use-toast';

const PriceSummary = ({ data, updateData }) => {
  const [estimatedPrice, setEstimatedPrice] = useState(null);
  const [isCalculating, setIsCalculating] = useState(false);
  const [error, setError] = useState(null);

  // Safety check for data
  if (!data || !data.selectedServices) {
    return (
      <div className="text-center py-8">
        <p className="text-white">Daten werden geladen...</p>
      </div>
    );
  }

  // Calculate estimate using Laravel API
  const calculateEstimate = async () => {
    if (!data.selectedServices || data.selectedServices.length === 0) {
      return;
    }

    setIsCalculating(true);
    setError(null);
    
    try {
      const response = await calculatorApi.calculatePricing(data);
      
      if (response.success) {
        setEstimatedPrice({
          total: response.pricing.total,
          breakdown: response.pricing.breakdown,
          currency: response.currency || 'EUR',
          disclaimer: response.disclaimer
        });
        
        // Update calculator data with pricing for later use
        if (updateData) {
          updateData({ pricing: response.pricing }, 'pricing');
        }
      } else {
        throw new Error(response.message || 'Calculation failed');
      }
    } catch (err) {
      console.error('Pricing calculation error:', err);
      setError(err);
      
      // Show user-friendly error message and provide fallback pricing
      if (err instanceof ApiError) {
        toast({
          title: "Berechnungsfehler",
          description: err.message,
          variant: "destructive",
        });
      } else {
        toast({
          title: "Berechnungsfehler", 
          description: "Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es erneut.",
          variant: "destructive",
        });
      }
      
      // Provide fallback pricing so user can still proceed
      setEstimatedPrice({
        total: 'Auf Anfrage',
        breakdown: [
          {
            service: data.selectedServices[0] || 'Service',
            details: 'Preis wird nach Besichtigung ermittelt',
            price: 'Auf Anfrage'
          }
        ],
        currency: 'EUR',
        disclaimer: 'Der genaue Preis wird nach einer kostenlosen Besichtigung vor Ort ermittelt.'
      });
    } finally {
      setIsCalculating(false);
    }
  };

  // Retry calculation
  const retryCalculation = () => {
    calculateEstimate();
  };





  // Use React.useEffect to calculate estimate when component mounts
  React.useEffect(() => {
    if (data.selectedServices.length > 0 && !estimatedPrice && !isCalculating) {
      // Add a small delay to prevent immediate execution issues
      const timer = setTimeout(() => {
        calculateEstimate();
      }, 100);
      
      return () => clearTimeout(timer);
    }
  }, [data.selectedServices]);

  return (
    <div className="space-y-6 sm:space-y-8">
      {/* Beautiful header with animations */}
      <motion.div 
        initial={{ opacity: 0, y: -20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
        className="text-center"
      >
        <motion.div
          initial={{ scale: 0.8 }}
          animate={{ scale: 1 }}
          transition={{ duration: 0.5, delay: 0.2 }}
          className="inline-flex items-center space-x-2 mb-4"
        >
          <div className="bg-gradient-to-r from-green-500 to-emerald-600 p-2 rounded-xl">
            <TrendingUp className="w-5 h-5 text-white" />
          </div>
          <h2 className="text-2xl sm:text-3xl font-bold text-white">
            Ihre Kostenübersicht
          </h2>
        </motion.div>
        <motion.p 
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="text-sm sm:text-base text-violet-200/80 max-w-md mx-auto"
        >
          Transparente Preise • Keine versteckten Kosten • Unverbindlich
        </motion.p>
      </motion.div>

      {/* Beautiful Selected Services Summary */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.3 }}
        className="bg-gradient-to-r from-blue-500/20 to-cyan-500/20 backdrop-blur-sm border border-blue-500/30 rounded-2xl p-4 sm:p-6"
      >
        <div className="flex items-center space-x-3 mb-4">
          <div className="bg-gradient-to-r from-blue-500 to-cyan-600 p-2 rounded-xl">
            <Star className="w-5 h-5 text-white" />
          </div>
          <h3 className="text-lg font-bold text-white">Gewählte Services</h3>
        </div>
        
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
          {data.selectedServices.map((service, index) => (
            <motion.div
              key={service}
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.4, delay: 0.4 + index * 0.1 }}
              className="flex items-center space-x-3 bg-white/10 backdrop-blur-sm rounded-xl p-3"
            >
              <div className="bg-gradient-to-r from-green-500 to-emerald-600 p-1.5 rounded-lg">
                <CheckCircle className="h-4 w-4 text-white" />
              </div>
              <span className="capitalize text-white font-medium">{service}</span>
            </motion.div>
          ))}
        </div>
      </motion.div>

      {/* Beautiful Price Calculation */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.5 }}
        className="bg-gray-800/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl overflow-hidden"
      >
        <div className="p-4 sm:p-6 border-b border-gray-700/50">
          <div className="flex items-center space-x-3">
            <div className="bg-gradient-to-r from-violet-500 to-purple-600 p-2 rounded-xl">
              <Calculator className="w-5 h-5 text-white" />
            </div>
            <h3 className="text-lg font-bold text-white">Kostenaufstellung</h3>
          </div>
        </div>

        <div className="p-4 sm:p-6">
          <AnimatePresence mode="wait">
            {isCalculating ? (
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                className="text-center py-8 sm:py-12"
              >
                <motion.div
                  animate={{ rotate: 360 }}
                  transition={{ duration: 2, repeat: Infinity, ease: "linear" }}
                  className="relative mx-auto mb-6"
                >
                  <div className="w-12 h-12 sm:w-16 sm:h-16 rounded-full border-4 border-violet-500/30 border-t-violet-500 mx-auto"></div>
                  <motion.div
                    animate={{ scale: [1, 1.2, 1] }}
                    transition={{ duration: 1.5, repeat: Infinity }}
                    className="absolute inset-0 flex items-center justify-center"
                  >
                    <Sparkles className="w-5 h-5 sm:w-6 sm:h-6 text-violet-400" />
                  </motion.div>
                </motion.div>
                <p className="text-violet-200 font-medium">Berechnung läuft...</p>
                <p className="text-violet-200/60 text-sm mt-2">Wir erstellen Ihr persönliches Angebot</p>
              </motion.div>
            ) : error ? (
              <motion.div
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.9 }}
                className="text-center py-8 sm:py-12"
              >
                <div className="bg-gradient-to-r from-red-500 to-pink-600 p-3 rounded-2xl w-fit mx-auto mb-4">
                  <AlertCircle className="h-8 w-8 sm:h-10 sm:w-10 text-white" />
                </div>
                <p className="text-red-300 mb-6 font-medium">
                  {error instanceof ApiError ? error.message : 'Fehler bei der Berechnung'}
                </p>
                <motion.button
                  whileHover={{ scale: 1.05 }}
                  whileTap={{ scale: 0.95 }}
                  onClick={retryCalculation}
                  className="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-xl hover:from-violet-700 hover:to-purple-700 transition-all duration-300 font-medium touch-manipulation"
                >
                  <RefreshCw className="h-4 w-4" />
                  <span>Erneut versuchen</span>
                </motion.button>
              </motion.div>
            ) : estimatedPrice ? (
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -20 }}
                className="space-y-4"
              >
                {/* Breakdown items */}
                <div className="space-y-3">
                  {estimatedPrice.breakdown.map((item, index) => (
                    <motion.div
                      key={index}
                      initial={{ opacity: 0, x: -20 }}
                      animate={{ opacity: 1, x: 0 }}
                      transition={{ duration: 0.4, delay: index * 0.1 }}
                      className="flex justify-between items-start py-3 px-4 bg-gray-700/30 rounded-xl border border-gray-600/30"
                    >
                      <div className="flex-1">
                        <div className="font-medium text-white">{item.service}</div>
                        {item.details && (
                          <div className="text-sm text-gray-400 mt-1">
                            {Array.isArray(item.details) ? item.details.join(', ') : item.details}
                          </div>
                        )}
                      </div>
                      <div className="font-bold text-green-400 text-lg ml-4">{item.price}€</div>
                    </motion.div>
                  ))}
                </div>
                
                {/* Total price with beautiful animation */}
                <motion.div
                  initial={{ opacity: 0, scale: 0.9 }}
                  animate={{ opacity: 1, scale: 1 }}
                  transition={{ duration: 0.5, delay: 0.3 }}
                  className="bg-gradient-to-r from-green-500/20 to-emerald-500/20 backdrop-blur-sm border border-green-500/30 rounded-2xl p-4 sm:p-6 mt-6"
                >
                  <div className="flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
                    <div className="text-lg sm:text-xl font-bold text-white">Geschätzter Gesamtpreis:</div>
                    <motion.div
                      initial={{ scale: 0 }}
                      animate={{ scale: 1 }}
                      transition={{ duration: 0.5, delay: 0.5, ease: "backOut" }}
                      className="flex items-center text-3xl sm:text-4xl font-bold text-green-400"
                    >
                      <Euro className="h-6 w-6 sm:h-8 sm:w-8 mr-2" />
                      {estimatedPrice.total}
                    </motion.div>
                  </div>
                </motion.div>
              </motion.div>
            ) : (
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                className="text-center py-8"
              >
                <p className="text-gray-400">Keine Services ausgewählt</p>
              </motion.div>
            )}
          </AnimatePresence>
        </div>
      </motion.div>

      {/* Beautiful Disclaimer */}
      <AnimatePresence>
        {estimatedPrice && (
          <motion.div
            initial={{ opacity: 0, y: 20, scale: 0.95 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: -20, scale: 0.95 }}
            transition={{ duration: 0.4 }}
            className="bg-gradient-to-r from-yellow-500/20 to-orange-500/20 backdrop-blur-sm border border-yellow-500/30 rounded-2xl p-4 sm:p-6"
          >
            <div className="flex items-start space-x-3">
              <div className="bg-gradient-to-r from-yellow-500 to-orange-600 p-2 rounded-xl flex-shrink-0">
                <Shield className="h-5 w-5 text-white" />
              </div>
              <div>
                <h4 className="font-bold text-yellow-200 mb-2">Wichtiger Hinweis</h4>
                <p className="text-sm text-yellow-200/80 leading-relaxed">
                  {estimatedPrice.disclaimer || 'Dies ist eine unverbindliche Schätzung basierend auf Ihren Angaben. Das finale Angebot erhalten Sie nach unserer kostenlosen Besichtigung vor Ort.'}
                </p>
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Beautiful Next Step Call-to-Action */}
      <AnimatePresence>
        {estimatedPrice && (
          <motion.div
            initial={{ opacity: 0, y: 20, scale: 0.95 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: -20, scale: 0.95 }}
            transition={{ duration: 0.5, delay: 0.2 }}
            className="bg-gradient-to-r from-green-500/20 to-emerald-500/20 backdrop-blur-sm border border-green-500/30 rounded-2xl p-6 sm:p-8 text-center"
          >
            <motion.div
              initial={{ scale: 0 }}
              animate={{ scale: 1 }}
              transition={{ duration: 0.5, delay: 0.4, ease: "backOut" }}
              className="inline-flex items-center space-x-2 mb-4"
            >
              <div className="bg-gradient-to-r from-green-500 to-emerald-600 p-2 rounded-xl">
                <Heart className="w-5 h-5 text-white" />
              </div>
              <h3 className="text-xl font-bold text-white">
                Gefällt Ihnen unser Angebot?
              </h3>
            </motion.div>
            
            <motion.p 
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ duration: 0.5, delay: 0.6 }}
              className="text-green-200/80 mb-6 max-w-md mx-auto"
            >
              Klicken Sie auf "Weiter" um Ihre Kontaktdaten einzugeben und ein kostenloses, 
              unverbindliches Angebot per E-Mail zu erhalten.
            </motion.p>
            
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
              <motion.div
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4, delay: 0.7 }}
                className="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-2"
              >
                <div className="bg-gradient-to-r from-green-500 to-emerald-600 p-1.5 rounded-lg">
                  <CheckCircle className="h-4 w-4 text-white" />
                </div>
                <span className="text-green-200 font-medium">Kostenlose Besichtigung</span>
              </motion.div>
              
              <motion.div
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4, delay: 0.8 }}
                className="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-2"
              >
                <div className="bg-gradient-to-r from-blue-500 to-cyan-600 p-1.5 rounded-lg">
                  <Shield className="h-4 w-4 text-white" />
                </div>
                <span className="text-green-200 font-medium">Unverbindliches Angebot</span>
              </motion.div>
              
              <motion.div
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4, delay: 0.9 }}
                className="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-2"
              >
                <div className="bg-gradient-to-r from-violet-500 to-purple-600 p-1.5 rounded-lg">
                  <Clock className="h-4 w-4 text-white" />
                </div>
                <span className="text-green-200 font-medium">24h Rückmeldung</span>
              </motion.div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default PriceSummary;