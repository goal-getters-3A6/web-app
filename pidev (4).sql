-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 16 mai 2024 à 16:22
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pidev`
--

-- --------------------------------------------------------

--
-- Structure de la table `abonnement`
--

CREATE TABLE `abonnement` (
  `idAb` int(11) NOT NULL,
  `montantAb` float NOT NULL,
  `dateExpirationAb` date NOT NULL,
  `codePromoAb` varchar(255) DEFAULT NULL,
  `typeAb` varchar(255) NOT NULL,
  `idU` int(11) DEFAULT NULL,
  `dureeAb` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `abonnement`
--

INSERT INTO `abonnement` (`idAb`, `montantAb`, `dateExpirationAb`, `codePromoAb`, `typeAb`, `idU`, `dureeAb`) VALUES
(7, 100, '2024-06-08', 'GoFit30', 'Familiale', 28, NULL),
(8, 50, '2024-06-07', 'GoFit30', 'Ordinaire', 32, NULL),
(9, 50, '2024-07-10', 'GoFit10', 'Ordinaire', 28, NULL),
(10, 50, '2024-06-30', NULL, 'Ordinaire', 28, NULL),
(11, 100, '2024-07-31', NULL, 'Familiale', 28, NULL),
(13, 150, '2024-06-22', NULL, 'Premium', 32, NULL),
(14, 150, '2024-06-30', NULL, 'Premium', 28, NULL),
(15, 50, '2024-06-07', 'GoFit30', 'Ordinaire', 34, NULL),
(16, 80, '2024-06-30', '', 'Ordinaire', 19, NULL),
(17, 80, '2024-05-15', 'GoFit20', 'Ordinaire', 19, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `avisequipement`
--

CREATE TABLE `avisequipement` (
  `idAEq` int(11) NOT NULL,
  `commAEq` varchar(255) DEFAULT NULL,
  `like` tinyint(1) DEFAULT NULL,
  `dislike` tinyint(1) DEFAULT NULL,
  `idEq` int(11) NOT NULL,
  `idUs` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avisequipement`
--

INSERT INTO `avisequipement` (`idAEq`, `commAEq`, `like`, `dislike`, `idEq`, `idUs`) VALUES
(294, 'great', NULL, NULL, 46, 28),
(312, 'good one', NULL, NULL, 46, 32),
(313, 'neutre', NULL, NULL, 46, 32),
(314, 'not bad', NULL, NULL, 46, 32),
(315, 'good', NULL, NULL, 47, 32),
(316, 'good', NULL, NULL, 46, 34),
(322, 'badword', 0, 0, 47, 19),
(324, 'waw', 0, 0, 46, 19),
(330, NULL, 1, 0, 46, 39);

-- --------------------------------------------------------

--
-- Structure de la table `avisp`
--

