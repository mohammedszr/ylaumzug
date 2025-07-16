import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { Calendar, Tag, ArrowRight, MapPin } from 'lucide-react';
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

const blogPosts = [
  {
    title: "Entrümpelung in 5 Schritten: So geht's richtig!",
    date: "15. Juli 2025",
    tags: ["Anleitung", "Tipps", "Saarbrücken"],
    content: "Eine Entrümpelung kann überwältigend sein. Mit unserer 5-Schritte-Anleitung behalten Sie den Überblick: 1. Planen & Ziele setzen. 2. Sortieren (Behalten, Verkaufen, Spenden, Entsorgen). 3. Helfer organisieren. 4. Den Profi für die Entsorgung rufen. 5. Neu einrichten und genießen. So wird Ihr Projekt in Saarbrücken zum Erfolg! Erfahren Sie mehr über Kellerentrümpelung, Sperrmüll entsorgen und Wertanrechnung bei Haushaltsauflösung.",
    link: "/ratgeber-entruempelung-5-schritte",
    region: "Saarbrücken, Trier, Kaiserslautern"
  },
  {
    title: "Umzug Checkliste: Stressfrei umziehen in 8 Wochen",
    date: "12. Juli 2025",
    tags: ["Umzug", "Checkliste", "Kaiserslautern"],
    content: "Planen Sie einen Umzug in Kaiserslautern, Trier oder Saarbrücken? Unsere 8-Wochen-Checkliste hilft Ihnen dabei, nichts zu vergessen: Von der Umzugsfirma-Auswahl über Möbellift mieten bis zur Endreinigung. Sparen Sie Geld mit unseren Profi-Tipps für günstigen Umzug und vermeiden Sie versteckte Kosten. Umzugscheckliste jetzt herunterladen!",
    link: "/ratgeber-umzug-checkliste",
    region: "Kaiserslautern, Trier, Saarbrücken"
  },
  {
    title: "Messi-Wohnung: Wie man als Angehöriger helfen kann",
    date: "10. Juli 2025",
    tags: ["Messi-Syndrom", "Hilfe", "Trier"],
    content: "Der Umgang mit einer Messi-Wohnung erfordert viel Einfühlungsvermögen. Wichtig ist, ohne Vorwürfe zu handeln. Bieten Sie Ihre Hilfe an, aber überfordern Sie die betroffene Person nicht. Professionelle Hilfe, wie die von YLA Umzug in der Region Trier, kann den Prozess diskret und respektvoll begleiten und ist oft der entscheidende Schritt. Messi-Syndrom aufräumen erfordert Geduld und Expertise.",
    link: "/ratgeber-entruempelung-5-schritte#messi-wohnung",
    region: "Trier, Saarbrücken"
  },
  {
    title: "Hausreinigung & Endreinigung: Kaution zurückbekommen",
    date: "08. Juli 2025",
    tags: ["Hausreinigung", "Endreinigung", "Rheinland-Pfalz"],
    content: "Endreinigung nach Umzug in Rheinland-Pfalz und Saarland: So bekommen Sie Ihre Kaution zurück! Unser Ratgeber zeigt, was zur professionellen Endreinigung gehört - von Fenster putzen bis Grundreinigung. Sparen Sie Zeit und Stress mit unserem Reinigungsservice. Transparente Preise für 1-Zimmer bis Einfamilienhaus.",
    link: "/ratgeber-hausreinigung-endreinigung",
    region: "Rheinland-Pfalz, Saarland"
  },
  {
    title: "Bürgergeld-Umzug: So übernimmt das Jobcenter Ihre Kosten",
    date: "16. Juli 2025",
    tags: ["Bürgergeld", "Jobcenter", "ALG II"],
    content: "Bürgergeld-Empfänger können Umzugskosten vom Jobcenter erstattet bekommen! Unser Ratgeber zeigt alle anerkannten Umzugsgründe, den korrekten Antrag und wie Sie drei Kostenvoranschläge richtig einreichen. Von Familienzuwachs bis Arbeitsaufnahme - erfahren Sie, wann das Jobcenter zahlt und welche Unterlagen Sie benötigen. Inklusive Checkliste und FAQ für Bürgergeld-Empfänger.",
    link: "/ratgeber-hartz-iv-umzug-jobcenter",
    region: "Rheinland-Pfalz, Saarland"
  },
  {
    title: "Wertanrechnung bei Haushaltsauflösung: So sparen Sie Geld",
    date: "05. Juli 2025",
    tags: ["Kosten", "Haushaltsauflösung", "Kaiserslautern"],
    content: "Wussten Sie, dass Sie bei einer Haushaltsauflösung Geld sparen können? Gut erhaltene Möbel, Antiquitäten oder Elektrogeräte können wir oft auf die Kosten anrechnen. Sprechen Sie uns bei der Besichtigung in Kaiserslautern oder Umgebung darauf an. Wir prüfen den Wert Ihrer Gegenstände und erstellen Ihnen ein faires Angebot. Bis zu 50% Kostenersparnis möglich!",
    link: "/ratgeber-entruempelung-5-schritte#wertanrechnung",
    region: "Kaiserslautern, Trier"
  }
];

