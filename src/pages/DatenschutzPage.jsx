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

const DatenschutzPage = () => {
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
        <title>Datenschutzerklärung - YLA Umzugservice</title>
        <meta name="description" content="Datenschutzerklärung der YLA Umzugservice - Informationen zum Umgang mit Ihren Daten" />
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
            Datenschutzerklärung
          </h1>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, delay: 0.4 }}
          className="bg-gray-800/60 rounded-xl p-8 text-gray-300 space-y-8"
        >
          <div>
            <h2 className="text-xl font-bold text-white mb-4">1. Verantwortlicher</h2>
            <div className="space-y-2">
              <p>YLA Umzugservice<br />Inhaberin: M. Hanifa<br />Hackstr. 4<br />67655 Kaiserslautern<br />Deutschland</p>
              <p><strong>Telefon:</strong> +49 1575 0693353<br /><strong>E-Mail:</strong> info@yla-umzug.de<br /><strong>Website:</strong> www.yla-umzug.de</p>
            </div>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">2. Allgemeine Hinweise</h2>
            <p>Wir nehmen den Schutz Ihrer persönlichen Daten sehr ernst. Wir behandeln Ihre personenbezogenen Daten vertraulich und entsprechend der gesetzlichen Datenschutzvorschriften sowie dieser Datenschutzerklärung.</p>
            <p className="mt-4">Diese Erklärung erläutert Ihnen, welche Daten wir erheben, verarbeiten und nutzen, wenn Sie unsere Website besuchen oder unsere Dienstleistungen in Anspruch nehmen.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">3. Zwecke und Rechtsgrundlagen der Verarbeitung</h2>
            
            <div className="space-y-6">
              <div>
                <h3 className="text-lg font-semibold text-white mb-2">3.1 Datenverarbeitung beim Besuch der Website</h3>
                <p>Bei jedem Zugriff auf unsere Website werden automatisch Daten durch unseren Webserver erfasst:</p>
                <ul className="list-disc list-inside mt-2 space-y-1">
                  <li>IP-Adresse</li>
                  <li>Datum und Uhrzeit des Zugriffs</li>
                  <li>Browsertyp und -version</li>
                  <li>verwendetes Betriebssystem</li>
                  <li>Referrer-URL</li>
                  <li>Hostname des zugreifenden Rechners</li>
                </ul>
                <p className="mt-2">Diese Daten dienen der Sicherstellung des technischen Betriebs, der Sicherheit unserer Systeme und der Optimierung unserer Website.</p>
                <p className="mt-2"><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse)</p>
              </div>

              <div>
                <h3 className="text-lg font-semibold text-white mb-2">3.2 Kontaktformular & Angebotsanfrage</h3>
                <p>Wenn Sie unser Kontaktformular nutzen oder eine Angebotsanfrage stellen, verarbeiten wir Ihre Daten zur Bearbeitung Ihrer Anfrage und für mögliche Rückfragen.</p>
                <p className="mt-2"><strong>Erhobene Daten:</strong></p>
                <ul className="list-disc list-inside mt-2 space-y-1">
                  <li>Name</li>
                  <li>Telefonnummer</li>
                  <li>E-Mail-Adresse</li>
                  <li>Adresse (bei Umzugsanfragen)</li>
                  <li>Informationen zu Ihrem Umzug / Auftrag</li>
                  <li>Nachrichtentext</li>
                </ul>
                <p className="mt-2"><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. b DSGVO (vorvertragliche Maßnahmen oder Vertragserfüllung)</p>
              </div>

              <div>
                <h3 className="text-lg font-semibold text-white mb-2">3.3 Angebotsrechner</h3>
                <p>Über unseren Angebotsrechner können Sie eine individuelle Preiskalkulation durchführen. Dabei werden folgende Daten verarbeitet:</p>
                <ul className="list-disc list-inside mt-2 space-y-1">
                  <li>Ausgangs- und Zieladresse</li>
                  <li>Wohnungsgröße</li>
                  <li>Anzahl der Kartons / Möbelstücke</li>
                  <li>gewünschte Zusatzleistungen</li>
                  <li>gewünschter Termin</li>
                </ul>
                <p className="mt-2">Diese Daten dienen ausschließlich der Angebotserstellung.</p>
                <p className="mt-2"><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. b DSGVO</p>
              </div>

              <div>
                <h3 className="text-lg font-semibold text-white mb-2">3.4 Cookies und ähnliche Technologien</h3>
                <p>Wir verwenden Cookies und ähnliche Technologien (z. B. Web Beacons, Pixel), um unsere Website nutzerfreundlicher und sicherer zu gestalten.</p>
                <div className="mt-4 space-y-3">
                  <div>
                    <p><strong>Technisch notwendige Cookies</strong><br />Unbedingt erforderlich für den Betrieb der Website.<br />Rechtsgrundlage: Art. 6 Abs. 1 lit. f DSGVO</p>
                  </div>
                  <div>
                    <p><strong>Funktionale Cookies</strong><br />Ermöglichen zusätzliche Funktionen oder Komfort.<br />Rechtsgrundlage: Art. 6 Abs. 1 lit. a DSGVO (Einwilligung)</p>
                  </div>
                  <div>
                    <p><strong>Statistik-/Analyse-Cookies</strong><br />Helfen uns, das Nutzungsverhalten zu verstehen.<br />Rechtsgrundlage: Art. 6 Abs. 1 lit. a DSGVO</p>
                  </div>
                  <div>
                    <p><strong>Marketing-Cookies</strong><br />Dienen zur Anzeige personalisierter Werbung.<br />Rechtsgrundlage: Art. 6 Abs. 1 lit. a DSGVO</p>
                  </div>
                </div>
                <p className="mt-4"><strong>Cookie-Einwilligung:</strong><br />Nicht notwendige Cookies werden erst gesetzt, wenn Sie ausdrücklich einwilligen. Ihre Einwilligung können Sie jederzeit über unser Cookie-Tool oder durch Löschen der Cookies im Browser widerrufen.</p>
              </div>
            </div>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">4. Empfänger der Daten</h2>
            <p>Wir geben Ihre personenbezogenen Daten grundsätzlich nicht an Dritte weiter, außer:</p>
            <ul className="list-disc list-inside mt-2 space-y-1">
              <li>dies ist zur Vertragserfüllung erforderlich</li>
              <li>wir sind gesetzlich dazu verpflichtet</li>
              <li>Sie haben ausdrücklich eingewilligt</li>
            </ul>
            <p className="mt-2">Beispiele: IT-Dienstleister, Webhosting-Anbieter, Steuerberater (nur soweit erforderlich).</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">5. Übermittlung in Drittstaaten</h2>
            <p>Eine Übermittlung personenbezogener Daten in Drittstaaten (außerhalb EU/EWR) findet nur statt, wenn:</p>
            <ul className="list-disc list-inside mt-2 space-y-1">
              <li>es für die Vertragserfüllung erforderlich ist,</li>
              <li>gesetzliche Vorschriften dies verlangen, oder</li>
              <li>Sie ausdrücklich eingewilligt haben.</li>
            </ul>
            <p className="mt-2">In diesen Fällen stellen wir sicher, dass geeignete Garantien zum Schutz Ihrer Daten bestehen (z. B. Standardvertragsklauseln der EU-Kommission).</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">6. Speicherdauer</h2>
            <p>Wir speichern personenbezogene Daten nur so lange, wie es für die jeweiligen Zwecke erforderlich ist oder wie es gesetzliche Aufbewahrungspflichten verlangen.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">7. Automatisierte Entscheidungsfindung / Profiling</h2>
            <p>Eine automatisierte Entscheidungsfindung oder Profiling gemäß Art. 22 DSGVO findet nicht statt.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">8. Ihre Rechte</h2>
            <p>Sie haben das Recht:</p>
            <ul className="list-disc list-inside mt-2 space-y-1">
              <li>Auskunft über Ihre gespeicherten Daten zu erhalten (Art. 15 DSGVO)</li>
              <li>Berichtigung unrichtiger Daten zu verlangen (Art. 16 DSGVO)</li>
              <li>Löschung Ihrer Daten zu verlangen (Art. 17 DSGVO)</li>
              <li>Einschränkung der Verarbeitung zu verlangen (Art. 18 DSGVO)</li>
              <li>Datenübertragbarkeit zu verlangen (Art. 20 DSGVO)</li>
              <li>Widerspruch gegen die Verarbeitung einzulegen (Art. 21 DSGVO)</li>
            </ul>
            <p className="mt-4">Bitte richten Sie Ihre Anfrage an: <strong>info@yla-umzug.de</strong></p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">9. Widerruf von Einwilligungen</h2>
            <p>Sie können eine einmal erteilte Einwilligung jederzeit mit Wirkung für die Zukunft widerrufen. Die Rechtmäßigkeit der bis zum Widerruf erfolgten Verarbeitung bleibt unberührt.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">10. Beschwerderecht bei der Aufsichtsbehörde</h2>
            <p>Sie haben das Recht, sich bei der zuständigen Datenschutzaufsichtsbehörde zu beschweren.</p>
            <div className="mt-4">
              <p><strong>Zuständige Aufsichtsbehörde in Rheinland-Pfalz:</strong></p>
              <p>Landesbeauftragter für den Datenschutz und die Informationsfreiheit Rheinland-Pfalz<br />
              Hintere Bleiche 34<br />
              55116 Mainz<br />
              Deutschland<br />
              Telefon: +49 (0) 6131 208-2449<br />
              E-Mail: poststelle@datenschutz.rlp.de</p>
            </div>
          </div>

          <div>
            <h2 className="text-xl font-bold text-white mb-4">11. Änderung dieser Datenschutzerklärung</h2>
            <p>Wir behalten uns vor, diese Datenschutzerklärung zu ändern, um sie an geänderte Rechtslagen oder Änderungen unserer Leistungen anzupassen.</p>
          </div>

          <div className="pt-4 border-t border-gray-700">
            <p className="text-sm text-gray-400">Stand: Juli 2025</p>
          </div>
        </motion.div>
      </div>
    </motion.div>
  );
};

export default DatenschutzPage;