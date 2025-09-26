-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Gegenereerd op: 26 sep 2025 om 14:54
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
-- Tabelstructuur voor tabel `tblElement`
--

CREATE TABLE `tblElement` (
  `elemId` int(11) NOT NULL,
  `element` varchar(25) NOT NULL,
  `eenheid` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Gegevens worden geëxporteerd voor tabel `tblElement`
--

INSERT INTO `tblElement` (`elemId`, `element`, `eenheid`) VALUES
(1, 'Aantal ooien', 'getal'),
(2, 'Destructie', 'euro'),
(3, 'Heffingen', 'euro'),
(4, 'Huur stal', 'euro'),
(5, 'Loonwerk', 'euro'),
(6, 'Medicatie', 'euro'),
(7, 'Overige', 'euro'),
(8, 'Pacht grasland', 'euro'),
(9, 'Percentage gedekte ooien', 'procent'),
(10, 'Prijs per lam', 'euro'),
(11, 'Scheren', 'euro'),
(12, 'Sterfte lammeren', 'procent'),
(13, 'Sterfte percentage ooien', 'procent'),
(14, 'Strooisel', 'euro'),
(15, 'Transportkosten', 'euro'),
(16, 'Vervanging percentage ooi', 'procent'),
(17, 'Voer', 'euro'),
(18, 'Worpen per jaar', 'getal'),
(19, 'Worpgrootte', 'getal');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `tblElement`
--
ALTER TABLE `tblElement`
  ADD PRIMARY KEY (`elemId`),
  ADD KEY `eenheid` (`eenheid`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `tblElement`
--
ALTER TABLE `tblElement`
  MODIFY `elemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
