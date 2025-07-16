import { useState, useEffect } from 'react';
import { X } from 'lucide-react';

export function CookieBanner() {
  const [isVisible, setIsVisible] = useState(false);
  const [preferences, setPreferences] = useState({
    necessary: true,
    analytics: false,
    marketing: false,
  });

  useEffect(() => {
    const consent = localStorage.getItem('cookie-consent');
    if (!consent) {
      setIsVisible(true);
    }
  }, []);

  const acceptAll = () => {
    const consent = {
      necessary: true,
      analytics: true,
      marketing: true,
      timestamp: new Date().toISOString(),
    };
    localStorage.setItem('cookie-consent', JSON.stringify(consent));
    setIsVisible(false);
  };

  const acceptSelected = () => {
    const consent = {
      ...preferences,
      timestamp: new Date().toISOString(),
    };
    localStorage.setItem('cookie-consent', JSON.stringify(consent));
    setIsVisible(false);
  };

  const acceptNecessary = () => {
    const consent = {
      necessary: true,
      analytics: false,
      marketing: false,
      timestamp: new Date().toISOString(),
    };
    localStorage.setItem('cookie-consent', JSON.stringify(consent));
    setIsVisible(false);
  };

  if (!isVisible) return null;

  return (
    <div className="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-lg">
      <div className="max-w-7xl mx-auto p-4">
        <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div className="flex-1">
            <h3 className="font-semibold text-gray-900 mb-2">Cookie-Einstellungen</h3>
            <p className="text-sm text-gray-600 leading-relaxed">
              Wir verwenden Cookies und ähnliche Technologien, um unsere Website sicher und zuverlässig zu betreiben, 
              Inhalte und Anzeigen zu personalisieren sowie die Zugriffe auf unsere Website zu analysieren. 
              Einige Cookies sind notwendig, andere dienen statistischen Zwecken oder der Anzeige personalisierter Inhalte.
            </p>
            <p className="text-sm text-gray-600 mt-1">
              Sie können selbst entscheiden, welche Cookies Sie zulassen möchten. Ihre Auswahl können Sie jederzeit ändern.
            </p>
          </div>
          
          <div className="flex flex-col sm:flex-row gap-2 lg:ml-4">
            <button
              onClick={acceptAll}
              className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium"
            >
              Alle akzeptieren
            </button>
            <button
              onClick={acceptSelected}
              className="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium"
            >
              Einstellungen speichern
            </button>
            <button
              onClick={acceptNecessary}
              className="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium"
            >
              Nur notwendige Cookies
            </button>
          </div>
        </div>
        
        {/* Cookie Preferences (expandable) */}
        <details className="mt-4">
          <summary className="cursor-pointer text-sm text-blue-600 hover:text-blue-800">
            Cookie-Einstellungen anpassen
          </summary>
          <div className="mt-3 space-y-3">
            <div className="flex items-center justify-between">
              <div>
                <label className="text-sm font-medium text-gray-900">Notwendige Cookies</label>
                <p className="text-xs text-gray-600">Erforderlich für die Grundfunktionen der Website</p>
              </div>
              <input
                type="checkbox"
                checked={true}
                disabled
                className="rounded border-gray-300"
              />
            </div>
            <div className="flex items-center justify-between">
              <div>
                <label className="text-sm font-medium text-gray-900">Analyse-Cookies</label>
                <p className="text-xs text-gray-600">Helfen uns, die Website zu verbessern</p>
              </div>
              <input
                type="checkbox"
                checked={preferences.analytics}
                onChange={(e) => setPreferences(prev => ({ ...prev, analytics: e.target.checked }))}
                className="rounded border-gray-300"
              />
            </div>
            <div className="flex items-center justify-between">
              <div>
                <label className="text-sm font-medium text-gray-900">Marketing-Cookies</label>
                <p className="text-xs text-gray-600">Für personalisierte Werbung und Inhalte</p>
              </div>
              <input
                type="checkbox"
                checked={preferences.marketing}
                onChange={(e) => setPreferences(prev => ({ ...prev, marketing: e.target.checked }))}
                className="rounded border-gray-300"
              />
            </div>
          </div>
        </details>
      </div>
    </div>
  );
}