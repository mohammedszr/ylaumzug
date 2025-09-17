import React, { useState, useCallback, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Button } from '@/components/ui/button';
import { ChevronLeft, Sparkles, Zap, Heart, ArrowRight } from 'lucide-react';
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
        { id: 'moving', title: 'Umzug Details', component: MovingDetails, condition: () => calculatorData.selectedServices[0] === 'umzug' },
        { id: 'cleaning', title: 'Putzservice Details', component: CleaningDetails, condition: () => calculatorData.selectedServices[0] === 'putzservice' },
        { id: 'declutter', title: 'Entrümpelung Details', component: DeclutterDetails, condition: () => calculatorData.selectedServices[0] === 'entruempelung' },
        { id: 'summary', title: 'Kostenübersicht', component: PriceSummary },
        { id: 'contact', title: 'Kontaktdaten', component: GeneralInfo }
    ];

    // Filter steps based on selected services
    const activeSteps = steps.filter(step => !step.condition || step.condition());

    const updateCalculatorData = useCallback((stepData, stepKey) => {
        setCalculatorData(prev => ({
            ...prev,
            [stepKey]: stepData
        }));
    }, []);

    const nextStep = useCallback(() => {
        if (currentStep < activeSteps.length - 1) {
            setCurrentStep(currentStep + 1);
        }
    }, [currentStep, activeSteps.length]);

    const prevStep = useCallback(() => {
        if (currentStep > 0) {
            setCurrentStep(currentStep - 1);
        }
    }, [currentStep]);

    const canProceed = () => {
        const currentStepData = activeSteps[currentStep];
        if (!currentStepData) return false;
        
        switch (currentStepData.id) {
            case 'service':
                return calculatorData.selectedServices && calculatorData.selectedServices.length > 0;
            case 'moving':
                return calculatorData.movingDetails.rooms && 
                       calculatorData.movingDetails.fromAddress && 
                       calculatorData.movingDetails.fromAddress.street &&
                       calculatorData.movingDetails.toAddress && 
                       calculatorData.movingDetails.toAddress.street;
            case 'cleaning':
                return calculatorData.cleaningDetails.objectType && 
                       calculatorData.cleaningDetails.size &&
                       calculatorData.cleaningDetails.cleaningIntensity;
            case 'declutter':
                return calculatorData.declutterDetails.objectType && 
                       calculatorData.declutterDetails.size &&
                       calculatorData.declutterDetails.address &&
                       calculatorData.declutterDetails.address.street;
            case 'contact':
                return calculatorData.generalInfo.name && 
                       calculatorData.generalInfo.email && 
                       calculatorData.generalInfo.phone;
            default:
                return true;
        }
    };

    // Auto-progression with proper debouncing - DISABLED to prevent white screen
    // TODO: Implement auto-progression without infinite loops
    // useEffect(() => {
    //     let timeoutId;
        
    //     if (canProceed() && currentStep < activeSteps.length - 1) {
    //         timeoutId = setTimeout(() => {
    //             setCurrentStep(prev => prev + 1);
    //         }, 500);
    //     }
        
    //     return () => {
    //         if (timeoutId) {
    //             clearTimeout(timeoutId);
    //         }
    //     };
    // }, [calculatorData.selectedServices, calculatorData.movingDetails, calculatorData.cleaningDetails, calculatorData.declutterDetails, calculatorData.generalInfo]);

    const CurrentStepComponent = activeSteps[currentStep]?.component;

    // Safety check to prevent white screen
    if (!CurrentStepComponent) {
        console.error('No component found for current step:', currentStep, activeSteps);
        return (
            <div className="max-w-4xl mx-auto p-3 sm:p-6">
                <div className="bg-gradient-to-br from-gray-800/95 to-gray-900/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-violet-500/20 overflow-hidden p-8">
                    <div className="text-center py-8">
                        <Sparkles className="w-12 h-12 text-violet-400 mx-auto mb-4" />
                        <h3 className="text-xl font-bold text-white mb-2">Rechner wird geladen...</h3>
                        <p className="text-violet-200/80">Einen Moment bitte</p>
                        <Button 
                            onClick={() => setCurrentStep(0)} 
                            className="mt-4 bg-violet-600 hover:bg-violet-700"
                        >
                            Neu starten
                        </Button>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="max-w-4xl mx-auto p-3 sm:p-6">
            {/* Beautiful glassmorphism card with mobile-first design */}
            <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, ease: "easeOut" }}
                className="bg-gradient-to-br from-gray-800/95 to-gray-900/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-violet-500/20 overflow-hidden"
            >
                {/* Static background - no heavy animations */}
                <div className="absolute inset-0 overflow-hidden pointer-events-none">
                    <div className="absolute inset-0 bg-gradient-to-br from-violet-600/5 via-purple-600/5 to-pink-600/5" />
                </div>

                {/* Header with enhanced mobile design */}
                <div className="relative text-center p-6 sm:p-8 pb-4 sm:pb-6">
                    <motion.div
                        initial={{ scale: 0.8, opacity: 0 }}
                        animate={{ scale: 1, opacity: 1 }}
                        transition={{ duration: 0.5, delay: 0.2 }}
                        className="flex items-center justify-center mb-4"
                    >
                        <div className="bg-gradient-to-r from-violet-500 to-purple-600 p-3 rounded-2xl shadow-lg">
                            <Zap className="w-6 h-6 sm:w-8 sm:h-8 text-white" />
                        </div>
                    </motion.div>

                    <motion.h1
                        initial={{ y: -20, opacity: 0 }}
                        animate={{ y: 0, opacity: 1 }}
                        transition={{ duration: 0.6, delay: 0.3 }}
                        className="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2"
                    >
                        Umzug berechnen
                    </motion.h1>

                    <motion.p
                        initial={{ y: -10, opacity: 0 }}
                        animate={{ y: 0, opacity: 1 }}
                        transition={{ duration: 0.6, delay: 0.4 }}
                        className="text-sm sm:text-base text-violet-200/80 mb-6"
                    >
                        Schnell • Einfach • Kostenlos
                    </motion.p>

                    {/* Enhanced mobile-friendly stepper */}
                    <motion.div
                        initial={{ scale: 0.9, opacity: 0 }}
                        animate={{ scale: 1, opacity: 1 }}
                        transition={{ duration: 0.5, delay: 0.5 }}
                    >
                        <CalculatorStepper
                            steps={activeSteps}
                            currentStep={currentStep}
                        />
                    </motion.div>
                </div>

                {/* Main content area with enhanced mobile UX */}
                <div className="relative p-4 sm:p-6 lg:p-8">
                    <AnimatePresence mode="wait">
                        <motion.div
                            key={currentStep}
                            initial={{ opacity: 0, x: 20, scale: 0.98 }}
                            animate={{ opacity: 1, x: 0, scale: 1 }}
                            exit={{ opacity: 0, x: -20, scale: 0.98 }}
                            transition={{
                                duration: 0.4,
                                ease: [0.4, 0, 0.2, 1],
                                scale: { duration: 0.3 }
                            }}
                            className="min-h-[300px] sm:min-h-[400px]"
                        >
                            {(() => {
                                try {
                                    return CurrentStepComponent ? (
                                        <CurrentStepComponent
                                            data={calculatorData}
                                            updateData={updateCalculatorData}
                                            onNext={nextStep}
                                        />
                                    ) : (
                                        <div className="text-center py-8">
                                            <p className="text-white">Schritt wird geladen...</p>
                                        </div>
                                    );
                                } catch (error) {
                                    console.error('Error rendering step component:', error);
                                    return (
                                        <div className="text-center py-8">
                                            <p className="text-red-400">Fehler beim Laden des Schritts</p>
                                            <Button 
                                                onClick={() => setCurrentStep(0)} 
                                                className="mt-4 bg-violet-600 hover:bg-violet-700"
                                            >
                                                Neu starten
                                            </Button>
                                        </div>
                                    );
                                }
                            })()}
                        </motion.div>
                    </AnimatePresence>

                    {/* Enhanced mobile-friendly navigation */}
                    <motion.div
                        initial={{ y: 20, opacity: 0 }}
                        animate={{ y: 0, opacity: 1 }}
                        transition={{ duration: 0.5, delay: 0.6 }}
                        className="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8 pt-6 border-t border-gray-700/50"
                    >
                        {/* Back button with enhanced mobile touch target */}
                        <Button
                            variant="outline"
                            onClick={prevStep}
                            disabled={currentStep === 0}
                            className="w-full sm:w-auto min-h-[48px] flex items-center justify-center space-x-2 bg-gray-800/50 border-gray-600/50 text-gray-300 hover:bg-gray-700/50 hover:border-violet-500/50 hover:text-white transition-all duration-300 rounded-xl touch-manipulation"
                        >
                            <ChevronLeft className="h-4 w-4" />
                            <span className="font-medium">Zurück</span>
                        </Button>

                        {/* Progress indicator for mobile */}
                        <div className="flex items-center space-x-2 text-sm text-violet-300/70">
                            <span>{currentStep + 1}</span>
                            <span>von</span>
                            <span>{activeSteps.length}</span>
                        </div>

                        {/* Next/Submit button with enhanced mobile design */}
                        {currentStep < activeSteps.length - 1 ? (
                            <motion.div
                                whileTap={{ scale: 0.98 }}
                                className="w-full sm:w-auto"
                            >
                                <Button
                                    onClick={nextStep}
                                    disabled={!canProceed()}
                                    className="w-full sm:w-auto min-h-[48px] flex items-center justify-center space-x-2 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300 rounded-xl touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span>Weiter</span>
                                    <ArrowRight className="h-4 w-4" />
                                </Button>
                            </motion.div>
                        ) : (
                            <motion.div
                                whileTap={{ scale: 0.98 }}
                                className="w-full sm:w-auto"
                            >
                                <Button
                                    className="w-full sm:w-auto min-h-[48px] flex items-center justify-center space-x-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300 rounded-xl touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled={!canProceed()}
                                >
                                    <Heart className="h-4 w-4" />
                                    <span>Angebot anfordern</span>
                                </Button>
                            </motion.div>
                        )}
                    </motion.div>
                </div>
            </motion.div>
        </div>
    );
};

export default Calculator;