import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Card, CardContent } from '@/components/ui/card';
import { Truck, Trash2, Sparkles, CheckCircle, Star, Zap, Heart } from 'lucide-react';
import { cn } from '@/lib/utils';

const ServiceSelection = ({ data, updateData }) => {
  const [selectedServices, setSelectedServices] = useState(data.selectedServices || []);

  const services = [
    {
      id: 'umzug',
      title: 'Umzug',
      description: 'Professioneller Umzugsservice mit Verpackung und Transport',
      icon: Truck,
      gradient: 'from-blue-500 to-cyan-500',
      shadowColor: 'shadow-blue-500/25',
      borderColor: 'border-blue-500/50',
      bgColor: 'bg-blue-500/10',
      badge: 'Beliebt',
      badgeColor: 'from-orange-500 to-red-500'
    },
    {
      id: 'entruempelung',
      title: 'Entrümpelung',
      description: 'Haushaltsauflösung und fachgerechte Entsorgung',
      icon: Trash2,
      gradient: 'from-green-500 to-emerald-500',
      shadowColor: 'shadow-green-500/25',
      borderColor: 'border-green-500/50',
      bgColor: 'bg-green-500/10',
      badge: 'Sehr nachgefragt',
      badgeColor: 'from-emerald-500 to-green-600'
    },
    {
      id: 'putzservice',
      title: 'Putzservice',
      description: 'Grundreinigung und besenreine Übergabe',
      icon: Sparkles,
      gradient: 'from-purple-500 to-pink-500',
      shadowColor: 'shadow-purple-500/25',
      borderColor: 'border-purple-500/50',
      bgColor: 'bg-purple-500/10',
      badge: 'Top Angebot',
      badgeColor: 'from-purple-500 to-pink-600'
    }
  ];

  const selectService = (serviceId) => {
    // Only allow single selection
    const newSelection = selectedServices.includes(serviceId) ? [] : [serviceId];
    setSelectedServices(newSelection);
  };

  useEffect(() => {
    updateData(selectedServices, 'selectedServices');
  }, [selectedServices, updateData]);

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
          <div className="bg-gradient-to-r from-violet-500 to-purple-600 p-2 rounded-xl">
            <Star className="w-5 h-5 text-white" />
          </div>
          <h2 className="text-2xl sm:text-3xl font-bold text-white">
            Welchen Service benötigen Sie?
          </h2>
        </motion.div>
        <motion.p 
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="text-sm sm:text-base text-violet-200/80 max-w-md mx-auto"
        >
          Wählen Sie einen Service für Ihr perfektes Angebot
        </motion.p>
      </motion.div>

      {/* Beautiful service cards with mobile-first design */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        {services.map((service, index) => {
          const Icon = service.icon;
          const isSelected = selectedServices.includes(service.id);
          
          return (
            <motion.div
              key={service.id}
              initial={{ opacity: 0, y: 20, scale: 0.9 }}
              animate={{ opacity: 1, y: 0, scale: 1 }}
              transition={{ 
                duration: 0.5, 
                delay: index * 0.1,
                ease: [0.4, 0, 0.2, 1]
              }}
              whileHover={{ 
                scale: 1.02,
                transition: { duration: 0.2 }
              }}
              whileTap={{ 
                scale: 0.98,
                transition: { duration: 0.1 }
              }}
              className="relative"
            >
              {/* Service badge */}
              {service.badge && (
                <motion.div
                  initial={{ opacity: 0, scale: 0 }}
                  animate={{ opacity: 1, scale: 1 }}
                  transition={{ duration: 0.3, delay: 0.5 }}
                  className="absolute -top-2 -right-2 z-10"
                >
                  <div className={`bg-gradient-to-r ${service.badgeColor} text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg`}>
                    {service.badge}
                  </div>
                </motion.div>
              )}

              <Card
                className={cn(
                  "cursor-pointer transition-all duration-300 relative overflow-hidden border-2 min-h-[180px] sm:min-h-[200px] touch-manipulation",
                  isSelected
                    ? `${service.borderColor} ${service.bgColor} ${service.shadowColor} shadow-xl`
                    : "border-gray-700/50 bg-gray-800/30 hover:border-gray-600/50 hover:bg-gray-800/50 hover:shadow-lg"
                )}
                onClick={() => selectService(service.id)}
              >
                {/* Animated background gradient */}
                <div className={cn(
                  "absolute inset-0 opacity-0 transition-opacity duration-300",
                  isSelected && "opacity-10"
                )}>
                  <div className={`absolute inset-0 bg-gradient-to-br ${service.gradient}`} />
                </div>

                {/* Selection indicator */}
                <AnimatePresence>
                  {isSelected && (
                    <motion.div
                      initial={{ scale: 0, opacity: 0 }}
                      animate={{ scale: 1, opacity: 1 }}
                      exit={{ scale: 0, opacity: 0 }}
                      transition={{ duration: 0.3, ease: "backOut" }}
                      className="absolute top-3 right-3 z-10"
                    >
                      <div className={`bg-gradient-to-r ${service.gradient} p-1.5 rounded-full shadow-lg`}>
                        <CheckCircle className="h-4 w-4 text-white" />
                      </div>
                    </motion.div>
                  )}
                </AnimatePresence>
                
                <CardContent className="p-4 sm:p-6 text-center relative z-10 h-full flex flex-col justify-center">
                  {/* Icon with beautiful animation */}
                  <motion.div 
                    className="mb-4"
                    animate={isSelected ? { 
                      scale: [1, 1.1, 1],
                      rotate: [0, 5, -5, 0]
                    } : {}}
                    transition={{ duration: 0.6 }}
                  >
                    <div className={cn(
                      "w-12 h-12 sm:w-16 sm:h-16 mx-auto rounded-2xl flex items-center justify-center transition-all duration-300",
                      isSelected 
                        ? `bg-gradient-to-r ${service.gradient} shadow-lg ${service.shadowColor}`
                        : "bg-gray-700/50"
                    )}>
                      <Icon className={cn(
                        "h-6 w-6 sm:h-8 sm:w-8 transition-colors duration-300",
                        isSelected ? "text-white" : "text-gray-400"
                      )} />
                    </div>
                  </motion.div>
                  
                  <h3 className={cn(
                    "text-lg sm:text-xl font-bold mb-2 transition-colors duration-300",
                    isSelected ? "text-white" : "text-gray-200"
                  )}>
                    {service.title}
                  </h3>
                  
                  <p className={cn(
                    "text-xs sm:text-sm leading-relaxed transition-colors duration-300",
                    isSelected ? "text-gray-200" : "text-gray-400"
                  )}>
                    {service.description}
                  </p>
                </CardContent>

                {/* Hover effect overlay */}
                <div className="absolute inset-0 bg-gradient-to-t from-transparent to-white/5 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none" />
              </Card>
            </motion.div>
          );
        })}
      </div>

      {/* Beautiful selected services summary */}
      <AnimatePresence>
        {selectedServices.length > 0 && (
          <motion.div
            initial={{ opacity: 0, y: 20, scale: 0.95 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: -20, scale: 0.95 }}
            transition={{ duration: 0.4, ease: [0.4, 0, 0.2, 1] }}
            className="bg-gradient-to-r from-violet-600/20 to-purple-600/20 backdrop-blur-sm border border-violet-500/30 rounded-2xl p-4 sm:p-6"
          >
            <div className="flex items-center space-x-3 mb-4">
              <div className="bg-gradient-to-r from-violet-500 to-purple-600 p-2 rounded-xl">
                <Heart className="w-5 h-5 text-white" />
              </div>
              <h4 className="font-bold text-white text-lg">Ihre Auswahl</h4>
            </div>
            
            <div className="flex flex-wrap gap-2 sm:gap-3">
              {selectedServices.map((serviceId, index) => {
                const service = services.find(s => s.id === serviceId);
                return (
                  <motion.div
                    key={serviceId}
                    initial={{ opacity: 0, scale: 0.8 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ duration: 0.3, delay: index * 0.1 }}
                    className={`bg-gradient-to-r ${service?.gradient} text-white px-3 sm:px-4 py-2 rounded-full text-sm font-medium shadow-lg flex items-center space-x-2`}
                  >
                    <Zap className="w-3 h-3" />
                    <span>{service?.title}</span>
                  </motion.div>
                );
              })}
            </div>
            
            <motion.p 
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ duration: 0.5, delay: 0.3 }}
              className="text-violet-200/80 text-sm mt-4"
            >
              Perfekt! Wir erstellen Ihnen ein maßgeschneidertes Angebot für Ihren gewählten Service.
            </motion.p>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default ServiceSelection;