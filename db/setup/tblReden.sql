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
-- Tabelstructuur voor tabel `tblReden`
--

CREATE TABLE `tblReden` (
  `redId` int(11) NOT NULL,
  `reden` varchar(50) NOT NULL,
  `actief` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Gegevens worden geëxporteerd voor tabel `tblReden`
--

INSERT INTO `tblReden` (`redId`, `reden`, `actief`) VALUES
(1, 'Baarmoederontsteking', 1),
(2, 'Bloedziekte', 1),
(3, 'Coli', 1),
(4, 'Dood gelegen', 1),
(5, 'Euthanasie', 1),
(6, 'Hersenvliesontsteking', 1),
(7, 'Inwendige bloeding', 1),
(8, 'Klem gezeten', 1),
(9, 'Longontsteking', 1),
(10, 'Melkvergiftiging', 1),
(11, 'Melkziekte', 1),
(12, 'Miase', 1),
(13, 'Onbekend', 1),
(14, 'Ouderdom', 1),
(15, 'Prolaps', 1),
(16, 'Scheenziekte', 1),
(17, 'Schmallenbergvirus', 1),
(18, 'Tetanus', 1),
(19, 'Verdronken', 1),
(20, 'Verworpen', 1),
(21, 'Vocht', 1),
(22, 'Zwak', 1),
(23, 'Nvt bij dood geboren', 0),
(24, 'Worm infecttie', 1),
(25, 'Niersteen', 1),
(26, '1e lam enting', 1),
(27, '2e lam enting', 1),
(28, 'Winter kou', 1),
(29, 'Leverbot', 1),
(30, 'Schurft/Luis', 1),
(31, 'Gewrichtsontsteking', 1),
(32, 'Uierontsteking', 1),
(33, 'Ontwormen na lammeren', 1),
(34, 'Coccidiose', 1),
(35, 'Ontwormen', 1),
(36, 'Kreupelheid', 1),
(37, 'Pens verzuring', 1),
(38, 'Pijn verlichting ooi', 1),
(39, 'Pijn verlichting lam', 1),
(40, 'Luchtwegen', 1),
(41, 'Clostridium ', 1),
(42, 'In het vlies', 1),
(43, 'Misvormd', 1),
(44, 'Verkeerde ligging', 1),
(45, 'Slechte uier', 1),
(46, 'Slacht ooi', 1),
(47, 'Weinig melk', 1),
(48, 'Verwerper', 1),
(49, 'Gust', 1),
(50, 'Slacht lam', 1),
(51, 'Weide lam', 1),
(52, 'Blauwtong', 1);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `tblReden`
--
ALTER TABLE `tblReden`
  ADD PRIMARY KEY (`redId`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `tblReden`
--
ALTER TABLE `tblReden`
  MODIFY `redId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
