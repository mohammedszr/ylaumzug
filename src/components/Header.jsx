import React from 'react';
import { NavLink } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Trash2, Home, Briefcase, Mail, Sparkles, BookOpen, Calculator } from 'lucide-react';
const Header = () => {
  const activeLinkStyle = {
    color: '#a78bfa',
    borderBottom: '2px solid #a78bfa'
  };
  return <motion.header initial={{
    y: -100
  }} animate={{
    y: 0
  }} transition={{
    duration: 0.5,
    ease: 'easeOut'
  }} className="bg-gray-800/50 backdrop-blur-sm sticky top-0 z-50 shadow-lg">
      <nav className="container mx-auto px-6 py-4 flex justify-between items-center">
        <div className="flex items-center space-x-2">
          <Sparkles className="h-8 w-8 text-violet-400" />
          <span className="text-2xl font-bold text-white tracking-wider">YLA Umzug</span>
        </div>
        <ul className="flex items-center space-x-8">
          <li>
            <NavLink to="/" style={({
            isActive
          }) => isActive ? activeLinkStyle : undefined} className="flex items-center space-x-2 text-gray-300 hover:text-violet-400 transition-colors duration-300 pb-1">
              <Home size={20} />
              <span>Startseite</span>
            </NavLink>
          </li>
          <li>
            <NavLink to="/rechner" style={({
            isActive
          }) => isActive ? activeLinkStyle : undefined} className="flex items-center space-x-2 text-gray-300 hover:text-violet-400 transition-colors duration-300 pb-1">
              <Calculator size={20} />
              <span>Rechner</span>
            </NavLink>
          </li>
          <li>
            <NavLink to="/entruempelung" style={({
            isActive
          }) => isActive ? activeLinkStyle : undefined} className="flex items-center space-x-2 text-gray-300 hover:text-violet-400 transition-colors duration-300 pb-1">
              <Trash2 size={20} />
              <span>Entrümpelung</span>
            </NavLink>
          </li>
          <li>
            <NavLink to="/umzuege" style={({
            isActive
          }) => isActive ? activeLinkStyle : undefined} className="flex items-center space-x-2 text-gray-300 hover:text-violet-400 transition-colors duration-300 pb-1">
              <Briefcase size={20} />
              <span>Umzüge</span>
            </NavLink>
          </li>
          <li>
            <NavLink to="/ratgeber" style={({
            isActive
          }) => isActive ? activeLinkStyle : undefined} className="flex items-center space-x-2 text-gray-300 hover:text-violet-400 transition-colors duration-300 pb-1">
              <BookOpen size={20} />
              <span>Ratgeber</span>
            </NavLink>
          </li>
          <li>
            <NavLink to="/kontakt" style={({
            isActive
          }) => isActive ? activeLinkStyle : undefined} className="flex items-center space-x-2 text-gray-300 hover:text-violet-400 transition-colors duration-300 pb-1">
              <Mail size={20} />
              <span>Kontakt</span>
            </NavLink>
          </li>
        </ul>
      </nav>
    </motion.header>;
};
export default Header;