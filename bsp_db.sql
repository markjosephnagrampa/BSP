-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 29, 2021 at 10:03 PM
-- Server version: 5.7.31
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bsp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_thread_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(2000) COLLATE utf8_bin NOT NULL,
  `datetime_created` datetime NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `item_thread_id` (`item_thread_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `item_thread_id`, `user_id`, `message`, `datetime_created`) VALUES
(9, 9, 30, 'Klara and the Sun is the eighth novel by the Nobel Prize-winning British writer Kazuo Ishiguro, published on March 2, 2021. It is a dystopian science fiction story.\n\nThe Story Shop is offering this item at a discounted price until June 2021. Buy now!', '2021-04-29 01:01:59'),
(10, 9, 29, 'Hi. May I ask if this item is available for shipping outside Metro Manila?', '2021-04-29 01:04:09'),
(11, 9, 30, 'Yes Maam it is. However, you have to pay an additional Php 100.00 for local addresses outside Metro Manila.', '2021-04-29 01:06:32'),
(12, 9, 31, 'I saw this item being offered in another thread for just 40 pesos. Dont get this item here!!', '2021-04-29 01:09:38'),
(13, 9, 34, 'I heard that a hard-copy edition for this book was recently shipped to the Philippines. Will you be posting a hard-cover edition of Klara and the Sun?', '2021-04-29 02:07:19'),
(14, 9, 30, 'Yes Sir we will! Stay posted. :)', '2021-04-29 02:08:58'),
(15, 9, 29, 'Take', '2021-04-29 02:10:51'),
(16, 9, 30, 'Noted Maam. We will close the thread now. Please coordinate with us via PM.', '2021-04-29 02:12:22'),
(17, 15, 33, 'The Committed is a 2021 novel by Viet Thanh Nguyen. It is his second novel and the sequel to his debut novel The Sympathizer, which sold over one million copies and was awarded the 2016 Pulitzer Prize for Fiction. The Committed was published by Grove Press on March 2, 2021.\n\nGet this limited edition item in hardcover now. To reserve this book, please comment TAKE in this thread.', '2021-04-30 05:06:21'),
(18, 15, 34, 'Hi! Can I ask if you have more stocks of this item?', '2021-04-30 05:22:11'),
(19, 13, 32, 'In her enthralling memoir, Whiting Awardâ€“winner Owusu (So Devilish a Fire) assesses the impact of key events in her life via the metaphor of earthquakes. Check out Aftershocks: A memoir - offered now by Hooked to Books! To reserve this item, please comment TAKE in this thread post. Reservations are done on a first come first serve basis.', '2021-04-30 05:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) COLLATE utf8_bin NOT NULL,
  `email` varchar(1000) COLLATE utf8_bin NOT NULL,
  `address` varchar(1000) COLLATE utf8_bin NOT NULL,
  `contact_no` varchar(1000) COLLATE utf8_bin NOT NULL,
  `image_location` varchar(2000) COLLATE utf8_bin DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `approval_status` enum('pending','approved','denied') COLLATE utf8_bin NOT NULL,
  `date_denied` date DEFAULT NULL,
  PRIMARY KEY (`company_id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `name`, `email`, `address`, `contact_no`, `image_location`, `user_id`, `approval_status`, `date_denied`) VALUES
