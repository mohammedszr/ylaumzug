import React, { useState } from 'react';
import { NavLink } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Trash2, Briefcase, Mail, Sparkles, BookOpen, Calculator, Menu, X } from 'lucide-react';
const Header = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  
  const activeLinkStyle = {
    color: '#a78bfa',
    borderBottom: '2px solid #a78bfa'
  };

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  const closeMenu = () => {
    setIsMenuOpen(false);
  };

  return (
    <motion.header 
      initial={{ y: -100 }} 
      animate={{ y: 0 }} 
      transition={{ duration: 0.5, ease: 'easeOut' }} 
      className="bg-gray-800/50 backdrop-blur-sm sticky top-0 z-50 shadow-lg"
    >
      <nav className="container mx-auto px-4 py-4">
        <div className="flex justify-between items-center">
          {/* Logo - Clickable to home */}
          <NavLink to="/" className="flex items-center space-x-2 hover:opacity-80 transition-opacity">
            <Sparkles className="h-8 w-8 text-violet-400" />
            <span className="text-2xl font-bold text-white tracking-wider">YLA Umzug</span>
          </NavLink>

          {/* Desktop Navigation */}
          <ul className="hidden md:flex items-center space-x-6 lg:space-x-8">
            <li>
              <NavLink 
                to="/rechner" 
                style={({ isActive }) => isActive ? activeLinkStyle : undefined} 
                className="flex items-center space-x-2 text-gray-300 hover:text-violet-400 transition-colors duration-300 pb-1"
              >
                <Calculator size={20} />
                <span>Rechner</span>
              </NavLink>
            </li>
            <li>
              <NavLink 
                to="/entruempelung" 
                style={({ isActive }) => isActive ? activeLinkStyle : undefined} 
                className="flex items-center space-x-2 text-gray-300 hover:text-violet-400 transition-colors duration-300 pb-1"
              >
                <Trash2 size={20} />
                <span>Entrümpelung</span>
              </NavLink>
            </li>
            <li>
              <NavLink 
                to="/umzuege" 
                style={({ isActive }) => isActive ? activeLinkStyle : undefined} 
                className="flex items-center space-x-2 text-gray-300 hover:text-violet-400 transition-colors duration-300 pb-1"
              >
                <Briefcase size={20} />
                <span>Umzüge</span>
              </NavLink>
            </li>
            <li>
              <NavLink 
                to="/ratgeber" 
                style={({ isActive }) => isActive ? activeLinkStyle : undefined} 
                className="flex items-center space-x-2 text-gray-300 hover:text-violet-400 transition-colors duration-300 pb-1"
              >
                <BookOpen size={20} />
                <span>Ratgeber</span>
              </NavLink>
            </li>
            <li>
              <NavLink 
                to="/kontakt" 
                style={({ isActive }) => isActive ? activeLinkStyle : undefined} 
                className="flex items-center space-x-2 text-gray-300 hover:text-violet-400 transition-colors duration-300 pb-1"
              >
                <Mail size={20} />
                <span>Kontakt</span>
              </NavLink>
            </li>
          </ul>

          {/* Mobile Menu Button */}
          <button
            onClick={toggleMenu}
            className="md:hidden text-white hover:text-violet-400 transition-colors p-2"
            aria-label="Toggle menu"
          >
            {isMenuOpen ? <X size={24} /> : <Menu size={24} />}
          </button>
        </div>

        {/* Mobile Navigation */}
        {isMenuOpen && (
          <motion.div
            initial={{ opacity: 0, height: 0 }}
            animate={{ opacity: 1, height: 'auto' }}
            exit={{ opacity: 0, height: 0 }}
            transition={{ duration: 0.3 }}
            className="md:hidden mt-4 pb-4 border-t border-gray-700"
          >
            <ul className="flex flex-col space-y-4 pt-4">
              <li>
                <NavLink 
                  to="/rechner" 
                  onClick={closeMenu}
                  style={({ isActive }) => isActive ? activeLinkStyle : undefined} 
                  className="flex items-center space-x-3 text-gray-300 hover:text-violet-400 transition-colors duration-300 py-2"
                >
                  <Calculator size={20} />
                  <span>Rechner</span>
                </NavLink>
              </li>
              <li>
                <NavLink 
                  to="/entruempelung" 
                  onClick={closeMenu}
                  style={({ isActive }) => isActive ? activeLinkStyle : undefined} 
                  className="flex items-center space-x-3 text-gray-300 hover:text-violet-400 transition-colors duration-300 py-2"
                >
                  <Trash2 size={20} />
                  <span>Entrümpelung</span>
                </NavLink>
              </li>
              <li>
                <NavLink 
                  to="/umzuege" 
                  onClick={closeMenu}
                  style={({ isActive }) => isActive ? activeLinkStyle : undefined} 
                  className="flex items-center space-x-3 text-gray-300 hover:text-violet-400 transition-colors duration-300 py-2"
                >
                  <Briefcase size={20} />
                  <span>Umzüge</span>
                </NavLink>
              </li>
              <li>
                <NavLink 
                  to="/ratgeber" 
                  onClick={closeMenu}
                  style={({ isActive }) => isActive ? activeLinkStyle : undefined} 
                  className="flex items-center space-x-3 text-gray-300 hover:text-violet-400 transition-colors duration-300 py-2"
                >
                  <BookOpen size={20} />
                  <span>Ratgeber</span>
                </NavLink>
              </li>
              <li>
                <NavLink 
                  to="/kontakt" 
                  onClick={closeMenu}
                  style={({ isActive }) => isActive ? activeLinkStyle : undefined} 
                  className="flex items-center space-x-3 text-gray-300 hover:text-violet-400 transition-colors duration-300 py-2"
                >
                  <Mail size={20} />
                  <span>Kontakt</span>
                </NavLink>
              </li>
            </ul>
          </motion.div>
        )}
      </nav>
    </motion.header>
  );
};
export default Header;