import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';

const pageVariants = {
  initial: { opacity: 0, x: -100 },
  in: { opacity: 1, x: 0 },
  out: { opacity: 0, x: 100 },
};

const pageTransition = {
  type: 'tween',
  ease: 'anticipate',
  duration: 0.5,
};

const AGBPage = () => {
  return (
    <motion.div
      initial="initial"
      animate="in"
      exit="out"
      variants={pageVariants}
      transition={pageTransition}
      className="container mx-auto px-6 py-16"
    >
      <Helmet>
        <title>AGB - Allgemeine Geschäftsbedingungen | YLA Umzugservice</title>
        <meta name="description" content="Allgemeine Geschäftsbedingungen der YLA Umzugservice für Umzug, Entrümpelung und Reinigungsleistungen" />
        <meta name="robots" content="noindex,follow" />
      </Helmet>

      <div className="max-w-4xl mx-auto">
        <motion.div
          initial={{ opacity: 0, y: -30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, delay: 0.2 }}
          className="text-center mb-12"
        >
          <h1 className="text-4xl md:text-5xl font-extrabold text-white leading-tight mb-4">
            Allgemeine Geschäftsbedingungen (AGB)
          </h1>
          <p className="text-lg text-violet-200">YLA Umzugservice</p>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, delay: 0.4 }}
          className="bg-gray-800/60 rounded-xl p-8 text-gray-300 space-y-8"
        >
          <div>
            <h2 className="text-xl font-bold text-white mb-4">1. Geltungsbereich</h2>
            <p>Diese AGB gelten für alle Verträge über Umzugs-, Entrümpelungs- und Reinigungsleistungen, die zwischen YLA Umzugservice (nachfolgend „Anbieter") und dem Kunden abgeschlossen werden, sowie für die Nutzung des Umzugsrechners und Angebots-Tools auf www.yla-umzug.de.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">2. Vertragsgegenstand</h2>
            <p>Der Anbieter stellt auf seiner Website einen interaktiven Rechner (Umzugsrechner) zur Verfügung, mit dem Kunden erste Preisangaben erhalten.</p>
            <p className="mt-4">Die mittels Rechner ermittelten Preise sind als nicht bindende Berechnung zu verstehen; ein verbindliches Angebot erhält der Kunde erst nach schriftlicher Bestätigung durch den Anbieter.</p>
            <p className="mt-4">Erst mit Annahme des schriftlichen Angebots durch den Kunden (z. B. per E-Mail, Post oder Online-Buchung) kommt ein verbindlicher Dienstleistungsvertrag zustande.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">3. Leistungsumfang</h2>
            <ul className="list-disc list-inside space-y-2">
              <li>Umfang und Art der Leistung ergeben sich aus dem verbindlichen Angebot.</li>
              <li>Zusatzleistungen (z. B. Möbellagerung, Verpackungsservice, Halteverbotszone) werden gesondert berechnet und im Angebot ausgewiesen.</li>
              <li>Terminvereinbarungen sind verbindlich; der Kunde teilt terminrelevante Informationen (Zugänglichkeit, Aufzug, Parkmöglichkeiten) rechtzeitig mit.</li>
            </ul>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">4. Preise und Zahlungsbedingungen</h2>
            <ul className="list-disc list-inside space-y-2">
              <li>Es gelten die im Angebot genannten Preise.</li>
              <li>Zahlbar ist der Rechnungsbetrag innerhalb von 14 Tagen nach Rechnungsstellung ohne Abzug.</li>
              <li>Bei kurzfristigen Anfragen (Wunschdatum &lt; 7 Tage) behält sich der Anbieter einen Aufschlag vor.</li>
              <li>Bei Zahlungsverzug ist der Anbieter berechtigt, Verzugszinsen in gesetzlicher Höhe zu verlangen.</li>
            </ul>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">5. Stornierung und Kündigung</h2>
            <ul className="list-disc list-inside space-y-2">
              <li>Eine kostenfreie Stornierung des verbindlichen Auftrags ist bis 7 Tage vor dem vereinbarten Termin möglich.</li>
              <li>Bei späterer Stornierung oder Nichterscheinen des Kunden berechnet der Anbieter pauschal 50 % des vereinbarten Preises.</li>
              <li>Der Anbieter kann aus wichtigem Grund (z. B. höhere Gewalt, unvorhersehbare technische Probleme) den Termin verschieben oder den Vertrag kündigen; bereits geleistete Zahlungen werden erstattet.</li>
            </ul>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">6. Pflichten des Kunden</h2>
            <ul className="list-disc list-inside space-y-2">
              <li>Der Kunde sorgt für einen sicheren und ungehinderten Zugang zum Umzugs- oder Entrümpelungsort.</li>
              <li>Der Kunde informiert vorab über besondere Gefährdungen oder empfindliche Gegenstände.</li>
              <li>Der Kunde stellt sicher, dass alle erforderlichen Genehmigungen (z. B. Halteverbotszone) rechtzeitig beantragt werden.</li>
            </ul>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">7. Haftung</h2>
            <ul className="list-disc list-inside space-y-2">
              <li>Der Anbieter haftet für Vorsatz und grobe Fahrlässigkeit unbeschränkt.</li>
              <li>Für einfache Fahrlässigkeit haftet der Anbieter nur bei Verletzung wesentlicher Vertragspflichten (Kardinalpflichten), begrenzt auf den typischerweise vorhersehbaren Schaden.</li>
              <li>Die Haftung für entgangenen Gewinn und mittelbare Schäden ist ausgeschlossen.</li>
              <li>Die Regelungen zur Haftungsbeschränkung gelten nicht für Schäden aus der Verletzung des Lebens, des Körpers oder der Gesundheit.</li>
            </ul>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">8. Nutzungsrechte am Online-Tool</h2>
            <ul className="list-disc list-inside space-y-2">
              <li>Der Anbieter gewährt dem Nutzer ein einfaches, nicht übertragbares Recht, den Umzugsrechner ausschließlich zu Zwecken der Angebotsermittlung zu verwenden.</li>
              <li>Jegliche Vervielfältigung, Verbreitung oder öffentliche Zugänglichmachung des Tools oder seiner Inhalte ist untersagt.</li>
            </ul>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">9. Datenschutz</h2>
            <p>Die Erhebung und Verarbeitung personenbezogener Daten erfolgt gemäß unserer Datenschutzerklärung. Mit Annahme dieser AGB erkennt der Kunde die Datenschutzerklärung in der jeweils gültigen Fassung an.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">10. Widerrufsbelehrung</h2>
            <p>Verbrauchern steht ein gesetzliches Widerrufsrecht zu. Einzelheiten entnehmen Sie bitte unserer Widerrufsbelehrung.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">11. Online-Streitbeilegung</h2>
            <p>Die EU-Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit: <a href="https://ec.europa.eu/consumers/odr" className="text-violet-400 hover:text-violet-300" target="_blank" rel="noopener noreferrer">https://ec.europa.eu/consumers/odr</a></p>
            <p className="mt-2">Wir sind nicht verpflichtet, an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">12. Schlussbestimmungen</h2>
            <ul className="list-disc list-inside space-y-2">
              <li>Es gilt deutsches Recht.</li>
              <li>Gerichtsstand ist, sofern zulässig, Kaiserslautern.</li>
              <li>Sollte eine Bestimmung dieser AGB unwirksam sein, berührt dies nicht die Wirksamkeit der übrigen Bestimmungen.</li>
            </ul>
          </div>

          <div className="pt-4 border-t border-gray-700">
            <p className="text-sm text-gray-400">Stand: Juli 2025</p>
          </div>
        </motion.div>
      </div>
    </motion.div>
  );
};

export default AGBPage;