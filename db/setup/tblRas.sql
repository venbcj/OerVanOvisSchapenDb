-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Gegenereerd op: 26 sep 2025 om 14:55
-- Serverversie: 10.6.15-MariaDB-cll-lve
-- PHP-versie: 8.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `k36098_bvdvSchapenDbT`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblRas`
--

CREATE TABLE `tblRas` (
  `rasId` int(11) NOT NULL,
  `ras` varchar(50) NOT NULL,
  `actief` tinyint(1) DEFAULT 1,
  `eigen` tinyint(1) NOT NULL DEFAULT 0,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Gegevens worden geëxporteerd voor tabel `tblRas`
--

INSERT INTO `tblRas` (`rasId`, `ras`, `actief`, `eigen`, `dmcreate`) VALUES
(1, 'NL bont', 1, 0, '2021-06-16 14:43:15'),
(2, 'Ardense voskop', 1, 0, '2021-06-16 14:43:15'),
(3, 'Assaf', 1, 0, '2021-06-16 14:43:15'),
(4, 'Awassi', 1, 0, '2021-06-16 14:43:15'),
(5, 'Barbados blackb', 1, 0, '2021-06-16 14:43:15'),
(6, 'Belgisch melksc', 1, 0, '2021-06-16 14:43:15'),
(7, 'Beltex', 1, 0, '2021-06-16 14:43:15'),
(8, 'Bentheimer land', 1, 0, '2021-06-16 14:43:15'),
(9, 'Black Welsh mou', 1, 0, '2021-06-16 14:43:15'),
(10, 'Blauwe Texelaar', 1, 0, '2021-06-16 14:43:15'),
(11, 'Bleu du Maine', 1, 0, '2021-06-16 14:43:15'),
(12, 'Border Leiceste', 1, 0, '2021-06-16 14:43:15'),
(13, 'Boulonnais', 1, 0, '2021-06-16 14:43:15'),
(14, 'Cambridge', 1, 0, '2021-06-16 14:43:15'),
(15, 'Castlemilk moor', 1, 0, '2021-06-16 14:43:15'),
(16, 'Charollais', 1, 0, '2021-06-16 14:43:15'),
(17, 'Cheviot', 1, 0, '2021-06-16 14:43:15'),
(18, 'Clun Forest', 1, 0, '2021-06-16 14:43:15'),
(19, 'Coburger fuchs', 1, 0, '2021-06-16 14:43:15'),
(20, 'Cotswold', 1, 0, '2021-06-16 14:43:15'),
(21, 'Drents heidesch', 1, 0, '2021-06-16 14:43:15'),
(22, 'Dassenkop', 1, 0, '2021-06-16 14:43:15'),
(23, 'Devon & Cornwal', 1, 0, '2021-06-16 14:43:15'),
(24, 'Dormer', 1, 0, '2021-06-16 14:43:15'),
(25, 'Dorper', 1, 0, '2021-06-16 14:43:15'),
(26, 'Dorset', 1, 0, '2021-06-16 14:43:15'),
(27, 'Drents heidesch', NULL, 0, '2021-06-16 14:43:15'),
(28, 'Duitse witkop', 1, 0, '2021-06-16 14:43:15'),
(29, 'Duitse zwartkop', 1, 0, '2021-06-16 14:43:15'),
(30, 'Entre-Sambre-et', 1, 0, '2021-06-16 14:43:15'),
(31, 'Exmoor horn', 1, 0, '2021-06-16 14:43:15'),
(32, 'Fins landschaap', 1, 0, '2021-06-16 14:43:15'),
(33, 'Flevolander', 1, 0, '2021-06-16 14:43:15'),
(34, 'Fries melkschaa', 1, 0, '2021-06-16 14:43:15'),
(35, 'Gotlandpelsscha', 1, 0, '2021-06-16 14:43:15'),
(36, 'GuteFar', 1, 0, '2021-06-16 14:43:15'),
(37, 'Hampshire down', 1, 0, '2021-06-16 14:43:15'),
(38, 'Hebridean', 1, 0, '2021-06-16 14:43:15'),
(39, 'Heidschnucke', 1, 0, '2021-06-16 14:43:15'),
(40, 'Herdwick', 1, 0, '2021-06-16 14:43:15'),
(41, 'Houtlandschaap', 1, 0, '2021-06-16 14:43:15'),
(42, 'Ile de France', 1, 0, '2021-06-16 14:43:15'),
(43, 'IJslander', 1, 0, '2021-06-16 14:43:15'),
(44, 'Jacobsschaap', 1, 0, '2021-06-16 14:43:15'),
(45, 'Katahdin', 1, 0, '2021-06-16 14:43:15'),
(46, 'Kameroenschaap', 1, 0, '2021-06-16 14:43:15'),
(47, 'Karakoelschaap', 1, 0, '2021-06-16 14:43:15'),
(48, 'Karntner brilsc', 1, 0, '2021-06-16 14:43:15'),
(49, 'Kempens schaap', 1, 0, '2021-06-16 14:43:15'),
(50, 'Kerry hill', 1, 0, '2021-06-16 14:43:15'),
(51, 'Lacauneschaap', 1, 0, '2021-06-16 14:43:15'),
(52, 'Lakens schaap', 1, 0, '2021-06-16 14:43:15'),
(53, 'Leicester Longw', 1, 0, '2021-06-16 14:43:15'),
(54, 'Lincoln', 1, 0, '2021-06-16 14:43:15'),
(55, 'Lleyn', 1, 0, '2021-06-16 14:43:15'),
(56, 'Lovenaar', 1, 0, '2021-06-16 14:43:15'),
(57, 'Manx Loghtan', 1, 0, '2021-06-16 14:43:15'),
(58, 'Meatlinc', 1, 0, '2021-06-16 14:43:15'),
(59, 'Mergellandschaa', 1, 0, '2021-06-16 14:43:15'),
(60, 'Merino', 1, 0, '2021-06-16 14:43:15'),
(61, 'Moeflon', 1, 0, '2021-06-16 14:43:15'),
(62, 'Montadale', 1, 0, '2021-06-16 14:43:15'),
(63, 'Navajo churro', 1, 0, '2021-06-16 14:43:15'),
(64, 'Noordhollander', 1, 0, '2021-06-16 14:43:15'),
(65, 'Nolana', 1, 0, '2021-06-16 14:43:15'),
(66, 'Norfolk horn', 1, 0, '2021-06-16 14:43:15'),
(67, 'Norsk Spelsau', 1, 0, '2021-06-16 14:43:15'),
(68, 'North Ronaldsay', 1, 0, '2021-06-16 14:43:15'),
(69, 'OuessantschaapO', 1, 0, '2021-06-16 14:43:15'),
(70, 'Ouessantschaap', 1, 0, '2021-06-16 14:43:15'),
(71, 'Persian Blackhe', 1, 0, '2021-06-16 14:43:15'),
(72, 'Poll Dorset', 1, 0, '2021-06-16 14:43:15'),
(73, 'Polwarth', 1, 0, '2021-06-16 14:43:15'),
(74, 'Portland', 1, 0, '2021-06-16 14:43:15'),
(75, 'Rackaschaap', 1, 0, '2021-06-16 14:43:15'),
(76, 'Rambouillet', 1, 0, '2021-06-16 14:43:15'),
(77, 'Rijnlam', 1, 0, '2021-06-16 14:43:15'),
(78, 'Romanov', 1, 0, '2021-06-16 14:43:15'),
(79, 'Romney', 1, 0, '2021-06-16 14:43:15'),
(80, 'Rouge de l Oues', 1, 0, '2021-06-16 14:43:15'),
(81, 'Ryeland', 1, 0, '2021-06-16 14:43:15'),
(82, 'Saeftingher', 1, 0, '2021-06-16 14:43:15'),
(83, 'Schoonebeker he', 1, 0, '2021-06-16 14:43:15'),
(84, 'Scottish blackf', 1, 0, '2021-06-16 14:43:15'),
(85, 'Shetlandschaap', 1, 0, '2021-06-16 14:43:15'),
(86, 'Shropshire', 1, 0, '2021-06-16 14:43:15'),
(87, 'Skudde', 1, 0, '2021-06-16 14:43:15'),
(88, 'Soay', 1, 0, '2021-06-16 14:43:15'),
(89, 'Solognote', 1, 0, '2021-06-16 14:43:15'),
(90, 'Southdown', 1, 0, '2021-06-16 14:43:15'),
(91, 'Suffolk', 1, 0, '2021-06-16 14:43:15'),
(92, 'Swifter', 1, 0, '2021-06-16 14:43:15'),
(93, 'Texelaar', 1, 0, '2021-06-16 14:43:15'),
(94, 'Tiroler steensc', 1, 0, '2021-06-16 14:43:15'),
(95, 'Veluws heidesch', 1, 0, '2021-06-16 14:43:15'),
(96, 'Vetstaart', 1, 0, '2021-06-16 14:43:15'),
(97, 'Vlaams kuddesch', 1, 0, '2021-06-16 14:43:15'),
(98, 'Vlaams schaap', 1, 0, '2021-06-16 14:43:15'),
(99, 'Walliser Schwar', 1, 0, '2021-06-16 14:43:15'),
(100, 'Wensleydale', 1, 0, '2021-06-16 14:43:15'),
(101, 'Wiltipoll', 1, 0, '2021-06-16 14:43:15'),
(102, 'Wiltshire horn', 1, 0, '2021-06-16 14:43:15'),
(103, 'Zeeuws melkscha', 1, 0, '2021-06-16 14:43:15'),
(104, 'Zwartbles', 1, 0, '2021-06-16 14:43:15'),
(105, '50%RL50%Sch', 1, 1, '2021-06-17 07:06:10'),
(106, '75%RL25%Sch', 1, 1, '2021-06-17 07:06:56'),
(107, '50%RL25%NH25%ov', 1, 1, '2021-06-17 07:07:36'),
(108, '50%NH50%OV', 1, 1, '2022-02-16 18:04:41'),
(109, '75%NH25OV', 1, 1, '2022-02-18 11:12:56'),
(110, '75%NH25%OV', 1, 1, '2022-02-18 11:13:41'),
(111, '50%RL 50% OV', 1, 1, '2022-02-20 13:41:47'),
(112, '75%PollDor25%IlledeFr', 1, 1, '2022-03-31 08:39:55'),
(113, '50%RL25%FL25%POLL', 1, 1, '2022-11-08 15:46:53'),
(114, '50%RL50%FL', 1, 1, '2022-11-08 15:47:14'),
(115, '50%RL50%POLL', 1, 1, '2022-11-08 15:49:19'),
(116, '50%POLL25%FL25%RL', 1, 1, '2022-11-16 08:05:58'),
(117, '38%Pol12%iL25%RL25%SCH', 1, 1, '2022-12-27 10:21:07'),
(118, '38%PoL12%IL38%RL12%Sch', 1, 1, '2022-12-27 10:22:05'),
(119, '38%PoL12%IL25%RL12%NH12%OV', 1, 1, '2022-12-27 10:25:31'),
(120, '38%Pol12%IL25%SuF25%Sch', 1, 1, '2022-12-27 10:26:21'),
(121, '50%RL25%SU25%SCH', 1, 1, '2023-01-08 18:55:29'),
(122, '50%RL25%SU25%CH', 1, 1, '2023-01-10 19:28:47'),
(123, '50%SU50%sch', 1, 1, '2023-01-18 09:47:01'),
(124, '50%CH50%SCH', 1, 1, '2023-01-18 09:47:22'),
(125, '??', 1, 1, '2023-01-18 10:20:49'),
(126, 'Romane', 1, 1, '2023-01-27 12:03:12'),
(127, '75%RL 12,5%P 12,5% FL', 1, 1, '2023-05-12 08:30:51'),
(128, '75%RL 25%FL', 1, 1, '2023-05-12 08:31:05'),
(129, '75%RL 25%Poll', 1, 1, '2023-05-12 08:31:23'),
(130, 'NHxCH', 1, 1, '2023-05-12 08:31:38'),
(131, '50%CH', 1, 1, '2023-05-12 08:32:07'),
(132, '75%CH', 1, 1, '2023-05-12 08:32:12'),
(133, '50% CH 25% NH', 1, 1, '2023-05-14 18:22:59'),
(134, '50%RL 38%Poll 12%IL', 1, 1, '2023-05-28 18:22:55'),
(135, '38%Poll 12%ill 25%NH', 1, 1, '2023-06-21 18:04:11'),
(136, 'Zoekram', 1, 1, '2023-10-06 13:16:33'),
(137, '87,5%RL 12,5%Sch', 1, 1, '2024-01-11 19:46:57'),
(138, '75%RL 12,5NH 12,5OV', 1, 1, '2024-01-11 19:47:17'),
(139, '50%CH 25%RL 25%Sch', 1, 1, '2024-01-11 20:03:55'),
(140, '50%RL 50%CH', 1, 1, '2024-01-31 19:51:26');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `tblRas`
--
ALTER TABLE `tblRas`
  ADD PRIMARY KEY (`rasId`),
  ADD KEY `eigen` (`eigen`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `tblRas`
--
ALTER TABLE `tblRas`
  MODIFY `rasId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
