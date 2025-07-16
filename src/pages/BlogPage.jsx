import React from 'react';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { Calendar, Tag, ArrowRight } from 'lucide-react';

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
    tags: ["Anleitung", "Tipps"],
    content: "Eine Entrümpelung kann überwältigend sein. Mit unserer 5-Schritte-Anleitung behalten Sie den Überblick: 1. Planen & Ziele setzen. 2. Sortieren (Behalten, Verkaufen, Spenden, Entsorgen). 3. Helfer organisieren. 4. Den Profi für die Entsorgung rufen. 5. Neu einrichten und genießen. So wird Ihr Projekt in Saarbrücken zum Erfolg!"
  },
  {
    title: "Messi-Wohnung: Wie man als Angehöriger helfen kann",
    date: "10. Juli 2025",
    tags: ["Messi-Syndrom", "Hilfe"],
    content: "Der Umgang mit einer Messi-Wohnung erfordert viel Einfühlungsvermögen. Wichtig ist, ohne Vorwürfe zu handeln. Bieten Sie Ihre Hilfe an, aber überfordern Sie die betroffene Person nicht. Professionelle Hilfe, wie die von YLA Umzug in der Region Trier, kann den Prozess diskret und respektvoll begleiten und ist oft der entscheidende Schritt."
  },
  {
    title: "Wertanrechnung bei Haushaltsauflösung: So sparen Sie Geld",
    date: "05. Juli 2025",
    tags: ["Kosten", "Haushaltsauflösung"],
    content: "Wussten Sie, dass Sie bei einer Haushaltsauflösung Geld sparen können? Gut erhaltene Möbel, Antiquitäten oder Elektrogeräte können wir oft auf die Kosten anrechnen. Sprechen Sie uns bei der Besichtigung in Kaiserslautern oder Umgebung darauf an. Wir prüfen den Wert Ihrer Gegenstände und erstellen Ihnen ein faires Angebot."
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
        <title>Ratgeber: Tipps zur Entrümpelung & Haushaltsauflösung | YLA Umzug</title>
        <meta name="description" content="Lesen Sie unsere Experten-Tipps zur Entrümpelung, zum Umgang mit Messi-Wohnungen und wie Sie bei einer Haushaltsauflösung im Saarland und RLP Geld sparen können." />
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
            <div className="flex items-center space-x-4 text-sm text-gray-400 mb-4">
              <div className="flex items-center space-x-2">
                <Calendar size={16} />
                <span>{post.date}</span>
              </div>
              <div className="flex items-center space-x-2">
                <Tag size={16} />
                <span>{post.tags.join(', ')}</span>
              </div>
            </div>
            <p className="text-gray-300 mb-6">{post.content}</p>
            <button className="font-semibold text-violet-400 hover:text-violet-300 flex items-center space-x-2">
              <span>Weiterlesen</span>
              <ArrowRight size={16} />
            </button>
          </motion.div>
        ))}
      </div>
    </motion.div>
  );
};

export default BlogPage;