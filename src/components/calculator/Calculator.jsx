import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import ServiceSelection from './ServiceSelection';
import GeneralInfo from './GeneralInfo';
import MovingDetails from './MovingDetails';
import CleaningDetails from './CleaningDetails';
import DeclutterDetails from './DeclutterDetails';
import PriceSummary from './PriceSummary';
import CalculatorStepper from './CalculatorStepper';

const Calculator = () => {
  const [currentStep, setCurrentStep] = useState(0);
  const [calculatorData, setCalculatorData] = useState({
    selectedServices: [],
    generalInfo: {},
    movingDetails: {},
    cleaningDetails: {},
    declutterDetails: {},
    pricing: null
  });

  const steps = [
    { id: 'service', title: 'Service wählen', component: ServiceSelection },
    { id: 'moving', title: 'Umzug Details', component: MovingDetails, condition: () => calculatorData.selectedServices.includes('umzug') },
    { id: 'cleaning', title: 'Putzservice Details', component: CleaningDetails, condition: () => calculatorData.selectedServices.includes('putzservice') },
    { id: 'declutter', title: 'Entrümpelung Details', component: DeclutterDetails, condition: () => calculatorData.selectedServices.includes('entruempelung') },
    { id: 'summary', title: 'Kostenübersicht', component: PriceSummary },
    { id: 'contact', title: 'Kontaktdaten', component: GeneralInfo }
  ];

  // Filter steps based on selected services
  const activeSteps = steps.filter(step => !step.condition || step.condition());

  const updateCalculatorData = (stepData, stepKey) => {
    setCalculatorData(prev => ({
      ...prev,
      [stepKey]: stepData
    }));
  };

  const nextStep = () => {
    if (currentStep < activeSteps.length - 1) {
      setCurrentStep(currentStep + 1);
    }
  };

  const prevStep = () => {
    if (currentStep > 0) {
      setCurrentStep(currentStep - 1);
    }
  };

  const canProceed = () => {
    const currentStepData = activeSteps[currentStep];
    switch (currentStepData.id) {
      case 'service':
        return calculatorData.selectedServices.length > 0;
      case 'contact':
        return calculatorData.generalInfo.name && calculatorData.generalInfo.email && calculatorData.generalInfo.phone;
      default:
        return true;
    }
  };

  const CurrentStepComponent = activeSteps[currentStep]?.component;

  return (
    <div className="max-w-4xl mx-auto p-6">
      <Card className="shadow-2xl border-0">
        <CardHeader className="text-center pb-6">
          <CardTitle className="text-3xl font-bold text-white mb-4">
            Jetzt Umzug berechnen
          </CardTitle>
          <CalculatorStepper 
            steps={activeSteps} 
            currentStep={currentStep} 
          />
        </CardHeader>
        
        <CardContent className="p-8">
          <AnimatePresence mode="wait">
            <motion.div
              key={currentStep}
              initial={{ opacity: 0, x: 50 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: -50 }}
              transition={{ duration: 0.3 }}
            >
              {CurrentStepComponent && (
                <CurrentStepComponent
                  data={calculatorData}
                  updateData={updateCalculatorData}
                  onNext={nextStep}
                />
              )}
            </motion.div>
          </AnimatePresence>

          <div className="flex justify-between mt-8">
            <Button
              variant="outline"
              onClick={prevStep}
              disabled={currentStep === 0}
              className="flex items-center space-x-2"
            >
              <ChevronLeft className="h-4 w-4" />
              <span>Zurück</span>
            </Button>

            {currentStep < activeSteps.length - 1 ? (
              <Button
                onClick={nextStep}
                disabled={!canProceed()}
                className="flex items-center space-x-2 bg-violet-600 hover:bg-violet-700"
              >
                <span>Weiter</span>
                <ChevronRight className="h-4 w-4" />
              </Button>
            ) : (
              <Button
                className="bg-green-600 hover:bg-green-700"
                disabled={!canProceed()}
              >
                Angebot anfordern
              </Button>
            )}
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default Calculator;