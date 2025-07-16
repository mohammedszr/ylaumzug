import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { MapPin, Home, Package, Truck, Plus, Minus } from 'lucide-react';

const MovingDetails = ({ data, updateData }) => {
  const [formData, setFormData] = useState({
    fromAddress: { street: '', postalCode: '', city: '' },
    toAddress: { street: '', postalCode: '', city: '' },
    apartmentSize: '',
    rooms: '',
    fromFloor: '',
    toFloor: '',
    fromElevator: '',
    toElevator: '',
    parking: '',
    boxes: 0,
    furniture: {
      beds: 0,
      wardrobes: 0,
      sofas: 0,
      tables: 0,
      washingMachine: 0,
      refrigerator: 0,
      other: 0
    },
    dismantleFurniture: '',
    specialItems: '',
    additionalServices: [],
    ...data.movingDetails
  });

  const handleChange = (field, value) => {
    if (field.includes('.')) {
      const [parent, child] = field.split('.');
      setFormData(prev => ({
        ...prev,
        [parent]: { ...prev[parent], [child]: value }
      }));
    } else {
      setFormData(prev => ({ ...prev, [field]: value }));
    }
  };

  const handleFurnitureChange = (item, value) => {
    setFormData(prev => ({
      ...prev,
      furniture: { ...prev.furniture, [item]: Math.max(0, value) }
    }));
  };

  const toggleAdditionalService = (service) => {
    setFormData(prev => ({
      ...prev,
      additionalServices: prev.additionalServices.includes(service)
        ? prev.additionalServices.filter(s => s !== service)
        : [...prev.additionalServices, service]
    }));
  };

  useEffect(() => {
    updateData(formData, 'movingDetails');
  }, [formData, updateData]);

  const additionalServices = [
    { id: 'assembly', label: 'Möbelabbau & Aufbau' },
    { id: 'packing', label: 'Verpackungsservice' },
    { id: 'parking', label: 'Halteverbotszone beantragen' },
    { id: 'storage', label: 'Einlagerung' },
    { id: 'disposal', label: 'Entsorgung alter Möbel' }
  ];

  const furnitureItems = [
    { key: 'beds', label: 'Betten', icon: '🛏️' },
    { key: 'wardrobes', label: 'Schränke', icon: '🚪' },
    { key: 'sofas', label: 'Sofas/Sessel', icon: '🛋️' },
    { key: 'tables', label: 'Tische/Stühle', icon: '🪑' },
    { key: 'washingMachine', label: 'Waschmaschine', icon: '🔌' },
    { key: 'refrigerator', label: 'Kühlschrank', icon: '❄️' },
    { key: 'other', label: 'Weitere Elektrogeräte', icon: '📺' }
  ];

  return (
    <div className="space-y-6">
      <div className="text-center">
        <h2 className="text-2xl font-bold text-white mb-2">
          Details zu Ihrem Umzug
        </h2>
        <p className="text-gray-300">
          Je genauer Ihre Angaben, desto präziser unser Angebot
        </p>
      </div>

      {/* Addresses */}
      <div className="grid md:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center space-x-2">
              <MapPin className="h-5 w-5 text-red-500" />
              <span>Auszugsadresse</span>
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <Input
              placeholder="Straße und Hausnummer"
              value={formData.fromAddress.street}
              onChange={(e) => handleChange('fromAddress.street', e.target.value)}
            />
            <div className="grid grid-cols-2 gap-2">
              <Input
                placeholder="PLZ"
                value={formData.fromAddress.postalCode}
                onChange={(e) => handleChange('fromAddress.postalCode', e.target.value)}
              />
              <Input
                placeholder="Stadt"
                value={formData.fromAddress.city}
                onChange={(e) => handleChange('fromAddress.city', e.target.value)}
              />
            </div>
            <div className="grid grid-cols-2 gap-2">
              <Input
                placeholder="Etage"
                value={formData.fromFloor}
                onChange={(e) => handleChange('fromFloor', e.target.value)}
              />
              <select
                value={formData.fromElevator}
                onChange={(e) => handleChange('fromElevator', e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-md"
              >
                <option value="">Fahrstuhl?</option>
                <option value="yes">Ja</option>
                <option value="no">Nein</option>
              </select>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center space-x-2">
              <MapPin className="h-5 w-5 text-green-500" />
              <span>Einzugsadresse</span>
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <Input
              placeholder="Straße und Hausnummer"
              value={formData.toAddress.street}
              onChange={(e) => handleChange('toAddress.street', e.target.value)}
            />
            <div className="grid grid-cols-2 gap-2">
              <Input
                placeholder="PLZ"
                value={formData.toAddress.postalCode}
                onChange={(e) => handleChange('toAddress.postalCode', e.target.value)}
              />
              <Input
                placeholder="Stadt"
                value={formData.toAddress.city}
                onChange={(e) => handleChange('toAddress.city', e.target.value)}
              />
            </div>
            <div className="grid grid-cols-2 gap-2">
              <Input
                placeholder="Etage"
                value={formData.toFloor}
                onChange={(e) => handleChange('toFloor', e.target.value)}
              />
              <select
                value={formData.toElevator}
                onChange={(e) => handleChange('toElevator', e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-md"
              >
                <option value="">Fahrstuhl?</option>
                <option value="yes">Ja</option>
                <option value="no">Nein</option>
              </select>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Apartment Details */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Home className="h-5 w-5 text-violet-600" />
            <span>Wohnungsdetails</span>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-3 gap-4">
            <div>
              <Label>Größe der Wohnung (m²)</Label>
              <Input
                type="number"
                placeholder="z.B. 80"
                value={formData.apartmentSize}
                onChange={(e) => handleChange('apartmentSize', e.target.value)}
              />
            </div>
            <div>
              <Label>Zimmeranzahl</Label>
              <Input
                type="number"
                placeholder="z.B. 3"
                value={formData.rooms}
                onChange={(e) => handleChange('rooms', e.target.value)}
              />
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

      {/* Furniture Inventory */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Package className="h-5 w-5 text-violet-600" />
            <span>Transportvolumen</span>
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-6">
          <div>
            <Label>Anzahl Kartons (Schätzung)</Label>
            <Input
              type="number"
              placeholder="z.B. 20"
              value={formData.boxes}
              onChange={(e) => handleChange('boxes', parseInt(e.target.value) || 0)}
            />
          </div>

          <div>
            <Label className="text-lg font-medium mb-4 block">Möbelstücke</Label>
            <div className="grid md:grid-cols-2 gap-4">
              {furnitureItems.map(item => (
                <div key={item.key} className="flex items-center justify-between p-3 border rounded-lg">
                  <span className="flex items-center space-x-2">
                    <span className="text-xl">{item.icon}</span>
                    <span>{item.label}</span>
                  </span>
                  <div className="flex items-center space-x-2">
                    <button
                      type="button"
                      onClick={() => handleFurnitureChange(item.key, formData.furniture[item.key] - 1)}
                      className="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300"
                    >
                      <Minus className="h-4 w-4" />
                    </button>
                    <span className="w-8 text-center font-medium">
                      {formData.furniture[item.key]}
                    </span>
                    <button
                      type="button"
                      onClick={() => handleFurnitureChange(item.key, formData.furniture[item.key] + 1)}
                      className="w-8 h-8 rounded-full bg-violet-200 flex items-center justify-center hover:bg-violet-300"
                    >
                      <Plus className="h-4 w-4" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>

          <div className="grid md:grid-cols-2 gap-4">
            <div>
              <Label>Zerlegbare Möbel?</Label>
              <select
                value={formData.dismantleFurniture}
                onChange={(e) => handleChange('dismantleFurniture', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-md"
              >
                <option value="">Auswählen</option>
                <option value="yes">Ja, Möbel müssen zerlegt werden</option>
                <option value="no">Nein, alles transportfertig</option>
              </select>
            </div>
            <div>
              <Label>Empfindliche/schwere Gegenstände</Label>
              <Input
                placeholder="z.B. Klavier, Tresor, Aquarium"
                value={formData.specialItems}
                onChange={(e) => handleChange('specialItems', e.target.value)}
              />
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Additional Services */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Truck className="h-5 w-5 text-violet-600" />
            <span>Zusatzleistungen</span>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-2 gap-3">
            {additionalServices.map(service => (
              <motion.button
                key={service.id}
                type="button"
                whileTap={{ scale: 0.95 }}
                onClick={() => toggleAdditionalService(service.id)}
                className={`p-3 rounded-lg border text-left transition-colors ${
                  formData.additionalServices.includes(service.id)
                    ? 'border-violet-500 bg-violet-50 text-violet-700'
                    : 'border-gray-300 hover:border-violet-300'
                }`}
              >
                {service.label}
              </motion.button>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default MovingDetails;