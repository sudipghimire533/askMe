-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 01, 2020 at 01:04 PM
-- Server version: 10.3.22-MariaDB-1
-- PHP Version: 7.3.15-3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `askme`
--

-- --------------------------------------------------------

--
-- Table structure for table `Answer`
--

CREATE TABLE `Answer` (
  `Id` int(11) NOT NULL,
  `Author` int(11) NOT NULL,
  `WrittenFor` int(11) NOT NULL,
  `Description` text NOT NULL,
  `ClapsCount` int(11) NOT NULL DEFAULT 0,
  `AddedOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `ModifiedOn` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Question`
--

CREATE TABLE `Question` (
  `Id` int(11) NOT NULL,
  `Title` varchar(200) NOT NULL,
  `URLTitle` varchar(200) NOT NULL,
  `Author` int(11) NOT NULL,
  `AddedOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `ModifiedOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `Description` text NOT NULL,
  `AcceptedAnswer` int(11) DEFAULT NULL,
  `ClapsCount` int(11) NOT NULL DEFAULT 0,
  `VisitCount` int(11) NOT NULL DEFAULT 0,
  `LastActive` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `QuestionTag`
--

CREATE TABLE `QuestionTag` (
  `Question` int(11) NOT NULL,
  `Tag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Tags`
--

CREATE TABLE `Tags` (
  `Id` int(11) NOT NULL,
  `Name` varchar(20) NOT NULL,
  `UseCount` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `Id` int(11) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `FirstName` varchar(20) NOT NULL,
  `LastName` varchar(20) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone` varchar(15) NOT NULL,
  `Location` varchar(30) DEFAULT NULL,
  `CreatedOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `Intro` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `UserBookmarks`
--

CREATE TABLE `UserBookmarks` (
  `User` int(11) NOT NULL,
  `Question` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `UserFollow`
--

CREATE TABLE `UserFollow` (
  `FollowedBy` int(11) NOT NULL,
  `FollowedTo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `UserTag`
--

CREATE TABLE `UserTag` (
  `User` int(11) NOT NULL,
  `Tag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Answer`
--
ALTER TABLE `Answer`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `WrittenFor` (`WrittenFor`),
  ADD KEY `Author` (`Author`);

--
-- Indexes for table `Question`
--
ALTER TABLE `Question`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `URLTitle` (`URLTitle`),
  ADD KEY `Author` (`Author`),
  ADD KEY `AcceptedAnswer` (`AcceptedAnswer`);

--
-- Indexes for table `QuestionTag`
--
ALTER TABLE `QuestionTag`
  ADD PRIMARY KEY (`Question`,`Tag`),
  ADD KEY `Tag` (`Tag`);

--
-- Indexes for table `Tags`
--
ALTER TABLE `Tags`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `UserName` (`UserName`);

--
-- Indexes for table `UserBookmarks`
--
ALTER TABLE `UserBookmarks`
  ADD PRIMARY KEY (`User`,`Question`),
  ADD KEY `Question` (`Question`);

--
-- Indexes for table `UserFollow`
--
ALTER TABLE `UserFollow`
  ADD PRIMARY KEY (`FollowedBy`,`FollowedTo`),
  ADD KEY `FollowedTo` (`FollowedTo`);

--
-- Indexes for table `UserTag`
--
ALTER TABLE `UserTag`
  ADD PRIMARY KEY (`User`,`Tag`),
  ADD KEY `Tag` (`Tag`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Answer`
--
ALTER TABLE `Answer`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Question`
--
ALTER TABLE `Question`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tags`
--
ALTER TABLE `Tags`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `User`
--
ALTER TABLE `User`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Answer`
--
ALTER TABLE `Answer`
  ADD CONSTRAINT `Answer_ibfk_1` FOREIGN KEY (`WrittenFor`) REFERENCES `Question` (`Id`),
  ADD CONSTRAINT `Answer_ibfk_2` FOREIGN KEY (`Author`) REFERENCES `User` (`Id`);

--
-- Constraints for table `Question`
--
ALTER TABLE `Question`
  ADD CONSTRAINT `Question_ibfk_1` FOREIGN KEY (`Author`) REFERENCES `User` (`Id`),
  ADD CONSTRAINT `Question_ibfk_2` FOREIGN KEY (`AcceptedAnswer`) REFERENCES `Answer` (`Id`);

--
-- Constraints for table `QuestionTag`
--
ALTER TABLE `QuestionTag`
  ADD CONSTRAINT `QuestionTag_ibfk_1` FOREIGN KEY (`Question`) REFERENCES `Question` (`Id`),
  ADD CONSTRAINT `QuestionTag_ibfk_2` FOREIGN KEY (`Tag`) REFERENCES `Tags` (`Id`);

--
-- Constraints for table `UserBookmarks`
--
ALTER TABLE `UserBookmarks`
  ADD CONSTRAINT `UserBookmarks_ibfk_1` FOREIGN KEY (`User`) REFERENCES `User` (`Id`),
  ADD CONSTRAINT `UserBookmarks_ibfk_2` FOREIGN KEY (`Question`) REFERENCES `Question` (`Id`);

--
-- Constraints for table `UserFollow`
--
ALTER TABLE `UserFollow`
  ADD CONSTRAINT `UserFollow_ibfk_1` FOREIGN KEY (`FollowedBy`) REFERENCES `User` (`Id`),
  ADD CONSTRAINT `UserFollow_ibfk_2` FOREIGN KEY (`FollowedTo`) REFERENCES `User` (`Id`);

--
-- Constraints for table `UserTag`
--
ALTER TABLE `UserTag`
  ADD CONSTRAINT `UserTag_ibfk_1` FOREIGN KEY (`User`) REFERENCES `User` (`Id`),
  ADD CONSTRAINT `UserTag_ibfk_2` FOREIGN KEY (`Tag`) REFERENCES `Tags` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
