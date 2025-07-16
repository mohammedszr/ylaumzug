import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import PrivacyCheckbox from '@/components/ui/privacy-checkbox';
import { User, Mail, Phone, Calendar, MessageSquare, CheckCircle, Send, AlertCircle, Heart, Shield, Clock, Star } from 'lucide-react';
import { useToast } from '@/components/ui/use-toast';
import { quoteApi, ApiError } from '@/lib/api';

const GeneralInfo = ({ data, updateData }) => {
  const [formData, setFormData] = useState({
    name: '',
    phone: '',
    email: '',
    preferredDate: '',
    message: '',
    preferredContact: 'email',
    honeypot: '', // Spam protection
    ...data.generalInfo
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [privacyAccepted, setPrivacyAccepted] = useState(false);
  const { toast } = useToast();

  const handleChange = (field, value) => {
    const newData = { ...formData, [field]: value };
    setFormData(newData);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    // Spam protection - honeypot field should be empty
    if (formData.honeypot) {
      return; // Silent fail for bots
    }
    
    if (!formData.name || !formData.email || !formData.phone) return;
    
    if (!privacyAccepted) {
      toast({
        title: "Datenschutz erforderlich",
        description: "Bitte stimmen Sie der Datenschutzerklärung zu, um fortzufahren.",
        variant: "destructive",
      });
      return;
    }
    
    setIsSubmitting(true);
    
    try {
      // Prepare quote data for API submission
      const quoteData = {
        // Contact information
        name: formData.name,
        email: formData.email,
        phone: formData.phone,
        preferredDate: formData.preferredDate,
        message: formData.message,
        preferredContact: formData.preferredContact,
        
        // Calculator data
        selectedServices: data.selectedServices || [],
        movingDetails: data.movingDetails || {},
        cleaningDetails: data.cleaningDetails || {},
        declutterDetails: data.declutterDetails || {},
        pricing: data.pricing || null,
        
        // Metadata
        submittedAt: new Date().toISOString(),
        source: 'calculator'
      };

      const response = await quoteApi.submitQuote(quoteData);
      
      if (response.success) {
        toast({
          title: "Anfrage erfolgreich gesendet!",
          description: response.message || "Wir melden uns innerhalb von 24 Stunden bei Ihnen zurück.",
        });
      } else {
        throw new Error(response.message || 'Quote submission failed');
      }
    } catch (err) {
      console.error('Quote submission error:', err);
      
      // Show user-friendly error message
      if (err instanceof ApiError) {
        toast({
          title: "Fehler beim Senden",
          description: err.message,
          variant: "destructive",
        });
      } else {
        toast({
          title: "Fehler beim Senden",
          description: "Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es erneut oder kontaktieren Sie uns direkt.",
          variant: "destructive",
        });
      }
    } finally {
      setIsSubmitting(false);
    }
  };

  useEffect(() => {
    updateData(formData, 'generalInfo');
  }, [formData, updateData]);

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
            <Heart className="w-5 h-5 text-white" />
          </div>
          <h2 className="text-2xl sm:text-3xl font-bold text-white">
            Kostenloses Angebot anfordern
          </h2>
        </motion.div>
        <motion.p 
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="text-sm sm:text-base text-violet-200/80 max-w-md mx-auto"
        >
          Wir erstellen Ihnen ein maßgeschneidertes, unverbindliches Angebot
        </motion.p>
      </motion.div>

      {/* Beautiful Price Summary Display */}
      <AnimatePresence>
        {data.pricing && (
          <motion.div
            initial={{ opacity: 0, scale: 0.95, y: 20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.95, y: -20 }}
            transition={{ duration: 0.5, ease: [0.4, 0, 0.2, 1] }}
            className="bg-gradient-to-r from-green-500/20 to-emerald-500/20 backdrop-blur-sm border border-green-500/30 rounded-2xl p-6 text-center"
          >
            <motion.div
              initial={{ scale: 0 }}
              animate={{ scale: 1 }}
              transition={{ duration: 0.5, delay: 0.2, ease: "backOut" }}
              className="inline-flex items-center space-x-2 mb-4"
            >
              <div className="bg-gradient-to-r from-green-500 to-emerald-600 p-2 rounded-xl">
                <Star className="w-5 h-5 text-white" />
              </div>
              <h3 className="text-xl font-bold text-white">
                Ihre Kostenschätzung
              </h3>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5, delay: 0.4 }}
              className="text-4xl sm:text-5xl font-bold text-green-400 mb-2"
            >
              {data.pricing.total}€
            </motion.div>
            
            <motion.p 
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ duration: 0.5, delay: 0.6 }}
              className="text-green-200/80 text-sm"
            >
              Unverbindliche Schätzung • Finales Angebot nach kostenloser Besichtigung
            </motion.p>
          </motion.div>
        )}
      </AnimatePresence>

      <motion.form 
        onSubmit={handleSubmit} 
        className="space-y-6"
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.3 }}
      >
        {/* Beautiful form card with glassmorphism */}
        <div className="bg-gray-800/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-4 sm:p-6 lg:p-8">
          <div className="space-y-6">
            {/* Honeypot field for spam protection - hidden from users */}
            <div style={{ display: 'none' }}>
              <label htmlFor="website">Website (leave empty)</label>
              <input 
                type="text" 
                id="website" 
                name="website" 
                value={formData.honeypot}
                onChange={(e) => handleChange('honeypot', e.target.value)}
                tabIndex="-1"
                autoComplete="off"
              />
            </div>
            
            {/* Contact Information Section */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
              {/* Name Input */}
              <motion.div
                initial={{ opacity: 0, x: -20 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.5, delay: 0.4 }}
                className="space-y-2"
              >
                <Label htmlFor="name" className="flex items-center space-x-2 text-gray-200 font-medium">
                  <div className="bg-gradient-to-r from-violet-500 to-purple-600 p-1 rounded-lg">
                    <User className="h-3 w-3 text-white" />
                  </div>
                  <span>Vollständiger Name *</span>
                </Label>
                <Input
                  id="name"
                  value={formData.name}
                  onChange={(e) => handleChange('name', e.target.value)}
                  placeholder="Max Mustermann"
                  className="min-h-[48px] bg-gray-700/50 border-gray-600/50 text-white placeholder:text-gray-400 focus:border-violet-500/50 focus:bg-gray-700/70 transition-all duration-300 rounded-xl touch-manipulation"
                  autoComplete="name"
                  inputMode="text"
                  required
                />
              </motion.div>

              {/* Email Input */}
              <motion.div
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.5, delay: 0.5 }}
                className="space-y-2"
              >
                <Label htmlFor="email" className="flex items-center space-x-2 text-gray-200 font-medium">
                  <div className="bg-gradient-to-r from-blue-500 to-cyan-600 p-1 rounded-lg">
                    <Mail className="h-3 w-3 text-white" />
                  </div>
                  <span>E-Mail-Adresse *</span>
                </Label>
                <Input
                  id="email"
                  type="email"
                  value={formData.email}
                  onChange={(e) => handleChange('email', e.target.value)}
                  placeholder="max@beispiel.de"
                  className="min-h-[48px] bg-gray-700/50 border-gray-600/50 text-white placeholder:text-gray-400 focus:border-blue-500/50 focus:bg-gray-700/70 transition-all duration-300 rounded-xl touch-manipulation"
                  autoComplete="email"
                  inputMode="email"
                  required
                />
              </motion.div>

              {/* Phone Input */}
              <motion.div
                initial={{ opacity: 0, x: -20 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.5, delay: 0.6 }}
                className="space-y-2"
              >
                <Label htmlFor="phone" className="flex items-center space-x-2 text-gray-200 font-medium">
                  <div className="bg-gradient-to-r from-green-500 to-emerald-600 p-1 rounded-lg">
                    <Phone className="h-3 w-3 text-white" />
                  </div>
                  <span>Telefonnummer *</span>
                </Label>
                <Input
                  id="phone"
                  type="tel"
                  value={formData.phone}
                  onChange={(e) => handleChange('phone', e.target.value)}
                  placeholder="+49 1575 0693353"
                  className="min-h-[48px] bg-gray-700/50 border-gray-600/50 text-white placeholder:text-gray-400 focus:border-green-500/50 focus:bg-gray-700/70 transition-all duration-300 rounded-xl touch-manipulation"
                  autoComplete="tel"
                  inputMode="tel"
                  required
                />
              </motion.div>

              {/* Date Input */}
              <motion.div
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.5, delay: 0.7 }}
                className="space-y-2"
              >
                <Label htmlFor="preferredDate" className="flex items-center space-x-2 text-gray-200 font-medium">
                  <div className="bg-gradient-to-r from-orange-500 to-red-600 p-1 rounded-lg">
                    <Calendar className="h-3 w-3 text-white" />
                  </div>
                  <span>Wunschtermin (optional)</span>
                </Label>
                <Input
                  id="preferredDate"
                  type="date"
                  value={formData.preferredDate}
                  onChange={(e) => handleChange('preferredDate', e.target.value)}
                  className="min-h-[48px] bg-gray-700/50 border-gray-600/50 text-white focus:border-orange-500/50 focus:bg-gray-700/70 transition-all duration-300 rounded-xl touch-manipulation"
                  min={new Date().toISOString().split('T')[0]}
                />
              </motion.div>
            </div>

            {/* Contact Preference Section */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5, delay: 0.8 }}
              className="space-y-3"
            >
              <Label className="text-gray-200 font-medium">Bevorzugte Kontaktart</Label>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <motion.button
                  type="button"
                  whileHover={{ scale: 1.02 }}
                  whileTap={{ scale: 0.98 }}
                  onClick={() => handleChange('preferredContact', 'email')}
                  className={`min-h-[56px] p-4 rounded-xl border-2 transition-all duration-300 touch-manipulation ${
                    formData.preferredContact === 'email'
                      ? 'border-blue-500/50 bg-blue-500/20 text-blue-200'
                      : 'border-gray-600/50 bg-gray-700/30 text-gray-300 hover:border-gray-500/50 hover:bg-gray-700/50'
                  }`}
                >
                  <div className="flex items-center justify-center space-x-2">
                    <Mail className="h-5 w-5" />
                    <span className="font-medium">E-Mail bevorzugt</span>
                  </div>
                </motion.button>
                
                <motion.button
                  type="button"
                  whileHover={{ scale: 1.02 }}
                  whileTap={{ scale: 0.98 }}
                  onClick={() => handleChange('preferredContact', 'phone')}
                  className={`min-h-[56px] p-4 rounded-xl border-2 transition-all duration-300 touch-manipulation ${
                    formData.preferredContact === 'phone'
                      ? 'border-green-500/50 bg-green-500/20 text-green-200'
                      : 'border-gray-600/50 bg-gray-700/30 text-gray-300 hover:border-gray-500/50 hover:bg-gray-700/50'
                  }`}
                >
                  <div className="flex items-center justify-center space-x-2">
                    <Phone className="h-5 w-5" />
                    <span className="font-medium">Anruf bevorzugt</span>
                  </div>
                </motion.button>
              </div>
            </motion.div>

            {/* Message Section */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5, delay: 0.9 }}
              className="space-y-2"
            >
              <Label htmlFor="message" className="flex items-center space-x-2 text-gray-200 font-medium">
                <div className="bg-gradient-to-r from-purple-500 to-pink-600 p-1 rounded-lg">
                  <MessageSquare className="h-3 w-3 text-white" />
                </div>
                <span>Zusätzliche Nachricht (optional)</span>
              </Label>
              <Textarea
                id="message"
                placeholder="Besondere Wünsche, Fragen oder zusätzliche Informationen..."
                value={formData.message}
                onChange={(e) => handleChange('message', e.target.value)}
                rows={4}
                className="min-h-[100px] bg-gray-700/50 border-gray-600/50 text-white placeholder:text-gray-400 focus:border-purple-500/50 focus:bg-gray-700/70 transition-all duration-300 rounded-xl resize-none touch-manipulation"
              />
            </motion.div>
          </div>
        </div>

        {/* Privacy Checkbox */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.95 }}
          className="bg-gray-800/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-4 sm:p-6"
        >
          <PrivacyCheckbox
            checked={privacyAccepted}
            onCheckedChange={setPrivacyAccepted}
            id="calculator-privacy"
          />
        </motion.div>

        {/* Beautiful submit button with enhanced mobile design */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 1.0 }}
          whileHover={{ scale: 1.02 }}
          whileTap={{ scale: 0.98 }}
          className="relative"
        >
          <Button
            type="submit"
            disabled={isSubmitting || !formData.name || !formData.email || !formData.phone || !privacyAccepted}
            className="w-full min-h-[56px] bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold text-base sm:text-lg shadow-lg hover:shadow-xl transition-all duration-300 rounded-xl touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed relative overflow-hidden"
          >
            {/* Static shimmer effect - no animation */}
            <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-50" />
            
            <div className="relative z-10">
              {isSubmitting ? (
                <div className="flex items-center justify-center space-x-3">
                  <motion.div
                    animate={{ rotate: 360 }}
                    transition={{ duration: 1, repeat: Infinity, ease: "linear" }}
                    className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full"
                  />
                  <span>Wird gesendet...</span>
                </div>
              ) : (
                <div className="flex items-center justify-center space-x-3">
                  <Send className="h-5 w-5" />
                  <span>Kostenloses Angebot anfordern</span>
                </div>
              )}
            </div>
          </Button>
        </motion.div>

        {/* Beautiful trust indicators with mobile-optimized design */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 1.1 }}
          className="bg-gradient-to-r from-gray-800/60 to-gray-700/60 backdrop-blur-sm border border-gray-600/30 rounded-2xl p-4 sm:p-6"
        >
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.4, delay: 1.2 }}
              className="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-2"
            >
              <div className="bg-gradient-to-r from-green-500 to-emerald-600 p-2 rounded-xl">
                <CheckCircle className="h-4 w-4 text-white" />
              </div>
              <span className="text-sm font-medium text-gray-200">Kostenlose Besichtigung</span>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.4, delay: 1.3 }}
              className="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-2"
            >
              <div className="bg-gradient-to-r from-blue-500 to-cyan-600 p-2 rounded-xl">
                <Shield className="h-4 w-4 text-white" />
              </div>
              <span className="text-sm font-medium text-gray-200">Unverbindliches Angebot</span>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.4, delay: 1.4 }}
              className="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-2"
            >
              <div className="bg-gradient-to-r from-violet-500 to-purple-600 p-2 rounded-xl">
                <Clock className="h-4 w-4 text-white" />
              </div>
              <span className="text-sm font-medium text-gray-200">24h Rückmeldung</span>
            </motion.div>
          </div>
        </motion.div>
      </motion.form>

      {/* Enhanced validation message */}
      <AnimatePresence>
        {(!formData.name || !formData.email || !formData.phone || !privacyAccepted) && (
          <motion.div
            initial={{ opacity: 0, y: 10, scale: 0.95 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: -10, scale: 0.95 }}
            transition={{ duration: 0.3 }}
            className="bg-gradient-to-r from-yellow-500/20 to-orange-500/20 backdrop-blur-sm border border-yellow-500/30 rounded-2xl p-4"
          >
            <div className="flex items-center space-x-3">
              <div className="bg-gradient-to-r from-yellow-500 to-orange-600 p-2 rounded-xl flex-shrink-0">
                <AlertCircle className="h-4 w-4 text-white" />
              </div>
              <p className="text-sm text-yellow-200 font-medium">
                Bitte füllen Sie alle Pflichtfelder (*) aus und stimmen Sie der Datenschutzerklärung zu, um Ihr kostenloses Angebot zu erhalten.
              </p>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default GeneralInfo;