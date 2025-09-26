-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Gegenereerd op: 26 sep 2025 om 14:56
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
-- Tabelstructuur voor tabel `tblRubriek`
--

CREATE TABLE `tblRubriek` (
  `rubId` int(11) NOT NULL,
  `rubhId` int(11) NOT NULL,
  `rubriek` varchar(25) DEFAULT NULL,
  `credeb` varchar(1) NOT NULL,
  `actief` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Gegevens worden geëxporteerd voor tabel `tblRubriek`
--

INSERT INTO `tblRubriek` (`rubId`, `rubhId`, `rubriek`, `credeb`, `actief`) VALUES
(1, 2, 'Aankoop schapen', 'c', 1),
(2, 1, 'Accountant kosten', 'c', 1),
(3, 4, 'Aflossing lening', 'c', 1),
(4, 1, 'Auto kosten', 'c', 1),
(5, 1, 'Automatiserings kosten', 'c', 1),
(6, 5, 'Beheersvergoeding', 'd', 1),
(7, 1, 'Brandstof auto', 'c', 1),
(8, 1, 'Brandstof tractor', 'c', 1),
(9, 1, 'Contributi / abonnementen', 'c', 1),
(10, 2, 'Gezondheidskosten', 'c', 1),
(11, 2, 'Heffingen (pvv)', 'c', 1),
(12, 2, 'Huur roerende zaken', 'c', 1),
(13, 2, 'I&R', 'c', 1),
(14, 1, 'Kantoorkosten', 'c', 1),
(15, 6, 'Kr.voer vervanger vl.lam', 'c', 1),
(16, 6, 'Kr.voer vervanger mdrdier', 'c', 1),
(17, 6, 'Kunstmelk', 'c', 1),
(18, 2, 'Loonwerk kosten', 'c', 1),
(19, 6, 'Mineralen', 'c', 1),
(20, 1, 'Onderh machine/werktuigen', 'c', 1),
(21, 1, 'Onderhoud en klein materi', 'c', 1),
(22, 1, 'Onderhoud gebouwen', 'c', 1),
(23, 3, 'Overige financ kosten', 'c', 1),
(24, 5, 'Overige opbrengsten', 'd', 1),
(25, 2, 'Pacht grasland', 'c', 1),
(26, 1, 'Polder waterschaps lasten', 'c', 1),
(27, 4, 'Prive ontrekkingen', 'c', 1),
(28, 4, 'Prive stortingen', 'c', 1),
(29, 3, 'Provici / bank kosten', 'c', 1),
(30, 3, 'Rente hypotheek ( totaal ', 'c', 1),
(31, 3, 'Rente rekening courant', 'c', 1),
(32, 2, 'Strooisel', 'c', 1),
(33, 1, 'Telefoon/internet kosten', 'c', 1),
(34, 5, 'Toeslagen,vergoed,subs', 'd', 1),
(35, 5, 'Toeslagrechten', 'd', 1),
(36, 2, 'Transportkosten', 'c', 1),
(37, 5, 'Vergoeding mest', 'd', 1),
(38, 5, 'Verkoop fokdieren', 'd', 1),
(39, 5, 'Verkoop lammeren', 'd', 1),
(40, 5, 'Verkoop moederdier', 'd', 1),
(41, 1, 'Verzekering gebouwen', 'c', 1),
(42, 1, 'Verzekeringen algemeen', 'c', 1),
(43, 6, 'Voer opfoklam', 'c', 1),
(44, 6, 'Voer moederdier', 'c', 1),
(45, 5, 'Werken voor derde', 'd', 1),
(46, 5, 'Wol', 'd', 1),
(47, 1, 'Overige kosten', 'c', 1),
(48, 6, 'Voer vleeslam', 'c', 1),
(49, 2, 'Destructie', 'c', 1),
(50, 2, 'Scheren', 'c', 1),
(51, 2, 'Aankoop vaderdieren', 'c', 1),
(52, 1, 'Gereedschap', 'c', 1),
(53, 2, 'Onderhoud percelen', 'c', 1),
(54, 1, 'Investering', 'c', 1),
(55, 1, 'Voorraad voor handel', 'c', 1);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `tblRubriek`
--
ALTER TABLE `tblRubriek`
  ADD PRIMARY KEY (`rubId`),
  ADD KEY `rubhId` (`rubhId`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `tblRubriek`
--
ALTER TABLE `tblRubriek`
  MODIFY `rubId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
