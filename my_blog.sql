-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 28 juin 2024 à 18:07
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `my_blog`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) UNSIGNED NOT NULL,
  `content` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `user_id` int(11) UNSIGNED NOT NULL,
  `post_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `content`, `createdAt`, `updatedAt`, `status`, `user_id`, `post_id`) VALUES
(1, 'super article sylvie tu es au top ', '2024-03-15 15:31:45', '2024-06-23 09:38:33', 'approved', 87, 2),
(9, 'trop top ', '2024-03-31 16:32:47', '2024-06-23 09:25:58', 'approved', 87, 2),
(14, 'lololololo', '2024-03-31 17:27:21', '2024-06-06 13:15:41', 'approved', 87, 1),
(37, 'tttttttttttttttttttttttttt', '2024-03-31 18:45:14', '2024-06-09 16:58:25', 'rejected', 87, 1),
(38, 'pppppppppppp', '2024-03-31 18:46:41', '2024-06-23 14:32:28', 'approved', 87, 43),
(47, 'rererererfefrjugdvfckjhbf!cj', '2024-03-31 19:03:02', '2024-06-07 14:15:10', 'approved', 87, 1),
(53, 'et allez encore un !', '2024-03-31 19:20:46', '2024-06-06 13:15:41', 'approved', 87, 1),
(55, 'c\'est trop de la balle ces petits !!!', '2024-04-05 17:46:55', '2024-06-06 13:15:41', 'approved', 87, 19),
(57, 'popo', '2024-04-13 12:03:06', '2024-06-06 13:15:41', 'rejected', 87, 19),
(58, 'trop beaux mes n\'amoureux !!!!!!!', '2024-04-13 17:09:05', '2024-06-06 13:15:41', 'approved', 87, 19),
(59, 'amoureux de moi ça !!!! ', '2024-04-13 17:09:30', '2024-06-06 13:15:41', 'approved', 87, 19),
(60, 'comme elle est forte sylvie la dev quand meme !!!! whaouuuuuuu', '2024-04-19 15:24:34', '2024-06-06 13:15:41', 'approved', 87, 19),
(64, 'aaaaaaaaaaaaaaaaaaaaaaaaaaa', '2024-04-28 16:07:07', '2024-06-08 10:14:30', 'rejected', 87, 43),
(65, 'bbbbbbbbbbbbbbbb', '2024-05-02 10:48:28', '2024-06-06 13:15:41', 'rejected', 87, 43),
(66, 'sssssssssssssssssss', '2024-05-02 12:34:32', '2024-06-08 12:21:24', 'approved', 87, 40),
(67, 'ssssssssssss', '2024-05-02 12:34:48', '2024-06-06 13:15:41', 'approved', 87, 40),
(68, 'olalaa', '2024-05-07 08:04:42', '2024-06-06 13:15:41', 'rejected', 87, 43),
(69, 'eeeeeeeeeee', '2024-05-07 09:26:59', '2024-06-06 13:15:41', 'rejected', 87, 43),
(70, 'popppppppppppppp\r\n', '2024-05-07 09:44:16', '2024-06-23 09:38:25', 'approved', 87, 43),
(71, 'rrrrrrrrrrrrrrrrrrrrrrrr', '2024-05-07 09:50:14', '2024-06-23 09:56:21', 'approved', 87, 43),
(72, 'rrrrrrrrrrrr', '2024-05-07 09:50:23', '2024-06-08 09:57:59', 'rejected', 87, 43),
(73, 'aaaaaaaaaaaaaa', '2024-05-07 09:52:11', '2024-06-23 09:56:27', 'approved', 87, 43),
(74, 'popopopo', '2024-05-07 12:05:31', '2024-06-08 10:05:09', 'approved', 87, 43),
(75, 'aaaaaaaaaa aaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaaaaa aaaaaa aaaaaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa aaaaaaa aaaaaaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaa', '2024-05-07 13:57:25', '2024-06-06 13:15:41', 'approved', 87, 1),
(77, 'new comment ', '2024-05-08 11:48:59', '2024-06-28 15:29:38', 'approved', 87, 1),
(83, 'momo', '2024-05-17 06:05:18', '2024-06-28 15:29:56', 'approved', 87, 1),
(84, 'pour l\'utilisateur anonyme', '2024-05-17 06:50:49', '2024-06-23 14:32:32', 'rejected', 87, 1),
(90, 'wwwwwwwwwwwwwww', '2024-05-17 09:11:17', '2024-06-14 10:10:49', 'rejected', 87, 2),
(91, 'ttttttttttttttttttttttttttttttttt', '2024-05-17 09:19:18', '2024-06-14 10:10:49', 'approved', 87, 1),
(97, 'zzzzzzzzzzzzzzzzz', '2024-05-19 09:25:10', '2024-06-23 09:37:07', 'approved', 87, 19),
(100, 'dddddddddddddddddddddd', '2024-05-19 14:24:32', '2024-06-14 10:10:49', 'rejected', 87, 40),
(103, 'dfhsgh', '2024-05-20 15:19:37', '2024-06-23 14:52:33', 'rejected', 87, 40),
(104, 'yufutdkuyf iug', '2024-05-26 12:48:41', '2024-06-28 15:29:26', 'approved', 87, 2),
(105, 'tdffnhrfjujjjvyyyyyyyyyyyyyyyy', '2024-05-26 12:49:23', '2024-06-28 15:29:38', 'approved', 87, 2),
(106, 'aaaaaaaaaaaaaaaaaa', '2024-05-26 12:53:25', '2024-06-28 15:29:56', 'approved', 87, 2),
(107, 'eeeeeeeeeeeeeeeee', '2024-05-26 12:54:30', '2024-06-28 15:59:10', 'approved', 123, 2),
(108, 'rrrrrrrrrrrrrrrrrr', '2024-05-26 13:01:26', '2024-06-26 19:28:59', 'approved', 87, 2),
(109, 'tout ca pour ca ?!!! ', '2024-05-26 13:09:39', '2024-06-14 09:47:19', 'approved', 87, 2),
(110, 'qdf<qd', '2024-05-26 16:10:41', '2024-06-28 15:29:38', 'approved', 87, 43),
(111, 'ikgfukiyflofu', '2024-05-26 16:15:31', '2024-06-28 15:29:38', 'pending', 87, 43),
(112, 'ikgfukiyflofu', '2024-05-26 16:17:42', '2024-06-28 15:29:38', 'pending', 87, 43),
(113, 'ergwszrfqegwszg', '2024-05-26 16:19:32', '2024-06-28 15:29:38', 'pending', 87, 43),
(114, 'ougpçouètgmpoiuhpi gb', '2024-05-30 17:55:41', '2024-06-23 14:52:54', 'approved', 87, 1),
(115, 'ftyjcdrfyjicfkc fy', '2024-05-31 15:05:06', '2024-06-23 14:52:24', 'rejected', 87, 1),
(116, 'qztzr(hjswdetj  z\'y\'y \'éqayt qé\'ay \' é\'ay éa\'', '2024-05-31 15:38:15', '2024-06-24 18:54:00', 'rejected', 87, 1),
(117, 'zerfoiqh<qrgqhmigiqngùa', '2024-05-31 15:38:39', '2024-06-26 19:27:46', 'approved', 87, 1),
(118, 'rqetg', '2024-05-31 15:40:36', '2024-06-23 14:41:42', 'rejected', 87, 1),
(119, 'rlkgfqhzrightqzw', '2024-06-01 12:11:36', '2024-06-28 15:29:38', 'approved', 87, 1),
(120, 'qqqqqqqqqq', '2024-06-01 12:11:49', '2024-06-28 15:29:38', 'approved', 87, 1),
(121, 'tututu', '2024-06-06 14:03:26', '2024-06-06 14:04:20', 'rejected', 106, 1),
(127, 'dgtggggggggggggg', '2024-06-08 14:29:24', '2024-06-26 19:27:51', 'pending', 106, 1),
(132, 'POOOOOOOOOOOOOOOOOOO', '2024-06-14 10:13:24', '2024-06-23 14:32:37', 'rejected', 106, 1),
(135, 'Étymologie Le terme blog est issu de l\'aphérèse d\'un mot composé, né de la contraction de Web log ; en anglais, log peut signifier registre ou journal. Ce terme est employé pour la première fois par Jorn Barger, en 19979. Un blogueur ou une blogueuse10 (e', '2024-06-23 14:36:49', '2024-06-23 15:19:21', 'rejected', 106, 2),
(136, 'test pour suppression', '2024-06-23 14:56:20', '2024-06-23 14:58:15', 'rejected', 106, 2),
(137, 'test pour validation', '2024-06-23 14:56:30', '2024-06-23 14:58:07', 'approved', 106, 2),
(138, 'encore uiun test', '2024-06-23 14:59:30', '2024-06-26 19:28:09', 'rejected', 106, 2),
(140, 'ddddddddddddddddd', '2024-06-26 18:38:38', '2024-06-26 19:28:06', 'approved', 106, 2);

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL,
  `chapo` varchar(255) NOT NULL,
  `author` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(50) DEFAULT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `published` tinyint(1) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`id`, `title`, `chapo`, `author`, `content`, `image`, `user_id`, `published`, `createdAt`, `updatedAt`) VALUES
(1, 'mon super blog', 'Je te raconte ici mon premier blog', 'bibi', 'Un blog, ou blogue, est un type de site web ou une partie d&#039;un site web utilisé pour la publication périodique et régulière d&#039;articles personnels, généralement succincts, rendant compte d&#039;une actualité autour d&#039;une thématique particulière. À la manière d&#039;un journal intime, ces articles appelés billets publiés par son/ses propriétaire(s) ou son/ses webmaster(s), sont généralement datés, signés et présentés dans un ordre rétrochronologique, c&#039;est-à-dire du plus récent au plus ancien. Ils permettent à l&#039;auteur, appelé blogueur, d’exprimer une opinion et sont la plupart du temps ouverts aux commentaires des lecteurs.\r\n\r\nAu printemps 2011, on dénombrait au moins 156 millions de blogs et pas moins d&#039;un million de nouveaux articles de blog publiés chaque jour. En 2012, on recensait 31 millions de blogs aux États-Unis, tandis qu&#039;à l&#039;échelle mondiale, on estime à trois millions le nombre de blogs qui naissent chaque mois. Toutefois, le nombre de blogs inactifs demeure élevé. Rares sont en effet ceux qui affichent une grande longévité et la majorité d&#039;entre eux sont abandonnés par les auteurs.\r\n\r\nUn blogueur a aujourd&#039;hui le loisir de mélanger textes, hypertextes et éléments multimédias (image, son, vidéo, applet) dans ses billets ; il peut aussi répondre aux questions des éventuels lecteurs-souscripteurs, car chaque visiteur d&#039;un blog peut laisser des commentaires sur le blog lui-même, ou bien contacter le blogueur par courriel.', '', 87, 0, '2024-02-22 09:42:26', '2024-06-28 16:06:36'),
(2, 'symfony', 'parlons un peu de cet orm', 'bibi', 'Étymologie\r\nLe terme blog est issu de l&#039;aphérèse d&#039;un mot composé, né de la contraction de Web log ; en anglais, log peut signifier registre ou journal. Ce terme est employé pour la première fois par Jorn Barger, en 19979.\r\n\r\nUn blogueur ou une blogueuse10 (en anglais : blogger) est l&#039;individu qui a l&#039;habitude de bloguer11 : il écrit et publie les billets, sans entrer dans la composition de tous les commentaires qui y sont associés. La blogosphère est l&#039;ensemble des blogs ou la communauté des blogueurs12. Parfois, par métonymie, on désigne l&#039;ensemble des blogs d&#039;une communauté précise : la « blogosphère homosexuelle », la « blogosphère des standards Web », etc', '', 87, 1, '2024-02-22 09:47:50', '2024-06-28 16:05:01'),
(19, 'php', 'des questions sur le langage php ? ', 'toujours moi ', 'PHP (officiellement, ce sigle est un acronyme récursif pour PHP Hypertext Preprocessor) est un langage de scripts généraliste et Open Source, spécialement conçu pour le développement d\'applications web. Il peut être intégré facilement au HTML.\r\nAu lieu d\'utiliser des tonnes de commandes afin d\'afficher du HTML (comme en C ou en Perl), les pages PHP contiennent des fragments HTML dont du code qui fait \"quelque chose\" (dans ce cas, il va afficher \"Bonjour, je suis un script PHP !\"). Le code PHP est inclus entre une balise de début <?php et une balise de fin ?> qui permettent au serveur web de passer en mode PHP.\r\n\r\nCe qui distingue PHP des langages de script comme le Javascript, est que le code est exécuté sur le serveur, générant ainsi le HTML, qui sera ensuite envoyé au client. Le client ne reçoit que le résultat du script, sans aucun moyen d\'avoir accès au code qui a produit ce résultat. Vous pouvez configurer votre serveur web afin qu\'il analyse tous vos fichiers HTML comme des fichiers PHP. Ainsi, il n\'y a aucun moyen de distinguer les pages qui sont produites dynamiquement des pages statiques.\r\n\r\nLe grand avantage de PHP est qu\'il est extrêmement simple pour les néophytes, mais offre des fonctionnalités avancées pour les experts. Ne craignez pas de lire la longue liste de fonctionnalités PHP. Vous pouvez vous plonger dans le code, et en quelques instants, écrire des scripts simples.\r\n\r\nBien que le développement de PHP soit orienté vers la programmation pour les sites web, vous pouvez en faire bien d\'autres usages. Lisez donc la section Que peut faire PHP ? ou bien le tutoriel d\'introduction si vous êtes uniquement intéressé dans la programmation web.', '', 87, 1, '2024-04-05 17:20:00', '2024-06-28 16:05:10'),
(40, 'wordpress', 'qu\'est ce que wordpress ?', 'moi ', 'WordPress est le moyen le plus simple et le plus populaire de créer votre propre site Web ou blog. En fait, WordPress gère plus de 42.7 % de tous les sites Web sur Internet. Oui – plus d’un site Web sur quatre que vous visitez est probablement propulsé par WordPress.\r\n\r\nSur un plan un peu plus technique, WordPress est un système de gestion de contenu open-source sous licence GPLv2, ce qui signifie que tout le monde peut utiliser ou modifier le logiciel WordPress gratuitement. Un système de gestion de contenu est essentiellement un outil qui facilite la gestion d’aspects importants de votre site Web – comme le contenu – sans avoir besoin de connaître quoi que ce soit en programmation.\r\n\r\nLe résultat final est que WordPress rend la construction d’un site Web accessible à tous – même aux personnes qui ne sont pas développeurs.', '', 87, 1, '2024-04-27 14:28:54', '2024-06-28 16:05:18'),
(43, 'Api platform', 'utilisons l\'api platform ', 'bibi', 'API Platform est un framework écrit en PHP et basé sur Symfony qui permet de mettre en place simplement et rapidement une API Rest et GraphQL. Il se base pour cela sur le système de configuration qui va permettre de transformer les modèles de notre application en ressources d\'API avec les points d\'entrée correspondants. Il va aussi automatiquement générer une documentation OpenAPI qui permet de décrire le fonctionnement de l\'API aux personnes qui souhaiteraient l\'utiliser.\r\n\r\nPuisqu\'il utilise le framework Symfony il est aussi très facile d\'étendre le fonctionnement de l\'outil en créant de nouveaux services ou en utilisant le système d\'événements intégré.', '', 87, 1, '2024-04-27 15:00:13', '2024-06-28 16:05:28');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(25) NOT NULL,
  `lastName` varchar(25) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `pseudo` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('admin','subscriber') NOT NULL,
  `resetToken` varchar(255) DEFAULT NULL,
  `first_login_done` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `lastName`, `image`, `pseudo`, `email`, `password`, `createdAt`, `updatedAt`, `role`, `resetToken`, `first_login_done`) VALUES
(87, 'Utilisateur', 'Anonyme', '', 'Anonymous', 'sylvie.pepete@live.fr', '$2y$10$EKzP7cJ7/3ziCSNmTX/aQO0JmEemVpyAeQ7bqSzS2EaFVnM8fqHk2', '2024-05-17 06:35:55', '2024-06-28 16:03:57', 'admin', '', 1),
(106, 'bibi', 'Bibi', '', 'Ladevquitue', 'peuzin.sylvie.sp@gmail.com', '$2y$10$EF.uXHsLKCwin0GgzNNxx.3DTtb2vSO0Mm.VnoR24MZwDQQDZ3heW', '2024-06-01 10:32:49', '2024-06-28 16:04:12', 'admin', NULL, 1),
(123, 'martin', 'dubois', NULL, 'martin\'s', 'martin@martin.fr', '$2y$10$YBJlG7vE2RQ1CIrtOl74JuJZbHbXi6cKd1YA6DDRcLuvsDQQCJ5Yu', '2024-06-06 15:24:24', '2024-06-28 15:58:11', 'subscriber', NULL, 0),
(155, 'jonh', 'doe', NULL, 'jojo', 'ffff@rrr.fr', '$2y$10$VmSr0a.pK/tWuT/D1N6LmeMe5jAWMpT8DbLG061ZUR9uHWaGPPX9.', '2024-06-26 18:02:28', '2024-06-28 15:58:51', 'admin', '', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_post_id` (`post_id`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD UNIQUE KEY `unique_pseudo` (`pseudo`) USING BTREE;

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_post_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
