-- phpMyAdmin SQL Dump
-- version 4.2.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2014
-- Server version: 5.6.19
-- PHP Version: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `privateask`
--

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
`id` int(10) unsigned NOT NULL,
  `fromuser` varchar(20) CHARACTER SET ascii NOT NULL,
  `touser` varchar(20) CHARACTER SET ascii NOT NULL,
  `question` varchar(200) NOT NULL,
  `answer` varchar(200) DEFAULT NULL,
  `publicasker` tinyint(1) NOT NULL DEFAULT '0',
  `timeasked` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timeanswered` datetime DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Table structure for table `question_reports`
--

CREATE TABLE IF NOT EXISTS `question_reports` (
`id` int(10) unsigned NOT NULL,
  `qid` int(10) unsigned NOT NULL,
  `reporter` varchar(20) CHARACTER SET ascii NOT NULL,
  `reason` set('illegal','threat','tos','porn','copyright','other') CHARACTER SET ascii NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(20) CHARACTER SET ascii NOT NULL,
  `hs_pass` varchar(100) CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'Hashed Salted Password',
  `realname` varchar(40) CHARACTER SET utf8 NOT NULL,
  `friends` varchar(1000) CHARACTER SET ascii NOT NULL DEFAULT '[]' COMMENT 'in JSON',
  `whosees` set('friends','users','all') CHARACTER SET ascii NOT NULL DEFAULT 'friends',
  `whoasks` set('friends','users','all') CHARACTER SET ascii NOT NULL DEFAULT 'friends',
  `deleteon` date DEFAULT NULL,
  `backcolor` varchar(7) CHARACTER SET ascii NOT NULL DEFAULT '#220963',
  `textcolor` varchar(7) CHARACTER SET ascii NOT NULL DEFAULT '#F0F8FF',
  `textfont` varchar(20) CHARACTER SET ascii NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Triggers `users`
--
DELIMITER //
CREATE TRIGGER `Rename Qs+reports a deleted user asked` AFTER DELETE ON `users`
 FOR EACH ROW BEGIN

UPDATE `questions` SET `fromuser` = 'deleteduser' WHERE `fromuser` = OLD.username;
UPDATE `question_reports` SET `reporter` = 'deleteduser' WHERE `reporter` = OLD.username;

END
//
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
 ADD PRIMARY KEY (`id`), ADD KEY `FK_touser` (`touser`), ADD FULLTEXT KEY `fulltext_index` (`question`,`answer`);

--
-- Indexes for table `question_reports`
--
ALTER TABLE `question_reports`
 ADD PRIMARY KEY (`id`), ADD KEY `FK_question_id` (`qid`), ADD KEY `FK_reporter` (`reporter`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`username`), ADD FULLTEXT KEY `friends` (`friends`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `question_reports`
--
ALTER TABLE `question_reports`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`touser`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question_reports`
--
ALTER TABLE `question_reports`
ADD CONSTRAINT `FK_question_id` FOREIGN KEY (`qid`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `question_reports_ibfk_1` FOREIGN KEY (`reporter`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `Delete users` ON SCHEDULE EVERY 1 DAY STARTS '2014-06-21 12:00:00' ON COMPLETION PRESERVE ENABLE DO DELETE FROM `users` WHERE `deleteon` < CURRENT_DATE$$

DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
