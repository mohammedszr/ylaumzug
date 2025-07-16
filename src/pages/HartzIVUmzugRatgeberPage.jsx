import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { CheckCircle, FileText, Phone, MessageCircle, ArrowRight, MapPin, Euro, Clock } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { NavLink } from 'react-router-dom';

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

const anerkannteGruende = [
  {
    title: "Familienzuwachs",
    description: "Die Wohnung ist zu klein für das wachsende Familienglück"
  },
  {
    title: "Arbeitsaufnahme",
    description: "Die neue Arbeitsstelle ist zu weit entfernt, der Arbeitsweg ist unzumutbar"
  },
  {
    title: "Veränderte Lebenssituation",
    description: "Heirat oder Scheidung erfordern eine neue Wohnsituation"
  },
  {
    title: "Unzumutbarer Wohnzustand",
    description: "Schimmel, Stromausfall oder Wasserschäden, die nicht durch Ihr Verschulden entstanden sind"
  },
  {
    title: "Kündigung durch Vermieter",
    description: "Ohne gerechtfertigten Grund"
  },
  {
    title: "Gesundheitliche Gründe",
    description: "Erkrankung oder Altersbeschwerden, die das Weiterwohnen unmöglich machen"
  },
  {
    title: "Aufforderung durch das Jobcenter",
    description: "Z.B. bei Überschreitung der Mietobergrenze"
  }
];

const antragSchritte = [
  {
    number: 1,
    title: "Vorlauf planen",
    description: "Antrag am besten mindestens 4 Wochen vor dem geplanten Umzug abgeben"
  },
  {
    number: 2,
    title: "Kostenvoranschläge",
    description: "Mindestens drei Angebote für Umzugsunternehmen bzw. Umzugstransport einholen"
  },
  {
    number: 3,
    title: "Antrag ausfüllen",
    description: "Formular des Jobcenters sorgfältig ausfüllen – Grund des Umzugs angeben"
  },
  {
    number: 4,
    title: "Nachweise beifügen",
    description: "Fotos vom alten Wohnzustand, ärztliche Atteste, Arbeitsvertrag, Mietvertrag etc. beilegen"
  },
  {
    number: 5,
    title: "Abgabe & Fristen",
    description: "Antrag persönlich im Jobcenter abgeben oder per Einschreiben senden; Eingangsbestätigung sichern"
  },
  {
    number: 6,
    title: "Bei Ablehnung reagieren",
    description: "Rechtsgrundlage erfragen und ggf. Widerspruch einlegen (Frist: 1 Monat)"
  }
];

const checkliste = [
  "Ausgefüllter Antrag auf Kostenübernahme",
  "Drei Kostenvoranschläge (Umzugsfirma, Transporter, Kartons)",
  "Nachweis über den neuen Mietvertrag (inkl. Kaution)",
  "Mietspiegel oder Mieterhöhung als Hinweis auf unzumutbare Miete",
  "Ärztliches Attest (bei gesundheitlichen Umzugsgründen)",
  "Fotos des schlechten Wohnzustands (z.B. Schimmel)"
];

const regions = [
  "Saarbrücken", "Trier", "Kaiserslautern", "Mainz", "Koblenz", "Ludwigshafen"
];

