import React from 'react';
import { Routes, Route, useLocation } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import HomePage from '@/pages/HomePage';
import ServicesPage from '@/pages/ServicesPage';
import ContactPage from '@/pages/ContactPage';
import DeclutterPage from '@/pages/DeclutterPage';
import BlogPage from '@/pages/BlogPage';
import CalculatorPage from '@/pages/CalculatorPage';
import { Toaster } from '@/components/ui/toaster';

function App() {
  const location = useLocation();

  return (
    <div className="flex flex-col min-h-screen bg-gray-900 text-white">
      <Header />
      <main className="flex-grow">
        <AnimatePresence mode="wait">
          <Routes location={location} key={location.pathname}>
            <Route path="/" element={<HomePage />} />
            <Route path="/rechner" element={<CalculatorPage />} />
            <Route path="/entruempelung" element={<DeclutterPage />} />
            <Route path="/umzuege" element={<ServicesPage />} />
            <Route path="/ratgeber" element={<BlogPage />} />
            <Route path="/kontakt" element={<ContactPage />} />
          </Routes>
        </AnimatePresence>
      </main>
      <Footer />
      <Toaster />
    </div>
  );
}

export default App;