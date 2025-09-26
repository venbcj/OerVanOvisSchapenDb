-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Gegenereerd op: 26 sep 2025 om 13:28
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
-- Tabelstructuur voor tabel `tblActie`
--

CREATE TABLE `tblActie` (
  `actId` int(11) NOT NULL,
  `actie` varchar(20) NOT NULL,
  `op` tinyint(1) DEFAULT 0,
  `af` tinyint(1) DEFAULT 0,
  `aan` tinyint(1) DEFAULT 0,
  `uit` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Gegevens worden geëxporteerd voor tabel `tblActie`
--

INSERT INTO `tblActie` (`actId`, `actie`, `op`, `af`, `aan`, `uit`) VALUES
(1, 'Geboren', 1, 0, 1, 0),
(2, 'Aangekocht', 1, 0, 1, 0),
(3, 'Aanwas', 0, 0, 0, 0),
(4, 'Gespeend', 0, 0, 1, 1),
(5, 'Overgeplaatst', 0, 0, 1, 1),
(6, 'Geplaatst', 0, 0, 1, 0),
(7, 'Verlaten', 0, 0, 0, 1),
(8, 'Medicatie', 0, 0, 0, 0),
(9, 'Gewogen', 0, 0, 0, 0),
(10, 'Uitgeschaard', 0, 1, 0, 1),
(11, 'Terug van uitscharen', 1, 0, 1, 0),
(12, 'Afgeleverd', 0, 1, 0, 1),
(13, 'Verkocht', 0, 1, 0, 1),
(14, 'Overleden', 0, 1, 0, 1),
(15, 'Geadopteerd', 0, 0, 0, 0),
(16, 'Naar Lambar', 0, 0, 1, 1),
(17, 'Omgenummerd', 0, 0, 0, 0),
(18, 'Gedekt', 0, 0, 0, 0),
(19, 'Drachtig', 0, 0, 0, 0),
(20, 'Vermist', 0, 1, 0, 1),
(21, 'Stallijst ingelezen', 0, 0, 1, 0),
(22, 'Stallijstcontrole', 0, 0, 0, 0);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `tblActie`
--
ALTER TABLE `tblActie`
  ADD PRIMARY KEY (`actId`),
  ADD KEY `op` (`op`),
  ADD KEY `af` (`af`),
  ADD KEY `aan` (`aan`),
  ADD KEY `uit` (`uit`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `tblActie`
--
ALTER TABLE `tblActie`
  MODIFY `actId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
