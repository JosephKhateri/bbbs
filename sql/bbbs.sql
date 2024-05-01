-- phpMyAdmin SQL Dump
-- version 5.1.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 01, 2024 at 12:33 PM
-- Server version: 8.0.34-26
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbgzgs8zt6q1mh`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbdonations`
--

CREATE TABLE `dbdonations` (
  `DonationID` int NOT NULL,
  `Email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DateOfContribution` date DEFAULT NULL,
  `ContributedSupportType` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ContributionCategory` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `AmountGiven` decimal(10,2) DEFAULT NULL,
  `PaymentMethod` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Memo` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbdonations`
--

INSERT INTO `dbdonations` (`DonationID`, `Email`, `DateOfContribution`, `ContributedSupportType`, `ContributionCategory`, `AmountGiven`, `PaymentMethod`, `Memo`) VALUES
(1, 'alex.johnson@example.com', '2024-03-15', 'Individual Contribution', 'Annual Giving Campaign', '5002.00', 'Credit Card', 'Thank you for your support!'),
(2, 'alex.johnson@example.com', '2024-02-01', 'Individual Contribution', 'Other Solicited Donations', '2500.00', 'Check', 'In response to our fundraising appeal'),
(3, 'alex.johnson@example.com', '2024-01-17', 'Fundraising Events', 'Bowl for Kids\' Sake Corporate Sponsors', '3000.00', 'Bank Transfer', 'Company sponsorship'),
(4, 'casey.brown@example.com', '2024-03-22', 'Individual Contribution', 'Donations - Board', '750.00', 'Credit Card', 'Monthly board contribution'),
(5, 'casey.brown@example.com', '2024-01-10', 'Individual Contribution', 'Gift Assist', '100.00', 'Check', 'Support for those in need'),
(6, 'morgan.green@example.com', '2021-08-05', 'Individual Contribution', 'Other Solicited Donations', '2000.00', 'Credit Card', 'Responding to donation request'),
(7, 'morgan.green@example.com', '2021-07-14', 'Fundraising Events', 'SantaCon Sponsors', '3000.00', 'Cash', 'Support for local event'),
(8, 'jordan.davis@example.com', '2023-06-15', 'Individual Contribution', 'Annual Giving Campaign', '1000.00', 'Credit Card', 'Annual donation pledge'),
(9, 'jordan.davis@example.com', '2023-01-05', 'Fundraising Events', 'Breakfast Fundraiser', '150.00', 'Check', 'Supporting community event'),
(10, 'taylor.white@example.com', '2024-02-18', 'Individual Contribution', 'Unsolicited Donations', '500.00', 'Credit Card', 'Donation without solicitation'),
(11, 'taylor.white@example.com', '2023-09-04', 'Individual Contribution', 'Donations - Board', '100.00', 'Cash', 'Monthly board contribution'),
(12, 'jamie.moore@example.com', '2023-12-18', 'Individual Contribution', 'Other Solicited Donations', '200.00', 'Credit Card', 'In response to donation request'),
(13, 'jamie.moore@example.com', '2023-08-12', 'Fundraising Events', 'Other Events', '50.00', 'Cash', 'Support for local community'),
(14, 'sydney.king@example.com', '2023-04-13', 'Individual Contribution', 'Annual Giving Campaign', '750.00', 'Credit Card', ''),
(15, 'sydney.king@example.com', '2022-12-14', 'Fundraising Events', 'Golf for Kids\' Sake Registrations', '200.00', 'Check', 'Support for charity golf event'),
(16, 'jesse.carter@example.com', '2024-02-01', 'Individual Contribution', 'Gift Assist', '100.00', 'Credit Card', 'Support for those in need'),
(17, 'jesse.carter@example.com', '2023-11-12', 'Fundraising Events', 'Other Events', '100.00', 'Cash', 'Support for community event'),
(18, 'drew.knight@example.com', '2023-06-11', 'Individual Contribution', 'Annual Giving Campaign', '1000.00', 'Credit Card', 'Annual donation pledge'),
(19, 'drew.knight@example.com', '2023-02-15', 'Fundraising Events', 'SantaCon Tickets', '75.00', 'Cash', 'Purchase of event tickets'),
(20, 'cameron.lee@example.com', '2024-01-12', 'Individual Contribution', 'Other Solicited Donations', '250.00', 'Credit Card', 'In response to donation request'),
(21, 'cameron.lee@example.com', '2023-07-14', 'Fundraising Events', 'Other Events', '150.00', 'Check', 'Support for community event'),
(22, 'alex.johnson@example.com', '2023-12-08', 'Individual Contribution', 'Unsolicited Donations', '150.00', 'Cash', 'Support without solicitation'),
(23, 'alex.johnson@example.com', '2022-12-13', 'Fundraising Events', 'Other Events', '100.00', 'Credit Card', 'Support for local community event'),
(24, 'alex.johnson@example.com', '2021-12-01', 'Grants', 'Foundation/Corp.', '500.00', 'Check', 'Contribution to local foundation'),
(25, 'casey.brown@example.com', '2023-11-01', 'Individual Contribution', 'Other Solicited Donations', '200.00', 'Credit Card', 'In response to fundraising appeal'),
(26, 'casey.brown@example.com', '2023-10-14', 'Fundraising Events', 'Bowl for Kids\' Sake Bowler Pledges', '75.00', 'Cash', 'Pledge for bowling event'),
(27, 'casey.brown@example.com', '2023-09-22', 'Grants', 'Local Government', '1000.00', 'Bank Transfer', 'Support for local government initiative'),
(28, 'morgan.green@example.com', '2021-06-01', 'Individual Contribution', 'Donations - Board', '1500.00', 'Check', 'Monthly board contribution'),
(29, 'morgan.green@example.com', '2021-05-02', 'Fundraising Events', 'Golf for Kids\' Sake Registrations', '2000.00', 'Cash', 'Registration for charity golf event'),
(30, 'morgan.green@example.com', '2021-04-05', 'Grants', 'Federal Government', '2000.00', 'Credit Card', 'Contribution to federal grant program'),
(31, 'jordan.davis@example.com', '2022-10-06', 'Individual Contribution', 'Gift Assist', '50.00', 'Credit Card', 'Support for those in need'),
(32, 'jordan.davis@example.com', '2022-06-11', 'Fundraising Events', 'Breakfast Fundraiser', '100.00', 'Check', 'Support for community breakfast event'),
(33, 'jordan.davis@example.com', '2022-01-18', 'Grants', 'Foundation/Corp.', '500.00', 'Credit Card', 'Contribution to corporate foundation'),
(34, 'taylor.white@example.com', '2022-09-08', 'Individual Contribution', 'Unsolicited Donations', '150.00', 'Cash', 'Support without solicitation'),
(35, 'taylor.white@example.com', '2022-05-13', 'Fundraising Events', 'Other Events', '100.00', 'Credit Card', 'Support for local community event'),
(36, 'taylor.white@example.com', '2022-02-01', 'Grants', 'Foundation/Corp.', '500.00', 'Check', 'Contribution to local foundation'),
(37, 'jamie.moore@example.com', '2022-11-01', 'Individual Contribution', 'Other Solicited Donations', '200.00', 'Credit Card', 'In response to fundraising appeal'),
(38, 'jamie.moore@example.com', '2022-08-14', 'Fundraising Events', 'Bowl for Kids\' Sake Bowler Pledges', '75.00', 'Cash', 'Pledge for bowling event'),
(39, 'jamie.moore@example.com', '2022-03-22', 'Grants', 'Local Government', '1000.00', 'Bank Transfer', 'Support for local government initiative'),
(40, 'sydney.king@example.com', '2022-12-01', 'Individual Contribution', 'Donations - Board', '300.00', 'Check', 'Monthly board contribution'),
(41, 'sydney.king@example.com', '2022-08-02', 'Fundraising Events', 'Golf for Kids\' Sake Registrations', '200.00', 'Cash', 'Registration for charity golf event'),
(42, 'sydney.king@example.com', '2022-04-05', 'Grants', 'Federal Government', '2000.00', 'Credit Card', 'Contribution to federal grant program'),
(43, 'jesse.carter@example.com', '2022-10-06', 'Individual Contribution', 'Gift Assist', '50.00', 'Credit Card', 'Support for those in need'),
(44, 'jesse.carter@example.com', '2022-06-11', 'Fundraising Events', 'Breakfast Fundraiser', '100.00', 'Check', 'Support for community breakfast event'),
(45, 'jesse.carter@example.com', '2022-01-18', 'Grants', 'Foundation/Corp.', '500.00', 'Credit Card', 'Contribution to corporate foundation'),
(46, 'drew.knight@example.com', '2022-09-08', 'Individual Contribution', 'Unsolicited Donations', '150.00', 'Cash', 'Support without solicitation'),
(47, 'drew.knight@example.com', '2022-05-13', 'Fundraising Events', 'Other Events', '100.00', 'Credit Card', 'Support for local community event'),
(48, 'drew.knight@example.com', '2022-02-01', 'Grants', 'Foundation/Corp.', '500.00', 'Check', 'Contribution to local foundation'),
(49, 'cameron.lee@example.com', '2022-11-01', 'Individual Contribution', 'Other Solicited Donations', '200.00', 'Credit Card', 'In response to fundraising appeal'),
(50, 'cameron.lee@example.com', '2022-08-14', 'Fundraising Events', 'Bowl for Kids\' Sake Bowler Pledges', '75.00', 'Cash', 'Pledge for bowling event'),
(51, 'cameron.lee@example.com', '2022-03-22', 'Grants', 'Local Government', '1000.00', 'Bank Transfer', 'Support for local government initiative'),
(52, 'alex.johnson@example.com', '2020-12-01', 'Individual Contribution', 'Other Solicited Donations', '250.00', 'Credit Card', 'In response to fundraising appeal'),
(53, 'alex.johnson@example.com', '2020-08-14', 'Fundraising Events', 'Bowl for Kids\' Sake Bowler Pledges', '100.00', 'Cash', 'Pledge for bowling event'),
(54, 'alex.johnson@example.com', '2020-03-22', 'Grants', 'Local Government', '1000.00', 'Bank Transfer', 'Support for local government initiative'),
(55, 'casey.brown@example.com', '2023-08-01', 'Individual Contribution', 'Other Solicited Donations', '200.00', 'Credit Card', 'In response to fundraising appeal'),
(56, 'casey.brown@example.com', '2023-07-14', 'Fundraising Events', 'Bowl for Kids\' Sake Bowler Pledges', '75.00', 'Cash', 'Pledge for bowling event'),
(57, 'casey.brown@example.com', '2023-06-22', 'Grants', 'Local Government', '1000.00', 'Bank Transfer', 'Support for local government initiative'),
(58, 'jsmith@gmail.com', '2024-03-02', 'Individual', 'Annual Giving Campaign', '2000.00', 'Credit', ''),
(59, 'jsmith@gmail.com', '2023-02-24', 'Individual', 'Annual Giving Campaign', '2000.00', 'Credit', ''),
(60, 'jsmith@gmail.com', '2022-02-26', 'Individual', 'Annual Giving Campaign', '2000.00', 'Credit', ''),
(61, 'jsmith@gmail.com', '2021-03-02', 'Individual', 'Annual Giving Campaign', '1500.00', 'Credit', ''),
(62, 'jsmith@gmail.com', '2020-01-15', 'Individual', 'Annual Giving Campaign', '1500.00', 'Credit', ''),
(63, 'ejohnson@gmail.com', '2024-03-02', 'Fundraising Events', 'Golf for Kids\' Sake Registrations', '200.00', 'Cash', ''),
(64, 'ejohnson@gmail.com', '2024-02-15', 'Fundraising Events', 'Bowl for Kids\' Sake Corporate Sponsors', '200.00', 'Cash', ''),
(65, 'ejohnson@gmail.com', '2024-01-12', 'Fundraising Events', 'Other Events', '200.00', 'Cash', ''),
(66, 'ejohnson@gmail.com', '2023-12-18', 'Grants', 'Foundation/Corp.', '200.00', 'Cash', ''),
(67, 'ejohnson@gmail.com', '2023-11-22', 'Individual', 'Gift Assist', '200.00', 'Cash', ''),
(68, 'ejohnson@gmail.com', '2023-10-14', 'Individual', 'Gift Assist', '100.00', 'Cash', ''),
(69, 'ejohnson@gmail.com', '2023-09-04', 'Individual', 'Donations - Board', '100.00', 'Cash', ''),
(70, 'ejohnson@gmail.com', '2023-08-12', 'Fundraising Events', 'SantaCon Sponsors', '100.00', 'Cash', ''),
(71, 'ejohnson@gmail.com', '2023-07-11', 'Fundraising Events', 'Bowl for Kids\' Sake Corporate Sponsors', '100.00', 'Cash', ''),
(72, 'ejohnson@gmail.com', '2023-06-15', 'Fundraising Events', 'Other Events', '100.00', 'Cash', ''),
(73, 'ejohnson@gmail.com', '2023-05-09', 'Individual', 'Other Solicited Donations', '100.00', 'Cash', ''),
(74, 'ejohnson@gmail.com', '2023-04-13', 'Fundraising Events', 'SantaCon Sponsors', '100.00', 'Cash', ''),
(75, 'ejohnson@gmail.com', '2023-03-11', 'Grants', 'Foundation/Corp.', '100.00', 'Cash', ''),
(76, 'ejohnson@gmail.com', '2023-02-18', 'Individual', 'Other Solicited Donations', '100.00', 'Cash', ''),
(77, 'ejohnson@gmail.com', '2023-01-05', 'Fundraising Events', 'Breakfast Fundraiser', '100.00', 'Cash', ''),
(78, 'ejohnson@gmail.com', '2022-12-14', 'Individual', 'Other Solicited Donations', '100.00', 'Cash', ''),
(79, 'sbrown@gmail.com', '2024-02-01', 'Grants', 'Local Government', '5000.00', 'Check', ''),
(80, 'sbrown@gmail.com', '2023-05-02', 'Fundraising Events', 'Breakfast Fundraiser', '200.00', 'Check', ''),
(81, 'sbrown@gmail.com', '2023-05-01', 'Individual', 'Gift Assist', '500.00', 'Check', ''),
(82, 'sbrown@gmail.com', '2023-02-15', 'Fundraising Events', 'SantaCon Sponsors', '300.00', 'Check', ''),
(83, 'mwilliams@gmail.com', '2022-03-15', 'Individual', 'Annual Giving Campaign', '1000.00', 'Credit Card', ''),
(84, 'mwilliams@gmail.com', '2021-12-01', 'Fundraising Events', 'SantaCon Tickets', '500.00', 'Check', ''),
(85, 'mwilliams@gmail.com', '2021-08-17', 'Fundraising Events', 'Breakfast Fundraiser', '2500.00', 'Bank Transfer', ''),
(86, 'djones@gmail.com', '2022-03-22', 'Individual', 'Unsolicited Donations', '750.00', 'Credit Card', ''),
(87, 'djones@gmail.com', '2021-11-10', 'Individual', 'Gift Assist', '1000.00', 'Check', ''),
(88, 'EXAMPLE@gmail.com', '2024-01-01', 'Grants', 'Foundation/Corp.', '1000.00', 'Check', 'EXAMPLE'),
(89, 'msimpson@gmail.com', '2024-03-03', 'Grants', 'Foundation/Corp.', '3000.00', 'Check', 'HELP'),
(90, 'kortega@gmail.com', '2024-04-04', 'Grants', 'Miscellaneous', '4000.00', 'Check', 'YUP'),
(91, 'ialderman@gmail.com', '2024-03-07', 'Grants', 'Miscellaneous', '1200.00', 'Check', 'BOOM'),
(92, 'dwhite@gmail.com', '2024-02-08', 'Fundraising Events', 'Breakfast Fundraiser', '2600.00', 'Check', 'YELP'),
(93, 'TEST11@gmail.com', '2024-01-01', 'Grants', 'Foundation/Corp.', '1000.00', 'Check', 'EXAMPLE'),
(94, 'TEST22@gmail.com', '2024-02-02', 'Grants', 'Foundation/Corp.', '2200.00', 'Check', 'DUH');

-- --------------------------------------------------------

--
-- Table structure for table `dbdonors`
--

CREATE TABLE `dbdonors` (
  `Email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Company` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `FirstName` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `LastName` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PhoneNumber` bigint DEFAULT NULL,
  `Address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `City` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `State` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Zip` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbdonors`
--

INSERT INTO `dbdonors` (`Email`, `Company`, `FirstName`, `LastName`, `PhoneNumber`, `Address`, `City`, `State`, `Zip`) VALUES
('alex.johnson@example.com', 'Nowhere Inc', 'Alex', 'Johnson', 5551234567, '123 Elm Street', 'Springfield', 'IL', 62704),
('cameron.lee@example.com', 'Lee Developments', 'Cameron', 'Lee', 5550123456, '707 Oak Drive', 'Nashville', 'TN', 37203),
('casey.brown@example.com', 'TechSolutions', 'Casey', 'Brown', 5559876543, '456 Pine Street', 'Lincoln', 'NE', 68508),
('cwatson@gmail.com', 'Amazon', 'Charles', 'Watson', 7535468970, '567 Baker ST', 'Dahlgren', 'VA', 22409),
('djones@gmail.com', 'Microsoft', 'David', 'Jones', 5558765432, '210 Cedar Road', 'Hillcrest', 'OH', 45678),
('drew.knight@example.com', 'Knightly Tech', 'Drew', 'Knight', 5559012345, '606 Elm Circle', 'Denver', 'CO', 80202),
('dummy@gmail.com', 'dummy', 'dummy', 'dummy', 1234567890, '1234 ROAD ST', 'Fredericksburg', 'VA', 22401),
('dwhite@gmail.com', 'Pfizer', 'Donna', 'White', 8976572321, '455 Dover ST', 'Buffalo', 'NY', 47068),
('ejohnson@gmail.com', 'Amazon', 'Emily', 'Johnson', 5559876543, '456 Elm Avenue', 'Springfield', 'NY', 54321),
('EXAMPLE@gmail.com', 'EXAMPLE', 'EXAMPLE', 'EXAMPLE', 1234567890, '1234 ROAD ST', 'Fredericksburg', 'VA', 22401),
('FILLER@gmail.com', 'FILLER', 'FILLER', 'FILLER', 1234567890, '1234 ROAD ST', 'Fredericksburg', 'VA', 22401),
('hspector@gmail.com', 'Proctor&Gamble', 'Harvey', 'Spector', 7866753321, '789 Corny ST', 'Essex', 'NJ', 47063),
('ialderman@gmail.com', 'Capital One', 'Isaac', 'Alderman', 7865437865, '300 ACRE ST', 'Chantilly', 'VA', 22407),
('jamie.moore@example.com', 'Moore Innovations', 'Jamie', 'Moore', 5556543210, '303 Cedar Blvd', 'Salem', 'OR', 97301),
('jesse.carter@example.com', 'Carter Hardware', 'Jesse', 'Carter', 5558901234, '505 Pine Needle Drive', 'Raleigh', 'NC', 27601),
('jordan.davis@example.com', 'Davis & Sons', 'Jordan', 'Davis', 5554567891, '101 Oak Lane', 'Columbus', 'OH', 43215),
('jsmith@gmail.com', 'Apple', 'John', 'Smith', 5551234567, '123 Main Street', 'Anytown', 'CA', 12345),
('kortega@gmail.com', 'General Motors', 'Kenny', 'Ortega', 8987654532, '232 Marvin BLVD', 'Dallas', 'TX', 22400),
('morgan.green@example.com', 'Greenery', 'Morgan', 'Green', 5556781234, '789 Maple Avenue', 'Madison', 'WI', 53703),
('msimpson@gmail.com', 'UPS', 'Monica', 'Simpson', 9087652310, '789 AVE ST', 'Austin', 'TX', 22400),
('mwilliams@gmail.com', 'Walmart', 'Michael', 'Williams', 5552345678, '789 Oak Street', 'Rivertown', 'TX', 67890),
('sbrown@gmail.com', 'Samsung', 'Sarah', 'Brown', 5553456789, '101 Pine Lane', 'Lakeside', 'FL', 34567),
('sydney.king@example.com', 'King Tech', 'Sydney', 'King', 5557890123, '404 Spruce St', 'Austin', 'TX', 78701),
('taylor.white@example.com', 'Whiteline', 'Taylor', 'White', 5553214567, '202 Birch Road', 'Topeka', 'KS', 66603),
('TEST11@gmail.com', 'EXAMPLE', 'TEST11', 'TEST11', 1234567890, '1234 ROAD ST', 'Fredericksburg', 'VA', 22401),
('TEST22@gmail.com', 'Amazon', 'TEST22', 'TEST22', 7535468970, '567 Baker ST', 'Dahlgren', 'VA', 22409),
('TEST@gmail.com', 'TEST', 'TEST', 'TEST', 1234567890, '1234 ROAD ST', 'Fredericksburg', 'VA', 22401);

-- --------------------------------------------------------

--
-- Table structure for table `dbusers`
--

CREATE TABLE `dbusers` (
  `id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `account_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbusers`
--

INSERT INTO `dbusers` (`id`, `email`, `password`, `first_name`, `last_name`, `account_type`, `role`) VALUES
('carolinebloom@bbbsfred.org', 'carolinebloom@bbbsfred.org', '$2y$10$oOoYwbRGJAHBxrN/3NZLKODtZ2rRw/tRFs8K8wQh/5trKUO9k05qa', 'Caroline', 'Bloom', 'user', 'Fund Development &amp; Marketing Assistant'),
('info@bbbsfred.org', 'info@bbbsfred.org', '$2y$10$.JcUwR7AKzB19WRJJxrV4OZC67rOFdxHu5w7l5cyer1jp5llcy2/2', 'BBBS', 'Fredericksburg', 'admin', 'Official Account'),
('madlynmastin@bbbsfred.org', 'madlynmastin@bbbsfred.org', '$2y$10$VQclb6T/SroZ1c2lJ23GxOtgNPMEXq/eAMwtqS4LRF3n3BLNgi56S', 'Madlyn', 'Mastin', 'user', 'Office Assistant'),
('sandraerickson@bbbsfred.org', 'sandraerickson@bbbsfred.org', '$2y$10$aadr6G/R9BincaaeG.rlxON9DcZIUjxBnbmBb3LZyMhdceR57PMkK', 'Sandra', 'Erickson', 'admin', 'Executive Director'),
('vmsroot', 'vmsroot', '$2y$10$OZkPCMyt.sF.pWueMf3fvO4t0XSw5.ZsTZbFYnNPLLPxf7RQwuqiu', 'vmsroot', 'vmsroot', 'super admin', 'Root User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbdonations`
--
ALTER TABLE `dbdonations`
  ADD PRIMARY KEY (`DonationID`),
  ADD KEY `Email` (`Email`);

--
-- Indexes for table `dbdonors`
--
ALTER TABLE `dbdonors`
  ADD PRIMARY KEY (`Email`);

--
-- Indexes for table `dbusers`
--
ALTER TABLE `dbusers`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
