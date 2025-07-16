import React from 'react';
import { NavLink } from 'react-router-dom';
import { ChevronRight, Home } from 'lucide-react';
import { Helmet } from 'react-helmet';

const Breadcrumbs = ({ items }) => {
  // Generate Schema.org structured data for breadcrumbs
  const breadcrumbSchema = {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": items.map((item, index) => ({
      "@type": "ListItem",
      "position": index + 1,
      "name": item.name,
      "item": item.url ? `https://yla-umzug.de${item.url}` : undefined
    }))
  };

  return (
    <>
      <Helmet>
        <script type="application/ld+json">
          {JSON.stringify(breadcrumbSchema)}
        </script>
      </Helmet>
      
      <nav aria-label="Breadcrumb" className="mb-8">
        <ol className="flex items-center space-x-2 text-sm text-gray-400">
          <li>
            <NavLink 
              to="/" 
              className="flex items-center hover:text-violet-300 transition-colors duration-200"
              aria-label="Zur Startseite"
            >
              <Home size={16} />
              <span className="sr-only">Startseite</span>
            </NavLink>
          </li>
          
          {items.map((item, index) => (
            <li key={index} className="flex items-center">
              <ChevronRight size={16} className="mx-2 text-gray-500" />
              {item.url && index < items.length - 1 ? (
                <NavLink 
                  to={item.url}
                  className="hover:text-violet-300 transition-colors duration-200"
                >
                  {item.name}
                </NavLink>
              ) : (
                <span className="text-white font-medium" aria-current="page">
                  {item.name}
                </span>
              )}
            </li>
          ))}
        </ol>
      </nav>
    </>
  );
};

export default Breadcrumbs;