CREATE TABLE `avisp` (
  `idAP` int(11) NOT NULL,
  `commAP` varchar(255) NOT NULL,
  `star` int(11) DEFAULT NULL,
  `fav` tinyint(1) NOT NULL,
  `idPlat` int(11) NOT NULL,
  `iduap` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avisp`
--

INSERT INTO `avisp` (`idAP`, `commAP`, `star`, `fav`, `idPlat`, `iduap`) VALUES
(82, 'test', 0, 1, 39, 32),
(83, 'ye3', 0, 0, 39, 32),
(84, 'mmm', 0, 0, 41, 32),
(85, 'waw', 5, 1, 41, 32),
(87, ' sur \"tey \":waw', 2, 1, 41, 19),
(88, 'modification  test avis', 3, 1, 42, 39);

-- --------------------------------------------------------

--
-- Structure de la table `equipement`
--

CREATE TABLE `equipement` (
  `idEq` int(11) NOT NULL,
  `nomEq` varchar(255) NOT NULL,
  `descEq` varchar(255) NOT NULL,
  `docEq` varchar(255) NOT NULL,
  `imageEq` varchar(255) DEFAULT NULL,
  `categEq` varchar(255) NOT NULL,
  `noteEq` int(11) DEFAULT NULL,
  `marqueEq` varchar(255) DEFAULT NULL,
  `matriculeEq` varchar(255) DEFAULT NULL,
  `datePreMainte` date DEFAULT NULL,
  `dateProMainte` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `equipement`
--

INSERT INTO `equipement` (`idEq`, `nomEq`, `descEq`, `docEq`, `imageEq`, `categEq`, `noteEq`, `marqueEq`, `matriculeEq`, `datePreMainte`, `dateProMainte`) VALUES
(46, 'Tapis de course', 'Un tapis de course est un équipement de fitness motorisé conçu pour la course ou la marche à l\'intérieur. Il se compose d\'une ceinture roulante qui se déplace à une vitesse réglable.', 'Commencez par allumer l\'appareil et ajustez la vitesse et l\'inclinaison selon vos préférences. En position centrale, démarrez en douceur et maintenez une posture stable en utilisant les poignées pour l\'équilibre.', 'tapis.jpg', 'Fitness', 0, 'Hammer', '367783', '2024-04-01', '2024-05-15'),
(47, 'Bike', 'Un appareil de cardio-training conçu pour offrir une alternative pratique à la bicyclette traditionnelle. Doté d\'une selle réglable et de pédales fixées à des résistances ajustables.', 'Ajustez le siège à la hauteur appropriée, démarrez avec une résistance légère, puis asseyez-vous confortablement en maintenant une posture droite. Commencez à pédaler à un rythme modéré, augmentant graduellement la résistance pour intensifier votre entraî', 'velo.jpg', 'Cardio-training', 0, 'Hammer', '095875', '2024-04-01', '2024-11-30'),
(54, 'Climber Step escalier', 'Le Climber Step est un équipement de fitness conçu pour simuler l\'effort de monter des escaliers, offrant ainsi un entraînement cardiovasculaire efficace.', 'Ajustez la résistance selon votre niveau, utilisez les poignées pour solliciter le haut du corps, et suivez les données du moniteur intégré. Commencez doucement et intensifiez progressivement.', 'escalier.jpg', 'Fitness', 0, 'Hammer', '673783', '2024-04-01', '2024-05-23'),
(56, 'Banc d’entraînement', 'un banc d\'entraînement est un équipement de fitness avec un dossier réglable, permettant une variété d\'exercices de musculation comme le développé couché. Il offre polyvalence pour l\'entraînement musculaire à domicile ou en salle de sport.', 'Pour commencer, montez le banc en suivant les instructions du fabricant. Ajustez le dossier et le siège pour les exercices spécifiques que vous souhaitez réaliser. Utilisez-le pour des mouvements classiques tels que le développé couché ou le curl biceps.', 'dos.jpg', 'Fitness', 0, 'Hammer', '435097', '2024-04-01', '2024-08-11'),
(99, 'Tes', 'TEST', 'TEST', 'dos.jpg', 'Musculation', 0, 'Hammer', '456789', '2019-01-01', '2025-01-01');

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE `evenement` (
  `id_eve` int(11) NOT NULL,
  `nom_eve` varchar(255) NOT NULL,
  `date_deve` date NOT NULL,
  `date_feve` date NOT NULL,
  `nbr_max` int(11) DEFAULT NULL,
  `adresse_eve` varchar(255) NOT NULL,
  `image_eve` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evenement`
--

INSERT INTO `evenement` (`id_eve`, `nom_eve`, `date_deve`, `date_feve`, `nbr_max`, `adresse_eve`, `image_eve`) VALUES
(96, 'Marathon', '2024-08-14', '2024-08-18', 20, 'sousse', 'bodyattack.jpg'),
(99, 'FootBall', '2024-05-29', '2024-06-02', 50, 'Tunis', 'foot.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `participation`
--

CREATE TABLE `participation` (
  `id_p` int(11) NOT NULL,
  `nom_p` varchar(255) NOT NULL,
  `prenom_p` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `idf_event` int(11) NOT NULL,
  `age` int(11) NOT NULL,
  `id_User` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `participation`
--

INSERT INTO `participation` (`id_p`, `nom_p`, `prenom_p`, `email`, `idf_event`, `age`, `id_User`) VALUES
(77, 'yosr', 'ben amor', 'yosrbenamor@gmail.com', 96, 22, 32),
(78, 'hammami', 'salma', 'salma.hammemi@esprit.tn', 99, 22, 19),
(79, 'hammami', 'salma', 'salma.hammemi@esprit.tn', 99, 22, 19);

-- --------------------------------------------------------

--
-- Structure de la table `plat`
--

CREATE TABLE `plat` (
  `idP` int(11) NOT NULL,
  `nomP` varchar(255) DEFAULT NULL,
  `prixP` float DEFAULT NULL,
  `descP` varchar(255) DEFAULT NULL,
  `alergieP` varchar(255) DEFAULT NULL,
  `etatP` tinyint(1) DEFAULT NULL,
  `photop` varchar(255) DEFAULT 'food4.png',
  `calories` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `plat`
--

INSERT INTO `plat` (`idP`, `nomP`, `prixP`, `descP`, `alergieP`, `etatP`, `photop`, `calories`) VALUES
(39, 'test modification', 10, 'yum yum salade verte wow cest tres bien', 'none', 0, 'food4.png', 5),
(41, 'Soupe', 50, 'Tres bon', 'None', 1, 'food6.png', 549),
(42, 'testplat', 12, 'cest un test', 'lactose', 1, 'food6.png', 123);

-- --------------------------------------------------------

--
-- Structure de la table `reclamation`
--

CREATE TABLE `reclamation` (
  `idRec` int(11) NOT NULL,
  `categorieRec` varchar(255) NOT NULL,
  `descriptionRec` varchar(255) NOT NULL,
  `pieceJointeRec` varchar(255) NOT NULL,
  `oddRec` varchar(255) NOT NULL,
  `serviceRec` varchar(255) NOT NULL,
  `etatRec` int(11) DEFAULT NULL,
  `idU` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reclamation`
--

INSERT INTO `reclamation` (`idRec`, `categorieRec`, `descriptionRec`, `pieceJointeRec`, `oddRec`, `serviceRec`, `etatRec`, `idU`) VALUES
(3, 'Qualité', 'mauvaise', 'C:\\xampp\\tmp\\phpC3E8.tmp', 'ODD13', 'Hygiène', 1, 32),
(4, 'Communication', 'too bad', 'C:\\xampp\\tmp\\php2581.tmp', 'ODD1', 'Sécurité', NULL, 32),
(5, 'Durabilité', 'pas bon', 'C:\\xampp\\tmp\\phpBEB5.tmp', 'ODD12', 'Discipline', NULL, 32),
(6, 'Problème Technique', 'site inaccessible', 'C:\\xampp\\tmp\\php6DB3.tmp', 'ODD5', 'Sécurité', NULL, 32),
(7, 'Qualité', 'mauvaise', 'C:\\xampp\\tmp\\php127F.tmp', 'ODD1', 'Hygiène', NULL, 34),
(8, 'Problème Technique', 'mauvaise', 'C:\\xampp\\tmp\\php7BC4.tmp', 'ODD13', 'Sécurité', NULL, 39);

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `idreservation` int(11) NOT NULL,
  `ids` int(11) NOT NULL,
  `nompersonne` varchar(255) NOT NULL,
  `prenompersonne` varchar(255) NOT NULL,
  `iduser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `seance`
--

CREATE TABLE `seance` (
  `idseance` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `horaire` time NOT NULL,
  `jourseance` varchar(255) NOT NULL,
  `numesalle` int(11) NOT NULL,
  `duree` varchar(255) NOT NULL,
  `imageseance` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `seance`
--

INSERT INTO `seance` (`idseance`, `nom`, `horaire`, `jourseance`, `numesalle`, `duree`, `imageseance`) VALUES
(119, 'Boxe', '19:00:00', 'Jeudi', 3, '60min', 'Boxe.jpg'),
(121, 'Bodypump', '16:00:00', 'Vendredi', 3, '93min', 'bodypump.jpg'),
(122, 'BodyAttack', '21:00:00', 'Lundi', 2, '98min', 'bodyattack.jpg'),
(124, 'Spinning', '20:00:00', 'Jeudi', 2, '44min', 'spinning.jpg'),
(126, 'Crossfit', '23:00:00', 'Jeudi', 2, '90min', 'crossfit.jpg'),
(127, 'Yoga', '19:00:00', 'Vendredi', 3, '73min', 'yoga.png'),
(128, 'Boxe', '22:00:00', 'Jeudi', 3, '66min', 'boxe.jpg'),
(130, 'Yoga', '16:00:00', 'Vendredi', 3, '30min', 'yoga.png');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `statut` tinyint(4) DEFAULT 0,
  `nb_tentative` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `date_inscription` date DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `role` varchar(20) NOT NULL,
  `poids` float DEFAULT NULL,
  `taille` float DEFAULT NULL,
  `sexe` varchar(255) DEFAULT NULL,
  `tfa` int(1) DEFAULT NULL,
  `tfa_secret` varchar(255) DEFAULT NULL,
  `isVerified` int(11) NOT NULL,
  `activation_token` varchar(50) DEFAULT NULL,
  `reset_token` varchar(60) DEFAULT NULL,
  `disable_token` varchar(60) DEFAULT NULL,
  `verificationCode` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `nom`, `prenom`, `mail`, `mdp`, `statut`, `nb_tentative`, `image`, `date_naissance`, `date_inscription`, `tel`, `role`, `poids`, `taille`, `sexe`, `tfa`, `tfa_secret`, `isVerified`, `activation_token`, `reset_token`, `disable_token`, `verificationCode`) VALUES
(17, 'bensmida', 'selim', 'selim.dih@gmail.com', 'f48ac822376a54dbe8667a5b3a649058', 0, NULL, '', '2002-03-04', NULL, NULL, 'CLIENT', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(19, 'hammami', 'salma', 'salma.hammemi@esprit.tn', 'f6852b2a3ac0cd7e69c801f69eddb57a', 0, 0, '', '2004-03-05', '2024-03-01', '+9312258774', 'CLIENT', 43.2692, 1.44231, 'Homme', NULL, NULL, 0, NULL, NULL, NULL, NULL),
(28, 'mayssa', 'hakimi', 'meryemboukraa199@gmail.com', '$2y$13$b.WOsTW9yFrdUDEkCxQNausCPmslv9yjNEyvAbL9Oyubje2008UIu', 0, 0, 'images.png', '1904-01-01', '2024-04-18', '93172686', 'CLIENT', 255, 210, 'femme', NULL, NULL, 0, NULL, NULL, NULL, NULL),
(32, 'yosr', 'ben amor', '', '$2y$13$xmJHS06N1TSILHrbM4mo9.4727frzm8QgDzFe4RvWatBZPFfsCNLy', 0, 0, 'https://uc6b0f2f3c7e8868ce8cc6d60085.dl.dropboxusercontent.com/cd/0/get/CSUB335-_mtR_ReY9DNDEYgMjfCb15f8CQJ2WeMARJuKUn1VdBu4_Oo021oQNtsgwAe78sJEMnUpwKJD5eHRYu5i2hZnzjO89Ty01m4yxF6M5DTNSUOl3ptxx8WxiRQVtQmLQ-puDhBq-WbThEnr2Lyp4FpPsYxamn0B3jZ-nCC62w/file', '1904-01-01', '2024-05-06', '97336009', 'ADMIN', 150, 150, 'Homme', NULL, NULL, 1, NULL, NULL, NULL, NULL),
(34, 'YOSR', 'BEN AMOR', 'hakimimayssa@gmail.com', '$2y$13$PWpuXuK.b4m9jgfTZ7bChOEgZJqoqEURO.N4kU8rqWk/Hg9gFLjXe', 1, 3, 'https://uca086556907da63151ea8f38544.dl.dropboxusercontent.com/cd/0/get/CSYz2t4343ISHOKMmwG8bkCob4Wi9_nCHrsNghxM7WqNeBVYH9MD9y6fI8HX5VeGvYCce769kRADMsJ2CVnHp1YjpBvR_fsp4nrPY3TUVKoPD2viAhS0-RS8lxpzBjmdaRNUpi3a78w1KrLvvvXlWGiRM-55v1Y8Mn06YU9uJVCrA8AdOCfZrIR', '1904-01-01', '2024-05-07', '97336009', 'CLIENT', 150, 150, 'Femme', NULL, 'VKQB5IEFMV4BLB6SUIMQK4L5R5L2M23EAH7NSUKOAIFUHOQAKTKA', 1, NULL, NULL, NULL, NULL),
(36, 'ff', 'ff', 'meryem@meryem.com', '$2y$13$D52lszwB3it1H4kcQKPQ7O10Z6GEImd1wMjsdYon1GA2omKkE0j6G', 0, 0, NULL, NULL, '2024-05-07', '23456789', 'CLIENT', 150, 150, 'Homme', NULL, NULL, 1, NULL, NULL, '1946d81960c8bb9342aca97e177a5867e712f572660e587570c01797e3d5', NULL),
(39, 'YOSRr', 'BEN AMOR', 'yosrbenamor2002@gmail.com', '05a671c66aefea124cc08b76ea6d30bb', 1, 0, 'https://uc66f2796a5e395faa040bc05108.dl.dropboxusercontent.com/cd/0/get/CS4snNYWbq_TOuCFSmGGa5wIPga1RLNnSZO2AR3Vu2GEXnvNwm6T2GHciqtX7OVYeJEAOx5UIfJIZx6SApCvpL_bFQHf0L5XK0sZpooy_LSq75c4dl_H4T0waupxBM5bx1YZbjgxxz8-BLA7OKAD2zKflJsPj5xOYCWz8fe0aBFMqw/file', '1969-01-01', '2024-05-15', '97336009', 'ADMIN', 150, 150, 'Femme', 1, 'KVURHNIUYNMCM3XFJO4O3C4UE3Q3JKJF', 1, NULL, NULL, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `abonnement`
--
ALTER TABLE `abonnement`
  ADD PRIMARY KEY (`idAb`),
  ADD KEY `idu_pk1` (`idU`);

--
-- Index pour la table `avisequipement`
--
ALTER TABLE `avisequipement`
  ADD PRIMARY KEY (`idAEq`),
  ADD KEY `fk_idEq` (`idEq`),
  ADD KEY `fk_idUs` (`idUs`);

--
-- Index pour la table `avisp`
--
ALTER TABLE `avisp`
  ADD PRIMARY KEY (`idAP`),
  ADD KEY `toto` (`idPlat`),
  ADD KEY `iduap` (`iduap`);

--
-- Index pour la table `equipement`
--
ALTER TABLE `equipement`
  ADD PRIMARY KEY (`idEq`);

--
-- Index pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id_eve`);

--
-- Index pour la table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id_p`),
  ADD KEY `id_eve` (`idf_event`),
  ADD KEY `user` (`id_User`);

--
-- Index pour la table `plat`
--
ALTER TABLE `plat`
  ADD PRIMARY KEY (`idP`);

--
-- Index pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`idRec`),
  ADD KEY `idu_pk2` (`idU`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`idreservation`),
  ADD KEY `ids` (`ids`),
  ADD KEY `iduser` (`iduser`);

--
-- Index pour la table `seance`
--
ALTER TABLE `seance`
  ADD PRIMARY KEY (`idseance`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `abonnement`
--
ALTER TABLE `abonnement`
  MODIFY `idAb` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `avisequipement`
--
ALTER TABLE `avisequipement`
  MODIFY `idAEq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=332;

--
-- AUTO_INCREMENT pour la table `avisp`
--
ALTER TABLE `avisp`
  MODIFY `idAP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT pour la table `equipement`
--
ALTER TABLE `equipement`
  MODIFY `idEq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id_eve` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT pour la table `participation`
--
ALTER TABLE `participation`
  MODIFY `id_p` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT pour la table `plat`
--
ALTER TABLE `plat`
  MODIFY `idP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT pour la table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `idRec` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `idreservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT pour la table `seance`
--
ALTER TABLE `seance`
  MODIFY `idseance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `abonnement`
--
ALTER TABLE `abonnement`
  ADD CONSTRAINT `idu_pk1` FOREIGN KEY (`idU`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `avisequipement`
--
ALTER TABLE `avisequipement`
  ADD CONSTRAINT `fk_idEq` FOREIGN KEY (`idEq`) REFERENCES `equipement` (`idEq`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_idUs` FOREIGN KEY (`idUs`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `avisp`
--
ALTER TABLE `avisp`
  ADD CONSTRAINT `avisp_ibfk_1` FOREIGN KEY (`idPlat`) REFERENCES `plat` (`idP`),
  ADD CONSTRAINT `avisp_ibfk_2` FOREIGN KEY (`iduap`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`idf_event`) REFERENCES `evenement` (`id_eve`),
  ADD CONSTRAINT `user` FOREIGN KEY (`id_User`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `idu_pk2` FOREIGN KEY (`idU`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`ids`) REFERENCES `seance` (`idseance`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`iduser`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
