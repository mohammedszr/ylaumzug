import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sparkles, Home, Calendar, Key } from 'lucide-react';

const CleaningDetails = ({ data, updateData }) => {
  const [formData, setFormData] = useState({
    objectType: '',
    size: '',
    rooms: [],
    cleaningIntensity: '',
    frequency: '',
    keyHandover: '',
    ...data.cleaningDetails
  });

  const handleChange = (field, value) => {
    const newData = { ...formData, [field]: value };
    setFormData(newData);
    // Update parent data immediately
    updateData(newData, 'cleaningDetails');
  };

  const toggleRoom = (room) => {
    const newData = {
      ...formData,
      rooms: formData.rooms.includes(room)
        ? formData.rooms.filter(r => r !== room)
        : [...formData.rooms, room]
    };
    setFormData(newData);
    // Update parent data immediately
    updateData(newData, 'cleaningDetails');
  };



  const objectTypes = [
    { id: 'apartment', label: 'Wohnung', icon: '🏠' },
    { id: 'house', label: 'Haus', icon: '🏡' },
    { id: 'office', label: 'Büro', icon: '🏢' },
    { id: 'practice', label: 'Praxis', icon: '🏥' }
  ];

  const roomTypes = [
    { id: 'kitchen', label: 'Küche', icon: '🍳' },
    { id: 'bathroom', label: 'Badezimmer/WC', icon: '🚿' },
    { id: 'livingRooms', label: 'Wohnräume', icon: '🛋️' },
    { id: 'windows', label: 'Fensterreinigung', icon: '🪟' }
  ];

  const cleaningIntensities = [
    { id: 'normal', label: 'Normalreinigung', description: 'Standardreinigung für bewohnte Räume' },
    { id: 'deep', label: 'Grundreinigung', description: 'Intensive Reinigung aller Bereiche' },
    { id: 'construction', label: 'Bauschlussreinigung', description: 'Nach Renovierung oder Neubau' }
  ];

  const frequencies = [
    { id: 'once', label: 'Einmalig' },
    { id: 'weekly', label: 'Wöchentlich' },
    { id: 'biweekly', label: '14-tägig' },
    { id: 'monthly', label: 'Monatlich' }
  ];

  return (
    <div className="space-y-6">
      <div className="text-center">
        <h2 className="text-2xl font-bold text-white mb-2">
          Details zum Putzservice
        </h2>
        <p className="text-gray-300">
          Beschreiben Sie Ihre Reinigungsanforderungen
        </p>
      </div>

      {/* Object Type */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Home className="h-5 w-5 text-violet-600" />
            <span>Objektart</span>
            <span className="text-red-400 font-bold ml-1">*</span>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-4 gap-3">
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

      {/* Size */}
      <Card>
        <CardContent className="p-6">
          <div>
            <Label htmlFor="size" className="flex items-center space-x-2 mb-2">
              <Sparkles className="h-4 w-4 text-violet-600" />
              <span>Größe (m²)</span>
              <span className="text-red-400 font-bold ml-1">*</span>
            </Label>
            <Input
              id="size"
              type="number"
              placeholder="z.B. 80"
              value={formData.size}
              onChange={(e) => handleChange('size', e.target.value)}
              className="max-w-xs"
            />
          </div>
        </CardContent>
      </Card>

      {/* Rooms */}
      <Card>
        <CardHeader>
          <CardTitle>Zu reinigende Bereiche</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-2 gap-3">
            {roomTypes.map(room => (
              <motion.button
                key={room.id}
                type="button"
                whileTap={{ scale: 0.95 }}
                onClick={() => toggleRoom(room.id)}
                className={`p-4 rounded-lg border text-left transition-colors ${
                  formData.rooms.includes(room.id)
                    ? 'border-violet-500 bg-violet-50 text-violet-700'
                    : 'border-gray-300 hover:border-violet-300'
                }`}
              >
                <div className="flex items-center space-x-3">
                  <span className="text-xl">{room.icon}</span>
                  <span className="font-medium">{room.label}</span>
                </div>
              </motion.button>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Cleaning Intensity */}
      <Card>
        <CardHeader>
          <CardTitle>Reinigungsintensität <span className="text-red-400 font-bold ml-1">*</span></CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {cleaningIntensities.map(intensity => (
              <motion.button
                key={intensity.id}
                type="button"
                whileTap={{ scale: 0.98 }}
                onClick={() => handleChange('cleaningIntensity', intensity.id)}
                className={`w-full p-4 rounded-lg border text-left transition-colors ${
                  formData.cleaningIntensity === intensity.id
                    ? 'border-violet-500 bg-violet-50 text-violet-700'
                    : 'border-gray-300 hover:border-violet-300'
                }`}
              >
                <div className="font-medium mb-1">{intensity.label}</div>
                <div className="text-sm opacity-75">{intensity.description}</div>
              </motion.button>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Frequency */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Calendar className="h-5 w-5 text-violet-600" />
            <span>Häufigkeit</span>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-4 gap-3">
            {frequencies.map(freq => (
              <motion.button
                key={freq.id}
                type="button"
                whileTap={{ scale: 0.95 }}
                onClick={() => handleChange('frequency', freq.id)}
                className={`p-3 rounded-lg border text-center transition-colors ${
                  formData.frequency === freq.id
                    ? 'border-violet-500 bg-violet-50 text-violet-700'
                    : 'border-gray-300 hover:border-violet-300'
                }`}
              >
                {freq.label}
              </motion.button>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Key Handover */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Key className="h-5 w-5 text-violet-600" />
            <span>Zugang</span>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-2 gap-3">
            <motion.button
              type="button"
              whileTap={{ scale: 0.95 }}
              onClick={() => handleChange('keyHandover', 'present')}
              className={`p-3 rounded-lg border transition-colors ${
                formData.keyHandover === 'present'
                  ? 'border-violet-500 bg-violet-50 text-violet-700'
                  : 'border-gray-300 hover:border-violet-300'
              }`}
            >
              Ich bin vor Ort
            </motion.button>
            <motion.button
              type="button"
              whileTap={{ scale: 0.95 }}
              onClick={() => handleChange('keyHandover', 'key')}
              className={`p-3 rounded-lg border transition-colors ${
                formData.keyHandover === 'key'
                  ? 'border-violet-500 bg-violet-50 text-violet-700'
                  : 'border-gray-300 hover:border-violet-300'
              }`}
            >
              Schlüsselübergabe nötig
            </motion.button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default CleaningDetails;