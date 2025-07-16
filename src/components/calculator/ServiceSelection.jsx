import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Card, CardContent } from '@/components/ui/card';
import { Truck, Trash2, Sparkles, CheckCircle } from 'lucide-react';
import { cn } from '@/lib/utils';

const ServiceSelection = ({ data, updateData }) => {
  const [selectedServices, setSelectedServices] = useState(data.selectedServices || []);

  const services = [
    {
      id: 'umzug',
      title: 'Umzug',
      description: 'Professioneller Umzugsservice mit Verpackung und Transport',
      icon: Truck,
      color: 'blue'
    },
    {
      id: 'entruempelung',
      title: 'Entrümpelung',
      description: 'Haushaltsauflösung und fachgerechte Entsorgung',
      icon: Trash2,
      color: 'green'
    },
    {
      id: 'putzservice',
      title: 'Putzservice',
      description: 'Grundreinigung und besenreine Übergabe',
      icon: Sparkles,
      color: 'purple'
    }
  ];

  const toggleService = (serviceId) => {
    const newSelection = selectedServices.includes(serviceId)
      ? selectedServices.filter(id => id !== serviceId)
      : [...selectedServices, serviceId];
    
    setSelectedServices(newSelection);
  };

  useEffect(() => {
    updateData(selectedServices, 'selectedServices');
  }, [selectedServices, updateData]);

  const getColorClasses = (color, isSelected) => {
    const colors = {
      blue: isSelected 
        ? 'border-blue-500 bg-blue-50 text-blue-700' 
        : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50',
      green: isSelected 
        ? 'border-green-500 bg-green-50 text-green-700' 
        : 'border-gray-200 hover:border-green-300 hover:bg-green-50',
      purple: isSelected 
        ? 'border-purple-500 bg-purple-50 text-purple-700' 
        : 'border-gray-200 hover:border-purple-300 hover:bg-purple-50'
    };
    return colors[color];
  };

  return (
    <div className="space-y-6">
      <div className="text-center">
        <h2 className="text-2xl font-bold text-white mb-2">
          Welchen Service benötigen Sie?
        </h2>
        <p className="text-gray-300">
          Sie können mehrere Services auswählen für ein Komplettangebot
        </p>
      </div>

      <div className="grid md:grid-cols-3 gap-6">
        {services.map((service) => {
          const Icon = service.icon;
          const isSelected = selectedServices.includes(service.id);
          
          return (
            <motion.div
              key={service.id}
              whileHover={{ scale: 1.02 }}
              whileTap={{ scale: 0.98 }}
            >
              <Card
                className={cn(
                  "cursor-pointer transition-all duration-200 relative",
                  getColorClasses(service.color, isSelected)
                )}
                onClick={() => toggleService(service.id)}
              >
                <CardContent className="p-6 text-center">
                  {isSelected && (
                    <CheckCircle className="absolute top-4 right-4 h-6 w-6 text-green-500" />
                  )}
                  
                  <div className="mb-4">
                    <Icon className="h-12 w-12 mx-auto mb-3" />
                  </div>
                  
                  <h3 className="text-xl font-semibold mb-2">
                    {service.title}
                  </h3>
                  
                  <p className="text-sm opacity-80">
                    {service.description}
                  </p>
                </CardContent>
              </Card>
            </motion.div>
          );
        })}
      </div>

      {selectedServices.length > 0 && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="bg-violet-100 p-4 rounded-lg"
        >
          <h4 className="font-medium text-violet-800 mb-2">Gewählte Services:</h4>
          <div className="flex flex-wrap gap-2">
            {selectedServices.map(serviceId => {
              const service = services.find(s => s.id === serviceId);
              return (
                <span
                  key={serviceId}
                  className="bg-violet-600 text-white px-3 py-1 rounded-full text-sm"
                >
                  {service?.title}
                </span>
              );
            })}
          </div>
        </motion.div>
      )}
    </div>
  );
};

export default ServiceSelection;