(7, 'The Story Shop', 'seller1@gmail.com', 'Mega State Building Araneta Ave Cor Agno Ext1100', '02-123-4567', '../img/companies/7.png', 30, 'approved', NULL),
(8, 'Hooked to Books', 'seller2@gmail.com', '6371 Estrella St., Guadalupe Viejo, NCR', '02-999-8888', '../img/companies/8.jpg', 32, 'approved', NULL),
(9, 'Beanie Books', 'seller3@gmail.com', '15/F Ps Bank Center 777 Paseo De Roxas Street 1226', '046-123-5555', '../img/companies/9.png', 33, 'approved', NULL),
(10, 'A Bogus Bookshop', 'seller4@gmail.com', 'A bogus address', 'A bogus contact', NULL, 35, 'denied', '2021-04-29'),
(11, 'Growing Readers', 'seller5@gmail.com', '1001-1002 Medical Plaza Building Cor. Amorsolo & Dela Rosa Streets 1200', '046-123-2222', '../img/companies/11.jpg', 36, 'pending', NULL),
(12, 'Strand Bookstore', 'seller6@gmail.com', 'Rm. 6 Sky Dragon Building Domestic Drive 1300', '046-123-2233', '../img/companies/12.jpg', 37, 'pending', NULL),
(13, 'Bogus Company 1', 'bogus_seller@gmail.com', '101 Avocado St., Fruit Village, Metro Manila', '046-123-5553', NULL, 38, 'denied', '2021-04-30'),
(14, 'Bogus Company 2', 'bogus_seller2@gmail.com', '102 Avocado St., Fruit Village, Metro Manila', '046-123-2223', '../img/companies/14.png', 39, 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
CREATE TABLE IF NOT EXISTS `complaints` (
  `complaint_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(2000) COLLATE utf8_bin NOT NULL,
  `date_created` date NOT NULL,
  `is_sent_by_buyer` tinyint(1) NOT NULL,
  `image_location` varchar(2000) COLLATE utf8_bin NOT NULL,
  `complaint_type` enum('Failure to pay','Foul/Obscene messages or comments','Spamming','Promoting content of competitors','Failure to deliver purchased items','Damaged item','Other') COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`complaint_id`),
  KEY `company_id` (`company_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`complaint_id`, `company_id`, `user_id`, `message`, `date_created`, `is_sent_by_buyer`, `image_location`, `complaint_type`) VALUES
(14, 7, 31, 'John Paolo Nagrampa, the user seen posting the bottom-most comment, insinuated a clear violation of promoting the content of our competitors and defaming our post by encouraging other buyers not to get this item from us. Please help us in curbing this adverse behavior.', '2021-04-29', 0, '../img/complaints/14.png', 'Promoting content of competitors'),
(15, 7, 31, 'As you can see, the buyer is posting some strong language here. Our company tried to reason with him as best as we can, but to no avail.', '2021-04-29', 0, '../img/complaints/15.png', 'Foul/Obscene messages or comments'),
(16, 8, 31, 'No reply until now.', '2021-04-29', 1, '../img/complaints/16.png', 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `item_threads`
--

DROP TABLE IF EXISTS `item_threads`;
CREATE TABLE IF NOT EXISTS `item_threads` (
  `item_thread_id` int(11) NOT NULL AUTO_INCREMENT,
  `book_title` varchar(1000) COLLATE utf8_bin NOT NULL,
  `binding_type` enum('Case Binding (Hardcover)','Perfect Binding (Softcover)','Saddle Stitch Binding','Spiral Binding','Other') COLLATE utf8_bin NOT NULL,
  `author` varchar(1000) COLLATE utf8_bin NOT NULL,
  `genre` varchar(1000) COLLATE utf8_bin NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_location` varchar(2000) COLLATE utf8_bin NOT NULL,
  `company_id` int(11) NOT NULL,
  `date_created` date NOT NULL,
  `is_closed` tinyint(1) NOT NULL,
  `date_closed` date DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`item_thread_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `item_threads`
--

INSERT INTO `item_threads` (`item_thread_id`, `book_title`, `binding_type`, `author`, `genre`, `price`, `image_location`, `company_id`, `date_created`, `is_closed`, `date_closed`, `is_deleted`) VALUES
(9, 'Klara and the Sun', 'Perfect Binding (Softcover)', 'Kazuo Ishiguro', 'Science Fiction', '50.00', '../img/items/9.jpg', 7, '2021-04-29', 0, NULL, 0),
(10, 'Of Women and Salt', 'Saddle Stitch Binding', 'Gabriela Garcia', 'Literary Fiction', '75.50', '../img/items/10.jpg', 7, '2021-04-29', 0, NULL, 0),
(11, 'Detransition, Baby: A Novel', 'Spiral Binding', 'Torrey Peters', 'Transgender Fiction', '100.25', '../img/items/11.jpg', 7, '2021-04-29', 0, NULL, 0),
(12, 'Let Me Tell You What I Mean', 'Other', 'Oan Didion', 'Biography', '85.00', '../img/items/12.jpg', 8, '2021-04-29', 0, NULL, 0),
(13, 'Aftershocks: A Memoir', 'Case Binding (Hardcover)', 'Nadia Owusu', 'Biography', '95.00', '../img/items/13.jpg', 8, '2021-04-29', 0, NULL, 0),
(14, 'Caul Baby: A Novel', 'Case Binding (Hardcover)', 'Morgan Jerkins', 'Magical Realism', '130.50', '../img/items/14.jpg', 8, '2021-04-29', 0, NULL, 0),
(15, 'The Committed', 'Case Binding (Hardcover)', 'Viet Thanh Nguyen', 'Crime Novel', '148.00', '../img/items/15.jpg', 9, '2021-04-29', 0, NULL, 0),
(16, 'My Year Abroad: A Novel', 'Other', 'Chang-Rae Lee', 'Satire', '156.25', '../img/items/16.jpg', 9, '2021-04-29', 0, NULL, 0),
(17, 'Klara and the Sun', 'Perfect Binding (Softcover)', 'Kazuo Ishiguro', 'Science Fiction', '40.00', '../img/items/17.jpg', 9, '2021-04-30', 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(2000) COLLATE utf8_bin NOT NULL,
  `datetime_created` datetime NOT NULL,
  `is_sent_by_buyer` tinyint(1) NOT NULL,
  `is_admin_announcement` tinyint(1) NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `company_id` (`company_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `company_id`, `user_id`, `message`, `datetime_created`, `is_sent_by_buyer`, `is_admin_announcement`) VALUES
(17, NULL, 28, 'We will be performing site maintenance at May 1, 2021 at around 5:00 am PHT.', '2021-04-30 05:08:38', 0, 1),
(18, 7, 31, 'Your company is stupid. Dont post overpriced items. Do you think we are fools?', '2021-04-29 01:19:25', 1, 0),
(19, 7, 31, 'Sir, please refrain from using foul language in this site. We understand your concern but product markups are important for our company. Rest assured that our items are of premium quality.', '2021-04-29 01:35:23', 0, 0),
(20, 7, 31, 'I know you overprice your items IDIOTS! Dont give me that explanation!', '2021-04-29 01:36:51', 1, 0),
(21, 7, 29, 'Hi Maam. We would just like to confirm if you will be purchasing Klara and the Sun. ', '2021-04-29 02:14:11', 0, 0),
(22, 7, 29, 'Yes I will. Do you accept COD?', '2021-04-29 02:15:29', 1, 0),
(23, 7, 29, 'Yes Maam.', '2021-04-29 02:16:29', 0, 0),
(24, 7, 29, 'Alright. Please ship the item to this address: 674 Tomas Mapua Street Sta. Cruz 1000.', '2021-04-29 02:17:55', 1, 0),
(25, 8, 31, 'Can I take the Hooked to Books item you posted?', '2021-04-29 02:29:37', 1, 0),
(26, 7, 34, 'I am sending a test message to buyer 3.', '2021-04-29 11:24:01', 0, 0),
(27, 7, 29, 'Noted Maam! Will do this by today po.', '2021-04-29 11:34:42', 0, 0),
(28, 9, 34, 'Hi can I follow up on the status of The Committed item you posted? You havent replied on the thread up to now.', '2021-04-30 05:37:42', 1, 0),
(29, 7, 34, 'Hi Story Shop. Can I inquire what this message was about?', '2021-04-30 05:43:36', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(1000) COLLATE utf8_bin NOT NULL,
  `name` varchar(1000) COLLATE utf8_bin NOT NULL,
  `user_type` enum('admin','seller','buyer') COLLATE utf8_bin NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `image_location` varchar(2000) COLLATE utf8_bin DEFAULT NULL,
  `contact_no` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(1000) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `name`, `user_type`, `is_active`, `image_location`, `contact_no`, `password`) VALUES
(28, 'marknagrampa489@gmail.com', 'Mark Joseph C. Nagrampa', 'admin', 1, '../img/users/28.jpg', '0966-699-3401', 'password'),
(29, 'buyer1@gmail.com', 'Ivana Marriam B. Saberon', 'buyer', 1, '../img/users/29.jpg', '0917-143-4444', 'password'),
(30, 'seller1@gmail.com', 'Marriam B. Ivana', 'seller', 1, NULL, '0911-222-3333', 'password'),
(31, 'buyer2@gmail.com', 'John Paolo Nagrampa', 'buyer', 1, NULL, '', 'password'),
(32, 'seller2@gmail.com', 'John F. Alzaga', 'seller', 1, NULL, '0911-222-4444', 'password'),
(33, 'seller3@gmail.com', 'Vincent M. Datu', 'seller', 1, NULL, '0911-222-6666', 'password'),
(34, 'buyer3@gmail.com', 'Jason R. Basilio', 'buyer', 1, '../img/users/34.png', '0977-666-5555', 'password'),
(35, 'seller4@gmail.com', 'John Smith', 'seller', 1, NULL, '000-000-000', 'password'),
(36, 'seller5@gmail.com', 'Juan dela Cruz', 'seller', 1, NULL, '0911-222-4455', 'password'),
(37, 'seller6@gmail.com', 'Jason Advincula', 'seller', 1, NULL, '0911-222-1122', 'password'),
(38, 'bogus_seller@gmail.com', 'Bogus Person', 'seller', 1, NULL, '0999-888-7777', 'password'),
(39, 'bogus_seller2@gmail.com', 'Bogus Person B', 'seller', 1, NULL, '0911-222-3334', 'password');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`item_thread_id`) REFERENCES `item_threads` (`item_thread_id`);

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `item_threads`
--
ALTER TABLE `item_threads`
  ADD CONSTRAINT `item_threads_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
