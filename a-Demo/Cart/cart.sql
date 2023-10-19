-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2014 at 08:30 AM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cart`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `author` varchar(30) NOT NULL,
  `price` float NOT NULL,
  `photo` varchar(30) DEFAULT NULL,
  `categoryId` int(11) NOT NULL,
  `Description` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoryId` (`categoryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `price`, `photo`, `categoryId`, `Description`) VALUES
(1, 'testedit', 'test', 50, '1.jpg', 2, 'super one'),
(2, 'CES', 'neal', 32, '2.jpg', 1, 'A good book for children education'),
(3, 'we', 'etret', 45, '3.jpg', 2, 'ertert'),
(4, 'test', 'test', 46, '4.jpg', 2, 'nice one'),
(7, 'test no 2', 'foras', 124, '7.jpg', 2, 'sportage kindres.'),
(8, 'foki faras', 'fertor', 12, '8.jpg', 1, 'educateral');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category`) VALUES
(1, 'Education'),
(2, 'Sports');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `cardno` varchar(15) NOT NULL,
  `address` varchar(50) NOT NULL,
  `deliveraddress` varchar(50) NOT NULL,
  `datetime` datetime NOT NULL,
  `deliverd` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `name`, `cardno`, `address`, `deliveraddress`, `datetime`, `deliverd`) VALUES
(1, 'Abubakar', '03335293995', 'cb 64/2 madni town sargodha', 'cb 64/2 madni town sargodha', '2014-02-18 10:21:06', 1),
(2, 'Abubakar', '03335293995', 'cb 64/2, madni town sargodha.', 'cb 64/2, madni town sargodha.', '2014-04-23 13:03:18', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetail`
--

CREATE TABLE IF NOT EXISTS `orderdetail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(11) NOT NULL,
  `bookId` int(11) NOT NULL,
  `count` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orderId` (`orderId`,`bookId`),
  KEY `bookId` (`bookId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `orderdetail`
--

INSERT INTO `orderdetail` (`id`, `orderId`, `bookId`, `count`) VALUES
(1, 1, 1, 5),
(2, 1, 2, 10),
(3, 1, 3, 7),
(4, 2, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `password`) VALUES
(1, 'abubakar', '123');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `category` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD CONSTRAINT `orderdetail_ibfk_1` FOREIGN KEY (`bookId`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `orderdetail_ibfk_2` FOREIGN KEY (`orderId`) REFERENCES `order` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
