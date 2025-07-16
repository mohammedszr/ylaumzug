import React from 'react';
import { Check, Sparkles } from 'lucide-react';
import { motion } from 'framer-motion';
import { cn } from '@/lib/utils';

const CalculatorStepper = ({ steps, currentStep }) => {
  return (
    <div className="w-full max-w-2xl mx-auto px-4">
      {/* Mobile-first progress bar */}
      <div className="mb-6">
        <div className="flex justify-between items-center mb-2">
          <span className="text-xs sm:text-sm text-violet-300/70 font-medium">
            Schritt {currentStep + 1} von {steps.length}
          </span>
          <span className="text-xs sm:text-sm text-violet-300/70">
            {Math.round(((currentStep + 1) / steps.length) * 100)}%
          </span>
        </div>
        
        {/* Beautiful animated progress bar */}
        <div className="relative h-2 bg-gray-700/50 rounded-full overflow-hidden">
          <motion.div
            initial={{ width: 0 }}
            animate={{ width: `${((currentStep + 1) / steps.length) * 100}%` }}
            transition={{ duration: 0.8, ease: [0.4, 0, 0.2, 1] }}
            className="absolute top-0 left-0 h-full bg-gradient-to-r from-violet-500 via-purple-500 to-pink-500 rounded-full"
          />
          
          {/* Animated shimmer effect */}
          <motion.div
            animate={{ x: ['-100%', '100%'] }}
            transition={{ duration: 2, repeat: Infinity, ease: "linear" }}
            className="absolute top-0 left-0 h-full w-1/3 bg-gradient-to-r from-transparent via-white/20 to-transparent"
          />
        </div>
      </div>

      {/* Desktop stepper - hidden on mobile */}
      <div className="hidden sm:flex items-center justify-center space-x-2 lg:space-x-4">
        {steps.map((step, index) => (
          <React.Fragment key={step.id}>
            <motion.div 
              initial={{ scale: 0.8, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              transition={{ duration: 0.3, delay: index * 0.1 }}
              className="flex flex-col items-center"
            >
              {/* Step circle with beautiful animations */}
              <motion.div
                whileHover={{ scale: 1.05 }}
                className={cn(
                  "relative w-10 h-10 lg:w-12 lg:h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-lg",
                  index < currentStep
                    ? "bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-green-500/25"
                    : index === currentStep
                    ? "bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-violet-500/25 ring-2 ring-violet-400/50"
                    : "bg-gray-700/50 text-gray-400 border border-gray-600/50"
                )}
              >
                {index < currentStep ? (
                  <motion.div
                    initial={{ scale: 0 }}
                    animate={{ scale: 1 }}
                    transition={{ duration: 0.3 }}
                  >
                    <Check className="h-4 w-4 lg:h-5 lg:w-5" />
                  </motion.div>
                ) : index === currentStep ? (
                  <motion.div
                    animate={{ rotate: [0, 360] }}
                    transition={{ duration: 2, repeat: Infinity, ease: "linear" }}
                  >
                    <Sparkles className="h-4 w-4 lg:h-5 lg:w-5" />
                  </motion.div>
                ) : (
                  <span className="text-xs lg:text-sm">{index + 1}</span>
                )}
                
                {/* Pulsing ring for current step */}
                {index === currentStep && (
                  <motion.div
                    animate={{ scale: [1, 1.2, 1], opacity: [0.5, 0, 0.5] }}
                    transition={{ duration: 2, repeat: Infinity }}
                    className="absolute inset-0 rounded-full border-2 border-violet-400"
                  />
                )}
              </motion.div>
              
              {/* Step title */}
              <motion.span 
                initial={{ opacity: 0, y: 5 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.3, delay: index * 0.1 + 0.2 }}
                className={cn(
                  "text-xs lg:text-sm mt-2 text-center max-w-16 lg:max-w-20 leading-tight font-medium transition-colors duration-300",
                  index === currentStep 
                    ? "text-violet-300" 
                    : index < currentStep 
                    ? "text-green-400" 
                    : "text-gray-500"
                )}
              >
                {step.title}
              </motion.span>
            </motion.div>
            
            {/* Connection line */}
            {index < steps.length - 1 && (
              <motion.div
                initial={{ scaleX: 0 }}
                animate={{ scaleX: 1 }}
                transition={{ duration: 0.5, delay: index * 0.1 + 0.3 }}
                className="flex-1 max-w-8 lg:max-w-12 h-0.5 mx-2 origin-left"
              >
                <div
                  className={cn(
                    "h-full rounded-full transition-all duration-500",
                    index < currentStep 
                      ? "bg-gradient-to-r from-green-500 to-emerald-500 shadow-sm shadow-green-500/25" 
                      : "bg-gray-700/50"
                  )}
                />
              </motion.div>
            )}
          </React.Fragment>
        ))}
      </div>

      {/* Mobile current step indicator */}
      <div className="sm:hidden text-center">
        <motion.div
          key={currentStep}
          initial={{ opacity: 0, y: 10 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.3 }}
          className="inline-flex items-center space-x-2 bg-violet-600/20 backdrop-blur-sm px-4 py-2 rounded-full border border-violet-500/30"
        >
          <div className="w-6 h-6 bg-gradient-to-r from-violet-600 to-purple-600 rounded-full flex items-center justify-center">
            <Sparkles className="h-3 w-3 text-white" />
          </div>
          <span className="text-sm font-medium text-violet-200">
            {steps[currentStep]?.title}
          </span>
        </motion.div>
      </div>
    </div>
  );
};

export default CalculatorStepper;