const HartzIVUmzugRatgeberPage = () => {
  const whatsappMessage = encodeURIComponent("Hallo! Ich benötige einen Kostenvoranschlag für einen Bürgergeld-Umzug für das Jobcenter. Können Sie mir dabei helfen?");
  const whatsappUrl = `https://wa.me/4915750693353?text=${whatsappMessage}`;

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
        <title>Bürgergeld-Umzug: Jobcenter Kostenübernahme - Antrag & Voraussetzungen | YLA Umzug</title>
        <meta name="description" content="Bürgergeld-Umzug: So übernimmt das Jobcenter Ihre Umzugskosten! Antrag, Voraussetzungen & Kostenvoranschlag für Bürgergeld-Empfänger in RLP & Saarland." />
        <meta name="keywords" content="Bürgergeld Umzug, Umzugskostenübernahme Jobcenter, Kostenvoranschlag Jobcenter Umzug, Antrag Umzugskosten Bürgergeld, Jobcenter Umzug Voraussetzungen, ALG II Umzug" />
        <link rel="canonical" href="/ratgeber-hartz-iv-umzug-jobcenter" />
        <script type="application/ld+json">
          {JSON.stringify({
            "@context": "https://schema.org",
            "@type": "HowTo",
            "name": "Bürgergeld-Umzug: Antrag auf Kostenübernahme beim Jobcenter",
            "description": "Schritt-für-Schritt Anleitung für Bürgergeld-Empfänger zur Beantragung der Umzugskostenübernahme",
            "image": "https://yla-umzug.de/images/hartz-iv-umzug.jpg",
            "totalTime": "P4W",
            "estimatedCost": {
              "@type": "MonetaryAmount",
              "currency": "EUR",
              "value": "0"
            },
            "step": antragSchritte.map(schritt => ({
              "@type": "HowToStep",
              "name": schritt.title,
              "text": schritt.description
            }))
          })}
        </script>
      </Helmet>

      {/* Hero Section */}
      <div className="text-center mb-16">
        <h1 className="text-4xl md:text-5xl font-extrabold text-white mb-6">
          Bürgergeld-Umzug: So übernimmt das Jobcenter Ihre Kosten
        </h1>
        <p className="text-xl text-violet-200 max-w-4xl mx-auto mb-8">
          Ein Umzug kann teuer werden – besonders für <strong>Bürgergeld-Empfänger</strong>. 
          Mit einem rechtzeitigen Antrag auf Umzugskostenübernahme können Sie diese Kosten jedoch komplett erstattet bekommen.
        </p>
        <div className="flex flex-wrap justify-center gap-2 mb-8">
          {regions.map((region) => (
            <span key={region} className="bg-violet-600/20 text-violet-300 px-3 py-1 rounded-full text-sm flex items-center">
              <MapPin size={14} className="mr-1" />
              {region}
            </span>
          ))}
        </div>
      </div>

      {/* Anerkannte Umzugsgründe */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Anerkannte Umzugsgründe für Bürgergeld-Empfänger</h2>
        <p className="text-lg text-gray-300 text-center mb-8">
          Das Jobcenter übernimmt Ihre Umzugskosten, wenn einer der folgenden Gründe vorliegt:
        </p>
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {anerkannteGruende.map((grund, index) => (
            <motion.div
              key={grund.title}
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.3 }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className="bg-gray-800/60 p-6 rounded-xl"
            >
              <div className="flex items-start space-x-3">
                <CheckCircle size={20} className="text-green-400 mt-1 flex-shrink-0" />
                <div>
                  <h3 className="text-lg font-bold text-white mb-2">{grund.title}</h3>
                  <p className="text-gray-300 text-sm">{grund.description}</p>
                </div>
              </div>
            </motion.div>
          ))}
        </div>
      </div>

      {/* Nicht anerkannte Gründe */}
      <motion.div
        initial={{ opacity: 0, y: 50 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, amount: 0.3 }}
        transition={{ duration: 0.5 }}
        className="bg-red-900/20 border border-red-500/30 p-8 rounded-xl mb-16"
      >
        <h2 className="text-2xl font-bold text-white mb-6">❌ Gründe, die NICHT anerkannt werden</h2>
        <p className="text-gray-300 mb-4">Das Jobcenter lehnt häufig folgende Umzugsanträge ab:</p>
        <ul className="space-y-2 text-gray-300">
          <li>• Familienzusammenführung (z.B. Auszug aus dem Elternhaus vor dem 25. Lebensjahr)</li>
          <li>• Aussicht auf bessere Jobchancen ohne konkretes Angebot</li>
          <li>• Kleine Wohnmängel, die Sie selbst beheben könnten</li>
        </ul>
      </motion.div>

      {/* Schritt-für-Schritt Antrag */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Schritt-für-Schritt: Antrag auf Umzugskostenübernahme</h2>
        <div className="space-y-8">
          {antragSchritte.map((schritt, index) => (
            <motion.div
              key={schritt.number}
              initial={{ opacity: 0, x: -50 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true, amount: 0.3 }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className="bg-gray-800/60 p-8 rounded-xl shadow-lg"
            >
              <div className="flex items-start space-x-6">
                <div className="flex-shrink-0 bg-violet-600 text-white w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold">
                  {schritt.number}
                </div>
                <div className="flex-1">
                  <h3 className="text-2xl font-bold text-white mb-2">{schritt.title}</h3>
                  <p className="text-gray-300">{schritt.description}</p>
                </div>
              </div>
            </motion.div>
          ))}
        </div>
      </div>

      {/* Checkliste */}
      <motion.div
        initial={{ opacity: 0, y: 50 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, amount: 0.3 }}
        transition={{ duration: 0.5 }}
        className="bg-gradient-to-r from-violet-600/20 to-purple-600/20 p-8 rounded-xl mb-16"
      >
        <h2 className="text-3xl font-bold text-white mb-6">📋 Checkliste für Ihre Unterlagen</h2>
        <div className="grid md:grid-cols-2 gap-4">
          {checkliste.map((item, index) => (
            <div key={index} className="flex items-center space-x-3">
              <div className="w-6 h-6 border-2 border-violet-400 rounded flex items-center justify-center">
                <CheckCircle size={16} className="text-violet-400" />
              </div>
              <span className="text-gray-300">{item}</span>
            </div>
          ))}
        </div>
      </motion.div>

      {/* FAQ Section */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Häufige Fragen zum Bürgergeld-Umzug</h2>
        <div className="space-y-6">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Kann ich Umzugskartons über das Jobcenter abrechnen?</h3>
            <p className="text-gray-300">Ja, Kosten für Umzugskartons, Packmaterial und Transport können Sie als Teil der Umzugskostenübernahme geltend machen.</p>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Was tun, wenn das Jobcenter ablehnt?</h3>
            <p className="text-gray-300">Fordern Sie schriftlich die Rechtsgrundlage der Entscheidung an und legen Sie innerhalb eines Monats Widerspruch ein.</p>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Übernimmt das Jobcenter auch die Renovierungskosten?</h3>
            <p className="text-gray-300">Ja, sogenannte „Schönheitsreparaturen" können erstattet werden, wenn sie für den Umzug notwendig sind und Sie dazu verpflichtet sind.</p>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Brauche ich einen Kostenvoranschlag für ein Umzugsunternehmen?</h3>
            <p className="text-gray-300">Unbedingt – und zwar drei verschiedene, um dem „erforderlichen Kostensparprinzip" zu genügen.</p>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-white mb-2">Muss ich selbst umziehen, wenn das Jobcenter die Wohnung zu groß findet?</h3>
            <p className="text-gray-300">Wenn Ihre Unterkunft über den angemessenen Richtwert hinaus geht, kann das Jobcenter einen Umzug verlangen und die Kosten übernehmen.</p>
          </div>
        </div>
      </div>

      {/* WhatsApp CTA */}
      <motion.div
        initial={{ opacity: 0, y: 50 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, amount: 0.3 }}
        transition={{ duration: 0.5 }}
        className="bg-green-600/20 border border-green-500/30 p-8 rounded-xl mb-16"
      >
        <div className="text-center">
          <MessageCircle size={48} className="text-green-400 mx-auto mb-4" />
          <h2 className="text-2xl font-bold text-white mb-4">Schnelle Hilfe per WhatsApp</h2>
          <p className="text-gray-300 mb-6">
            Benötigen Sie sofort einen Kostenvoranschlag für das Jobcenter? 
            Kontaktieren Sie uns direkt per WhatsApp!
          </p>
          <a 
            href={whatsappUrl}
            target="_blank"
            rel="noopener noreferrer"
            className="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-full text-lg transition-transform transform hover:scale-105"
          >
            <MessageCircle size={20} className="mr-2" />
            WhatsApp: +49 1575 0693353
          </a>
        </div>
      </motion.div>

      {/* Related Articles */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold text-white mb-8 text-center">Weitere hilfreiche Ratgeber</h2>
        <div className="grid md:grid-cols-3 gap-6">
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Umzug Checkliste</h3>
            <p className="text-gray-300 mb-4">Komplette 8-Wochen-Checkliste für Ihren stressfreien Umzug.</p>
            <NavLink to="/ratgeber-umzug-checkliste" className="text-violet-400 hover:text-violet-300 flex items-center">
              Umzug Ratgeber lesen <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Entrümpelung vor Umzug</h3>
            <p className="text-gray-300 mb-4">Entrümpeln Sie vor dem Umzug und sparen Sie Kosten.</p>
            <NavLink to="/ratgeber-entruempelung-5-schritte" className="text-violet-400 hover:text-violet-300 flex items-center">
              Entrümpelung Ratgeber lesen <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
          <div className="bg-gray-800/60 p-6 rounded-xl">
            <h3 className="text-xl font-bold text-white mb-3">Endreinigung</h3>
            <p className="text-gray-300 mb-4">Nach dem Umzug: So bekommen Sie Ihre Kaution zurück.</p>
            <NavLink to="/ratgeber-hausreinigung-endreinigung" className="text-violet-400 hover:text-violet-300 flex items-center">
              Reinigung Ratgeber lesen <ArrowRight size={16} className="ml-1" />
            </NavLink>
          </div>
        </div>
      </div>

      {/* CTA Section */}
      <div className="text-center bg-gradient-to-r from-violet-600/20 to-purple-600/20 p-8 rounded-xl">
        <h2 className="text-3xl font-bold text-white mb-4">Kostenvoranschlag für das Jobcenter anfordern</h2>
        <p className="text-lg text-violet-200 max-w-2xl mx-auto mb-8">
          Wir erstellen Ihnen einen rechtskonformen Kostenvoranschlag für das Jobcenter – 
          inklusive Transport, Umzugskartons und Möbelmontage.
        </p>
        <div className="flex flex-col sm:flex-row gap-4 justify-center">
          <Button asChild size="lg" className="bg-violet-600 hover:bg-violet-700 text-white font-bold py-4 px-8 rounded-full text-lg transition-transform transform hover:scale-105">
            <NavLink to="/kontakt">Kostenloses Angebot anfordern</NavLink>
          </Button>
          <a 
            href={whatsappUrl}
            target="_blank"
            rel="noopener noreferrer"
            className="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-full text-lg transition-transform transform hover:scale-105"
          >
            <MessageCircle size={20} className="mr-2" />
            WhatsApp Kontakt
          </a>
        </div>
      </div>
    </motion.div>
  );
};

export default HartzIVUmzugRatgeberPage;