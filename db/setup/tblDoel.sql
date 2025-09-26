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
-- Tabelstructuur voor tabel `tblDoel`
--

CREATE TABLE `tblDoel` (
  `doelId` int(11) NOT NULL,
  `doel` varchar(10) NOT NULL,
  `aanv` tinyint(1) DEFAULT 0 COMMENT 't.t.v. aanvoer',
  `ints` tinyint(1) DEFAULT 0 COMMENT 't.b.v. intesief'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Gegevens worden geëxporteerd voor tabel `tblDoel`
--

INSERT INTO `tblDoel` (`doelId`, `doel`, `aanv`, `ints`) VALUES
(1, 'Geboren', 1, 1),
(2, 'Gespeend', 0, 1),
(3, 'Stallijst', 1, 0);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `tblDoel`
--
ALTER TABLE `tblDoel`
  ADD PRIMARY KEY (`doelId`),
  ADD KEY `aanv` (`aanv`),
  ADD KEY `ints` (`ints`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `tblDoel`
--
ALTER TABLE `tblDoel`
  MODIFY `doelId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
