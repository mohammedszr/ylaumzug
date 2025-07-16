import React, { Suspense, lazy } from 'react';
import { Routes, Route, useLocation } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { Toaster } from '@/components/ui/toaster';
import { CookieBanner } from '@/components/ui/cookie-banner';
import WhatsAppFloat from '@/components/ui/whatsapp-float';

// Lazy load pages for better performance
const HomePage = lazy(() => import('@/pages/HomePage'));
const CalculatorPage = lazy(() => import('@/pages/CalculatorPage'));
const ServicesPage = lazy(() => import('@/pages/ServicesPage'));
const ContactPage = lazy(() => import('@/pages/ContactPage'));
const DeclutterPage = lazy(() => import('@/pages/DeclutterPage'));
const BlogPage = lazy(() => import('@/pages/BlogPage'));
const EntruempelungRatgeberPage = lazy(() => import('@/pages/EntruempelungRatgeberPage'));
const UmzugRatgeberPage = lazy(() => import('@/pages/UmzugRatgeberPage'));
const HausreinigungRatgeberPage = lazy(() => import('@/pages/HausreinigungRatgeberPage'));
const HartzIVUmzugRatgeberPage = lazy(() => import('@/pages/HartzIVUmzugRatgeberPage'));
const AGBPage = lazy(() => import('@/pages/AGBPage'));
const DatenschutzPage = lazy(() => import('@/pages/DatenschutzPage'));
const ImpressumPage = lazy(() => import('@/pages/ImpressumPage'));

// Loading component for better UX
const PageLoader = () => (
  <div className="flex items-center justify-center min-h-screen bg-gray-900">
    <div className="w-8 h-8 border-3 border-violet-500/30 border-t-violet-500 rounded-full animate-spin"></div>
  </div>
);

function App() {
  const location = useLocation();

  return (
    <div className="flex flex-col min-h-screen bg-gray-900 text-white">
      <Header />
      <main className="flex-grow">
        <Suspense fallback={<PageLoader />}>
          <AnimatePresence mode="wait">
            <Routes location={location} key={location.pathname}>
              <Route path="/" element={<HomePage />} />
              <Route path="/rechner" element={<CalculatorPage />} />
              <Route path="/entruempelung" element={<DeclutterPage />} />
              <Route path="/umzuege" element={<ServicesPage />} />
              <Route path="/ratgeber" element={<BlogPage />} />
              <Route path="/ratgeber-entruempelung-5-schritte" element={<EntruempelungRatgeberPage />} />
              <Route path="/ratgeber-umzug-checkliste" element={<UmzugRatgeberPage />} />
              <Route path="/ratgeber-hausreinigung-endreinigung" element={<HausreinigungRatgeberPage />} />
              <Route path="/ratgeber-hartz-iv-umzug-jobcenter" element={<HartzIVUmzugRatgeberPage />} />
              <Route path="/kontakt" element={<ContactPage />} />
              <Route path="/impressum" element={<ImpressumPage />} />
              <Route path="/datenschutz" element={<DatenschutzPage />} />
              <Route path="/agb" element={<AGBPage />} />
            </Routes>
          </AnimatePresence>
        </Suspense>
      </main>
      <Footer />
      <Toaster />
      <CookieBanner />
      <WhatsAppFloat />
    </div>
  );
}

export default App;