const BlogPage = () => {
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
        <title>Entrümpelung, Umzug & Hausreinigung Ratgeber - Saarbrücken, Trier, Kaiserslautern | YLA</title>
        <meta name="description" content="Experten-Ratgeber für Entrümpelung, Umzug & Hausreinigung in Saarbrücken, Trier & Kaiserslautern. 5-Schritte Anleitungen, Checklisten & Geld sparen Tipps!" />
        <meta name="keywords" content="Entrümpelung Saarbrücken, Umzug Trier, Hausreinigung Kaiserslautern, Messi-Wohnung Hilfe, Endreinigung Rheinland-Pfalz, Wertanrechnung Haushaltsauflösung" />
        <link rel="canonical" href="/ratgeber" />
      </Helmet>

      <div className="text-center mb-16">
        <h1 className="text-4xl md:text-5xl font-extrabold text-white mb-4">Unser Ratgeber</h1>
        <p className="text-lg text-violet-200 max-w-3xl mx-auto">
          Hier finden Sie nützliche Anleitungen, Tipps und Tricks rund um die Themen Entrümpelung, Entsorgung und Haushaltsauflösung.
        </p>
      </div>

      <div className="space-y-12">
        {blogPosts.map((post, index) => (
          <motion.div
            key={post.title}
            initial={{ opacity: 0, y: 50 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.3 }}
            transition={{ duration: 0.5, delay: index * 0.1 }}
            className="bg-gray-800/60 p-8 rounded-xl shadow-lg hover:bg-gray-700/80 transition-colors duration-300"
          >
            <h2 className="text-2xl md:text-3xl font-bold text-white mb-3">{post.title}</h2>
            <div className="flex flex-wrap items-center gap-4 text-sm text-gray-400 mb-4">
              <div className="flex items-center space-x-2">
                <Calendar size={16} />
                <span>{post.date}</span>
              </div>
              <div className="flex items-center space-x-2">
                <Tag size={16} />
                <span>{post.tags.join(', ')}</span>
              </div>
              <div className="flex items-center space-x-2">
                <MapPin size={16} />
                <span>{post.region}</span>
              </div>
            </div>
            <p className="text-gray-300 mb-6">{post.content}</p>
            <NavLink
              to={post.link}
              className="font-semibold text-violet-400 hover:text-violet-300 flex items-center space-x-2 transition-colors duration-200"
            >
              <span>Weiterlesen</span>
              <ArrowRight size={16} />
            </NavLink>
          </motion.div>
        ))}
      </div>
    </motion.div>
  );
};

export default BlogPage;