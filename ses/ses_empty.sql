-- phpMyAdmin SQL Dump
-- version OVH
-- http://www.phpmyadmin.net
--
-- Généré le : Jeu 27 Juin 2013 à 16:21
-- Version du serveur: 5.1.49
-- Version de PHP: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
--

-- --------------------------------------------------------

--
-- Structure de la table `ses_contact`
--

CREATE TABLE IF NOT EXISTS `ses_contact` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `address` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Structure de la table `ses_favorite`
--

CREATE TABLE IF NOT EXISTS `ses_favorite` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `semail` varchar(255) NOT NULL,
  `server` varchar(255) DEFAULT NULL,
  `tags` varchar(1024) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Structure de la table `ses_message`
--

CREATE TABLE IF NOT EXISTS `ses_message` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `address` varchar(128) NOT NULL,
  `datesent` datetime NOT NULL,
  `semail_id` varchar(255) NOT NULL,
  `commandkey` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=240 ;

-- --------------------------------------------------------

--
-- Structure de la table `ses_participant`
--

CREATE TABLE IF NOT EXISTS `ses_participant` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `address` varchar(128) NOT NULL,
  `semail_id` varchar(255) NOT NULL,
  `dateinvited` datetime NOT NULL,
  `commandkey` varchar(255) NOT NULL,
  `commandsender` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=60 ;

-- --------------------------------------------------------

--
-- Structure de la table `ses_semail`
--

CREATE TABLE IF NOT EXISTS `ses_semail` (
  `id` varchar(255) NOT NULL,
  `type` int(2) NOT NULL,
  `readonly` int(2) NOT NULL,
  `owneraddress` varchar(128) NOT NULL,
  `list` varchar(2048) NOT NULL,
  `tags` varchar(1024) NOT NULL,
  `datecreated` datetime NOT NULL,
  `dateactive` datetime NOT NULL,
  `commandkey` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ses_user`
--

CREATE TABLE IF NOT EXISTS `ses_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `address` varchar(128) NOT NULL,
  `pwd` varchar(128) NOT NULL,
  `mail` varchar(128) NOT NULL,
  `descr` varchar(2048) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `address` (`address`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Structure de la table `ses_validate`
--

CREATE TABLE IF NOT EXISTS `ses_validate` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `md5sum` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=294 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
