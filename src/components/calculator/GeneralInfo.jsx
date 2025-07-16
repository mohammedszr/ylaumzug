import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { User, Mail, Phone, Calendar, MessageSquare, CheckCircle, Send } from 'lucide-react';
import { useToast } from '@/components/ui/use-toast';

const GeneralInfo = ({ data, updateData }) => {
  const [formData, setFormData] = useState({
    name: '',
    phone: '',
    email: '',
    preferredDate: '',
    message: '',
    preferredContact: 'email',
    ...data.generalInfo
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const { toast } = useToast();

  const handleChange = (field, value) => {
    const newData = { ...formData, [field]: value };
    setFormData(newData);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!formData.name || !formData.email || !formData.phone) return;
    
    setIsSubmitting(true);
    
    // Simulate form submission to Laravel backend
    setTimeout(() => {
      toast({
        title: "Anfrage erfolgreich gesendet!",
        description: "Wir melden uns innerhalb von 24 Stunden bei Ihnen zurück.",
      });
      setIsSubmitting(false);
    }, 1000);
  };

  useEffect(() => {
    updateData(formData, 'generalInfo');
  }, [formData, updateData]);

  return (
    <div className="space-y-6">
      <div className="text-center">
        <h2 className="text-2xl font-bold text-white mb-2">
          Kostenloses Angebot anfordern
        </h2>
        <p className="text-gray-300">
          Lassen Sie uns Ihnen ein unverbindliches Angebot per E-Mail zusenden
        </p>
      </div>

      {/* Price Summary Display */}
      {data.pricing && (
        <Card className="bg-green-50 border-green-200">
          <CardHeader>
            <CardTitle className="text-green-800 text-center">
              Ihre geschätzte Kostenübersicht
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-center">
              <div className="text-3xl font-bold text-green-600 mb-2">
                {data.pricing.total}€
              </div>
              <p className="text-sm text-green-700">
                Unverbindliche Schätzung - Finales Angebot nach Besichtigung
              </p>
            </div>
          </CardContent>
        </Card>
      )}

      <form onSubmit={handleSubmit} className="space-y-6">
        <Card>
          <CardContent className="p-6">
            <div className="grid md:grid-cols-2 gap-4">
              <div>
                <Label htmlFor="name" className="flex items-center space-x-2 mb-2">
                  <User className="h-4 w-4 text-violet-600" />
                  <span>Vollständiger Name *</span>
                </Label>
                <Input
                  id="name"
                  value={formData.name}
                  onChange={(e) => handleChange('name', e.target.value)}
                  placeholder="Max Mustermann"
                  className="border-gray-300 focus:border-violet-500"
                  required
                />
              </div>

              <div>
                <Label htmlFor="email" className="flex items-center space-x-2 mb-2">
                  <Mail className="h-4 w-4 text-violet-600" />
                  <span>E-Mail-Adresse *</span>
                </Label>
                <Input
                  id="email"
                  type="email"
                  value={formData.email}
                  onChange={(e) => handleChange('email', e.target.value)}
                  placeholder="max@beispiel.de"
                  className="border-gray-300 focus:border-violet-500"
                  required
                />
              </div>

              <div>
                <Label htmlFor="phone" className="flex items-center space-x-2 mb-2">
                  <Phone className="h-4 w-4 text-violet-600" />
                  <span>Telefonnummer *</span>
                </Label>
                <Input
                  id="phone"
                  type="tel"
                  value={formData.phone}
                  onChange={(e) => handleChange('phone', e.target.value)}
                  placeholder="+49 123 456789"
                  className="border-gray-300 focus:border-violet-500"
                  required
                />
              </div>

              <div>
                <Label htmlFor="preferredDate" className="flex items-center space-x-2 mb-2">
                  <Calendar className="h-4 w-4 text-violet-600" />
                  <span>Wunschtermin (optional)</span>
                </Label>
                <Input
                  id="preferredDate"
                  type="date"
                  value={formData.preferredDate}
                  onChange={(e) => handleChange('preferredDate', e.target.value)}
                  className="border-gray-300 focus:border-violet-500"
                />
              </div>
            </div>

            <div className="mt-4">
              <Label>Bevorzugte Kontaktart</Label>
              <div className="grid grid-cols-2 gap-3 mt-2">
                <motion.button
                  type="button"
                  whileTap={{ scale: 0.95 }}
                  onClick={() => handleChange('preferredContact', 'email')}
                  className={`p-3 rounded-lg border transition-colors ${
                    formData.preferredContact === 'email'
                      ? 'border-violet-500 bg-violet-50 text-violet-700'
                      : 'border-gray-300 hover:border-violet-300'
                  }`}
                >
                  <Mail className="h-4 w-4 mx-auto mb-1" />
                  E-Mail bevorzugt
                </motion.button>
                <motion.button
                  type="button"
                  whileTap={{ scale: 0.95 }}
                  onClick={() => handleChange('preferredContact', 'phone')}
                  className={`p-3 rounded-lg border transition-colors ${
                    formData.preferredContact === 'phone'
                      ? 'border-violet-500 bg-violet-50 text-violet-700'
                      : 'border-gray-300 hover:border-violet-300'
                  }`}
                >
                  <Phone className="h-4 w-4 mx-auto mb-1" />
                  Anruf bevorzugt
                </motion.button>
              </div>
            </div>

            <div className="mt-4">
              <Label htmlFor="message" className="flex items-center space-x-2 mb-2">
                <MessageSquare className="h-4 w-4 text-violet-600" />
                <span>Zusätzliche Nachricht (optional)</span>
              </Label>
              <Textarea
                id="message"
                placeholder="Besondere Wünsche, Fragen oder zusätzliche Informationen..."
                value={formData.message}
                onChange={(e) => handleChange('message', e.target.value)}
                rows={3}
                className="border-gray-300 focus:border-violet-500"
              />
            </div>
          </CardContent>
        </Card>

        <motion.div
          whileHover={{ scale: 1.02 }}
          whileTap={{ scale: 0.98 }}
        >
          <Button
            type="submit"
            disabled={isSubmitting || !formData.name || !formData.email || !formData.phone}
            className="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 text-lg"
          >
            {isSubmitting ? (
              <div className="flex items-center space-x-2">
                <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                <span>Wird gesendet...</span>
              </div>
            ) : (
              <div className="flex items-center space-x-2">
                <Send className="h-5 w-5" />
                <span>Kostenloses Angebot per E-Mail anfordern</span>
              </div>
            )}
          </Button>
        </motion.div>

        <div className="bg-gray-100 p-4 rounded-lg">
          <div className="flex items-center justify-center space-x-6 text-sm text-gray-600">
            <div className="flex items-center space-x-1">
              <CheckCircle className="h-4 w-4 text-green-500" />
              <span>Kostenlose Besichtigung</span>
            </div>
            <div className="flex items-center space-x-1">
              <CheckCircle className="h-4 w-4 text-green-500" />
              <span>Unverbindliches Angebot</span>
            </div>
            <div className="flex items-center space-x-1">
              <CheckCircle className="h-4 w-4 text-green-500" />
              <span>Festpreisgarantie</span>
            </div>
          </div>
        </div>
      </form>

      {(!formData.name || !formData.email || !formData.phone) && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded"
        >
          <p className="text-sm">
            Bitte füllen Sie alle Pflichtfelder (*) aus, um Ihr kostenloses Angebot zu erhalten.
          </p>
        </motion.div>
      )}
    </div>
  );
};

export default GeneralInfo;