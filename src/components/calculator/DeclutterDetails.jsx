import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Trash2, MapPin, Home, Package, Building } from 'lucide-react';

const DeclutterDetails = ({ data, updateData }) => {
  const [formData, setFormData] = useState({
    address: { street: '', postalCode: '', city: '' },
    objectType: '',
    size: '',
    volume: '',
    wasteTypes: [],
    floor: '',
    elevator: '',
    parking: '',
    urgency: '',
    cleanHandover: '',
    additionalInfo: '',
    ...data.declutterDetails
  });

  const handleChange = (field, value) => {
    let newData;
    if (field.includes('.')) {
      const [parent, child] = field.split('.');
      newData = {
        ...formData,
        [parent]: { ...formData[parent], [child]: value }
      };
    } else {
      newData = { ...formData, [field]: value };
    }
    setFormData(newData);
    // Update parent data immediately
    updateData(newData, 'declutterDetails');
  };

  const toggleWasteType = (wasteType) => {
    const newData = {
      ...formData,
      wasteTypes: formData.wasteTypes.includes(wasteType)
        ? formData.wasteTypes.filter(w => w !== wasteType)
        : [...formData.wasteTypes, wasteType]
    };
    setFormData(newData);
    // Update parent data immediately
    updateData(newData, 'declutterDetails');
  };



  const objectTypes = [
    { id: 'apartment', label: 'Wohnung', icon: '🏠' },
    { id: 'house', label: 'Haus', icon: '🏡' },
    { id: 'basement', label: 'Keller', icon: '🏠' },
    { id: 'garage', label: 'Garage', icon: '🚗' },
    { id: 'office', label: 'Büro', icon: '🏢' },
    { id: 'attic', label: 'Dachboden', icon: '🏠' }
  ];

  const wasteTypes = [
    { id: 'furniture', label: 'Sperrmüll', icon: '🪑', description: 'Möbel, große Gegenstände' },
    { id: 'electronics', label: 'Elektrogeräte', icon: '📺', description: 'TV, Computer, Haushaltsgeräte' },
    { id: 'hazardous', label: 'Sondermüll', icon: '⚠️', description: 'Farben, Chemikalien, Batterien' },
    { id: 'household', label: 'Hausrat', icon: '📦', description: 'Kleidung, Bücher, Kleinteile' },
    { id: 'construction', label: 'Bauschutt', icon: '🧱', description: 'Fliesen, Beton, Ziegel' }
  ];

  return (
    <div className="space-y-6">
      <div className="text-center">
        <h2 className="text-2xl font-bold text-white mb-2">
          Details zur Entrümpelung
        </h2>
        <p className="text-gray-300">
          Beschreiben Sie das zu entrümpelnde Objekt
        </p>
      </div>

      {/* Address */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <MapPin className="h-5 w-5 text-violet-600" />
            <span>Adresse der Entrümpelung</span>
            <span className="text-red-400 font-bold ml-1">*</span>
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <Input
            placeholder="Straße und Hausnummer"
            value={formData.address.street}
            onChange={(e) => handleChange('address.street', e.target.value)}
          />
          <div className="grid grid-cols-2 gap-2">
            <Input
              placeholder="PLZ"
              value={formData.address.postalCode}
              onChange={(e) => handleChange('address.postalCode', e.target.value)}
            />
            <Input
              placeholder="Stadt"
              value={formData.address.city}
              onChange={(e) => handleChange('address.city', e.target.value)}
            />
          </div>
        </CardContent>
      </Card>

      {/* Object Type */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Home className="h-5 w-5 text-violet-600" />
            <span>Art des Objekts</span>
            <span className="text-red-400 font-bold ml-1">*</span>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-3 gap-3">
            {objectTypes.map(type => (
              <motion.button
                key={type.id}
                type="button"
                whileTap={{ scale: 0.95 }}
                onClick={() => handleChange('objectType', type.id)}
                className={`p-4 rounded-lg border text-center transition-colors ${
                  formData.objectType === type.id
                    ? 'border-violet-500 bg-violet-50 text-violet-700'
                    : 'border-gray-300 hover:border-violet-300'
                }`}
              >
                <div className="text-2xl mb-2">{type.icon}</div>
                <div className="font-medium">{type.label}</div>
              </motion.button>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Size and Volume */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Package className="h-5 w-5 text-violet-600" />
            <span>Größe und Volumen</span>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-2 gap-4">
            <div>
              <Label>Größe (m²) <span className="text-red-400 font-bold">*</span></Label>
              <Input
                type="number"
                placeholder="z.B. 50"
                value={formData.size}
                onChange={(e) => handleChange('size', e.target.value)}
              />
            </div>
            <div>
              <Label>Geschätztes Volumen</Label>
              <select
                value={formData.volume}
                onChange={(e) => handleChange('volume', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md"
              >
                <option value="">Auswählen</option>
                <option value="low">Wenig (1-2 Container)</option>
                <option value="medium">Mittel (3-5 Container)</option>
                <option value="high">Viel (6+ Container)</option>
                <option value="extreme">Sehr viel (Messi-Haushalt)</option>
              </select>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Waste Types */}
      <Card>
        <CardHeader>
          <CardTitle>Art des Materials</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {wasteTypes.map(waste => (
              <motion.button
                key={waste.id}
                type="button"
                whileTap={{ scale: 0.98 }}
                onClick={() => toggleWasteType(waste.id)}
                className={`w-full p-4 rounded-lg border text-left transition-colors ${
                  formData.wasteTypes.includes(waste.id)
                    ? 'border-violet-500 bg-violet-50 text-violet-700'
                    : 'border-gray-300 hover:border-violet-300'
                }`}
              >
                <div className="flex items-start space-x-3">
                  <span className="text-xl">{waste.icon}</span>
                  <div>
                    <div className="font-medium">{waste.label}</div>
                    <div className="text-sm opacity-75">{waste.description}</div>
                  </div>
                </div>
              </motion.button>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Access Details */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Building className="h-5 w-5 text-violet-600" />
            <span>Zugang und Logistik</span>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-3 gap-4">
            <div>
              <Label>Etage</Label>
              <Input
                placeholder="z.B. Erdgeschoss, 2. Stock"
                value={formData.floor}
                onChange={(e) => handleChange('floor', e.target.value)}
              />
            </div>
            <div>
              <Label>Fahrstuhl vorhanden?</Label>
              <select
                value={formData.elevator}
                onChange={(e) => handleChange('elevator', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md"
              >
                <option value="">Auswählen</option>
                <option value="yes">Ja, vorhanden</option>
                <option value="no">Nein, nur Treppen</option>
                <option value="freight">Lastenaufzug</option>
              </select>
            </div>
            <div>
              <Label>Parkmöglichkeiten</Label>
              <select
                value={formData.parking}
                onChange={(e) => handleChange('parking', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md"
              >
                <option value="">Auswählen</option>
                <option value="available">Parkplatz vorhanden</option>
                <option value="restricted">Halteverbot nötig</option>
                <option value="difficult">Schwierige Parksituation</option>
              </select>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Additional Options */}
      <Card>
        <CardContent className="p-6">
          <div className="space-y-4">
            <div>
              <Label>Besenreine Übergabe gewünscht?</Label>
              <div className="grid grid-cols-2 gap-3 mt-2">
                <motion.button
                  type="button"
                  whileTap={{ scale: 0.95 }}
                  onClick={() => handleChange('cleanHandover', 'yes')}
                  className={`p-3 rounded-lg border transition-colors ${
                    formData.cleanHandover === 'yes'
                      ? 'border-violet-500 bg-violet-50 text-violet-700'
                      : 'border-gray-300 hover:border-violet-300'
                  }`}
                >
                  Ja, mit Reinigung
                </motion.button>
                <motion.button
                  type="button"
                  whileTap={{ scale: 0.95 }}
                  onClick={() => handleChange('cleanHandover', 'no')}
                  className={`p-3 rounded-lg border transition-colors ${
                    formData.cleanHandover === 'no'
                      ? 'border-violet-500 bg-violet-50 text-violet-700'
                      : 'border-gray-300 hover:border-violet-300'
                  }`}
                >
                  Nein, nur Entrümpelung
                </motion.button>
              </div>
            </div>

            <div>
              <Label>Zusätzliche Informationen</Label>
              <Textarea
                placeholder="Besondere Umstände, schwer zugängliche Bereiche, Zeitvorgaben..."
                value={formData.additionalInfo}
                onChange={(e) => handleChange('additionalInfo', e.target.value)}
                rows={3}
              />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default DeclutterDetails;