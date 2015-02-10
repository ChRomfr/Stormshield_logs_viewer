-- phpMyAdmin SQL Dump
-- version 4.2.9
-- http://www.phpmyadmin.net
--
-- Client :  localhost:3307
-- Généré le :  Mar 10 Février 2015 à 11:25
-- Version du serveur :  5.6.21-log
-- Version de PHP :  5.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `internet_logs`
--

-- --------------------------------------------------------

--
-- Structure de la table `domains`
--

CREATE TABLE IF NOT EXISTS `domains` (
`id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `internet` int(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
`id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'nom du fichier',
  `date_add` datetime NOT NULL COMMENT 'Date insertion du fichier',
  `duration` varchar(10) NOT NULL COMMENT 'Duree en seconde',
  `lines` varchar(10) NOT NULL COMMENT 'Nombre de ligne traite dans le fichier'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Contient les fichiers de log';

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
`id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `hours` time DEFAULT NULL,
  `proto` varchar(20) DEFAULT NULL,
  `src_name` varchar(100) DEFAULT NULL,
  `src_ip` varchar(15) DEFAULT NULL,
  `src_port` varchar(5) DEFAULT NULL,
  `src_port_name` varchar(50) DEFAULT NULL,
  `dst_ip` varchar(15) DEFAULT NULL,
  `dst_name` varchar(150) DEFAULT NULL,
  `dst_port` varchar(5) DEFAULT NULL,
  `dst_port_name` varchar(50) DEFAULT NULL,
  `sent` bigint(11) DEFAULT NULL,
  `rcvd` bigint(11) DEFAULT NULL,
  `op` varchar(50) DEFAULT NULL,
  `arg` text,
  `internet` int(1) DEFAULT NULL,
  `dst_id` int(11) NOT NULL,
  `action` int(2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `stats_domains`
--

CREATE TABLE IF NOT EXISTS `stats_domains` (
`id` int(11) NOT NULL,
  `domain_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `hits` int(11) DEFAULT NULL,
  `sent` varchar(45) DEFAULT NULL,
  `rcvd` varchar(45) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `domains`
--
ALTER TABLE `domains`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `files`
--
ALTER TABLE `files`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `logs`
--
ALTER TABLE `logs`
 ADD PRIMARY KEY (`id`), ADD KEY `logs_date` (`date`), ADD KEY `logs_internet` (`internet`);

--
-- Index pour la table `stats_domains`
--
ALTER TABLE `stats_domains`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `domains`
--
ALTER TABLE `domains`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `files`
--
ALTER TABLE `files`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `logs`
--
ALTER TABLE `logs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `stats_domains`
--
ALTER TABLE `stats_domains`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
