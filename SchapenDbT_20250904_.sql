-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Gegenereerd op: 04 sep 2025 om 12:11
-- Serverversie: 10.6.15-MariaDB-cll-lve
-- PHP-versie: 8.4.7

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
-- Tabelstructuur voor tabel `impAgrident`
--

CREATE TABLE `impAgrident` (
  `actId` int(11) DEFAULT NULL,
  `datum` varchar(10) DEFAULT NULL,
  `ubnId` int(11) DEFAULT NULL,
  `transponder` varchar(23) DEFAULT NULL,
  `levensnummer` varchar(12) DEFAULT NULL,
  `nieuw_transponder` varchar(23) DEFAULT NULL,
  `nieuw_nummer` varchar(12) DEFAULT NULL,
  `verloop` varchar(20) DEFAULT NULL,
  `geboren` int(11) DEFAULT NULL,
  `levend` int(11) DEFAULT NULL,
  `ubn` int(11) DEFAULT NULL,
  `reden` int(11) DEFAULT NULL,
  `moedertransponder` varchar(23) DEFAULT NULL,
  `moeder` varchar(12) DEFAULT NULL,
  `vdrId` int(11) DEFAULT NULL,
  `gewicht` decimal(5,2) DEFAULT NULL,
  `hokId` int(11) DEFAULT NULL,
  `datumdier` varchar(10) DEFAULT NULL,
  `rasId` int(11) DEFAULT NULL,
  `geslacht` varchar(5) DEFAULT NULL,
  `leef_dgn` int(11) DEFAULT NULL,
  `momId` int(11) DEFAULT NULL,
  `artId` int(11) DEFAULT NULL,
  `toedat` decimal(5,2) DEFAULT NULL,
  `toedat_upd` decimal(5,2) DEFAULT NULL,
  `doelId` int(11) DEFAULT NULL,
  `kleur` varchar(6) DEFAULT NULL,
  `halsnr` int(11) DEFAULT NULL,
  `drachtig` tinyint(1) DEFAULT NULL,
  `grootte` tinyint(1) DEFAULT NULL,
  `verwerkt` tinyint(1) DEFAULT NULL,
  `Id` int(11) NOT NULL,
  `lidId` int(3) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `impReader`
--

CREATE TABLE `impReader` (
  `datum` varchar(10) DEFAULT NULL,
  `tijd` varchar(8) DEFAULT NULL,
  `levnr_geb` varchar(12) DEFAULT NULL,
  `teller` int(3) DEFAULT NULL,
  `rascode` varchar(5) DEFAULT NULL,
  `geslacht` varchar(3) DEFAULT NULL,
  `moeder` varchar(12) DEFAULT NULL,
  `hokcode` varchar(5) DEFAULT NULL,
  `gewicht` varchar(2) DEFAULT NULL,
  `col10` varchar(5) DEFAULT NULL COMMENT 'aantal dode dieren',
  `col11` varchar(10) DEFAULT NULL COMMENT 'uitval voor merken lam',
  `moment1` varchar(3) DEFAULT NULL COMMENT 'reden uitval voor merken 1',
  `col13` varchar(10) DEFAULT NULL COMMENT 'tijdelijk uitval voor merken zie Inlezenreader.php',
  `moment2` varchar(3) DEFAULT NULL COMMENT 'tijdelijk reden uitval voor merken zie Inlezenreader.php',
  `levnr_uitv` varchar(12) DEFAULT NULL,
  `teller_uitv` int(3) DEFAULT NULL,
  `reden_uitv` varchar(10) DEFAULT NULL,
  `levnr_afv` varchar(12) DEFAULT NULL,
  `teller_afv` int(3) DEFAULT NULL,
  `ubn_afv` varchar(10) DEFAULT NULL,
  `afvoerkg` decimal(5,2) DEFAULT NULL,
  `levnr_aanv` varchar(12) DEFAULT NULL,
  `teller_aanv` int(3) DEFAULT NULL,
  `ubn_aanv` varchar(10) DEFAULT NULL,
  `levnr_sp` varchar(12) DEFAULT NULL,
  `teller_sp` int(3) DEFAULT NULL,
  `hok_sp` int(8) DEFAULT NULL,
  `speenkg` decimal(5,2) DEFAULT NULL,
  `moeder_dr` varchar(12) DEFAULT NULL,
  `col30` int(11) DEFAULT NULL,
  `uitslag` varchar(25) DEFAULT NULL,
  `vader_dr` varchar(12) DEFAULT NULL,
  `levnr_ovpl` varchar(12) DEFAULT NULL,
  `teller_ovpl` int(3) DEFAULT NULL,
  `hok_ovpl` varchar(5) DEFAULT NULL,
  `reden_pil` int(3) DEFAULT NULL,
  `levnr_pil` varchar(12) DEFAULT NULL,
  `teller_pil` int(3) DEFAULT NULL,
  `col39` varchar(8) DEFAULT NULL COMMENT 'dit was aantal',
  `col40` int(3) DEFAULT NULL COMMENT 'dat was voerId',
  `col41` int(11) DEFAULT NULL,
  `weegkg` decimal(5,2) DEFAULT NULL,
  `levnr_weeg` varchar(12) DEFAULT NULL,
  `col44` int(11) DEFAULT NULL,
  `verwerkt` tinyint(1) DEFAULT NULL,
  `readId` int(11) NOT NULL,
  `lidId` int(3) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `impRespons`
--

CREATE TABLE `impRespons` (
  `reqId` int(11) DEFAULT NULL,
  `prod` varchar(3) DEFAULT NULL COMMENT 'Productie',
  `def` varchar(3) DEFAULT NULL COMMENT 'Vastleggen',
  `urvo` varchar(20) DEFAULT NULL COMMENT 'gebruikersnaam',
  `prvo` varchar(20) DEFAULT NULL COMMENT 'wachtwoord',
  `melding` varchar(3) DEFAULT NULL COMMENT 'Type_Melding',
  `relnr` int(20) DEFAULT NULL COMMENT 'relatienummerHouder',
  `ubn` int(12) DEFAULT NULL COMMENT 'meldingeenheid',
  `schaapdm` varchar(10) DEFAULT NULL COMMENT 'gebeurtenisdatum',
  `land` varchar(3) DEFAULT 'NL' COMMENT 'dierLandcode',
  `levensnummer` varchar(12) DEFAULT NULL COMMENT 'dierLevensnummer',
  `soort` varchar(2) DEFAULT '3' COMMENT 'dierSoort',
  `ubn_herk` int(12) DEFAULT NULL COMMENT 'meldingeenheidHerkomst',
  `land_new` varchar(3) DEFAULT NULL,
  `ubn_best` int(12) DEFAULT NULL COMMENT 'meldingeenheidBestemming',
  `levensnummer_new` varchar(12) DEFAULT NULL,
  `land_herk` varchar(3) DEFAULT 'NL' COMMENT 'dierHerkomstLandcode',
  `gebdm` varchar(10) DEFAULT NULL COMMENT 'geboortedatum',
  `sucind` varchar(1) DEFAULT NULL COMMENT 'succesIndicator',
  `foutind` varchar(1) DEFAULT NULL COMMENT 'soortFoutIndicator',
  `foutcode` varchar(10) DEFAULT NULL COMMENT 'foutcode',
  `foutmeld` varchar(2000) DEFAULT NULL COMMENT 'foutmelding',
  `meldnr` int(15) DEFAULT NULL,
  `respId` int(11) NOT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblAdres`
--

CREATE TABLE `tblAdres` (
  `adrId` int(11) NOT NULL,
  `relId` int(11) NOT NULL,
  `straat` varchar(40) DEFAULT NULL,
  `nr` varchar(5) DEFAULT NULL,
  `pc` varchar(10) DEFAULT NULL,
  `plaats` varchar(30) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT 1,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblAlert`
--

CREATE TABLE `tblAlert` (
  `Id` int(11) NOT NULL,
  `alert` varchar(23) NOT NULL,
  `actief` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblAlertselectie`
--

CREATE TABLE `tblAlertselectie` (
  `Id` int(11) NOT NULL,
  `volgnr` int(11) DEFAULT NULL,
  `lidId` int(11) NOT NULL,
  `transponder` varchar(23) NOT NULL,
  `alertId` int(11) NOT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblArtikel`
--

CREATE TABLE `tblArtikel` (
  `artId` int(11) NOT NULL,
  `soort` varchar(5) NOT NULL,
  `naam` varchar(50) NOT NULL,
  `stdat` decimal(5,2) DEFAULT NULL COMMENT 'standaard hoeveelheid',
  `enhuId` int(5) DEFAULT NULL,
  `perkg` decimal(5,2) DEFAULT NULL COMMENT 'per kg dier',
  `btw` int(11) DEFAULT NULL,
  `regnr` varchar(25) DEFAULT NULL COMMENT 'Registratienummer',
  `relId` int(11) DEFAULT NULL,
  `wdgn_v` int(2) DEFAULT NULL COMMENT 'wachtdagen vlees',
  `wdgn_m` int(2) DEFAULT NULL COMMENT 'wachtdagen melk',
  `rubuId` int(11) DEFAULT NULL,
  `actief` tinyint(1) NOT NULL DEFAULT 1,
  `naamreader` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblBezet`
--

CREATE TABLE `tblBezet` (
  `bezId` int(11) NOT NULL,
  `periId` int(11) DEFAULT NULL,
  `hisId` int(11) DEFAULT NULL,
  `hokId` int(11) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblCombireden`
--

CREATE TABLE `tblCombireden` (
  `comrId` int(11) NOT NULL,
  `tbl` varchar(4) NOT NULL,
  `artId` int(5) DEFAULT NULL,
  `stdat` decimal(5,2) DEFAULT NULL,
  `reduId` int(5) DEFAULT NULL,
  `scan` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDeklijst`
--

CREATE TABLE `tblDeklijst` (
  `dekId` int(11) NOT NULL,
  `lidId` int(11) NOT NULL,
  `dekat` int(11) DEFAULT NULL,
  `dmdek` date NOT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblDracht`
--

CREATE TABLE `tblDracht` (
  `draId` int(11) NOT NULL,
  `readId` int(11) DEFAULT NULL,
  `volwId` int(11) DEFAULT NULL,
  `hisId` int(11) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblEenheid`
--

CREATE TABLE `tblEenheid` (
  `eenhId` int(11) NOT NULL,
  `eenheid` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblEenheiduser`
--

CREATE TABLE `tblEenheiduser` (
  `enhuId` int(11) NOT NULL,
  `lidId` int(11) NOT NULL,
  `eenhId` int(11) NOT NULL,
  `actief` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblElement`
--

CREATE TABLE `tblElement` (
  `elemId` int(11) NOT NULL,
  `element` varchar(25) NOT NULL,
  `eenheid` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblElementuser`
--

CREATE TABLE `tblElementuser` (
  `elemuId` int(11) NOT NULL,
  `elemId` int(11) NOT NULL,
  `lidId` int(10) NOT NULL,
  `waarde` decimal(7,2) NOT NULL,
  `actief` tinyint(1) DEFAULT 1,
  `sal` tinyint(1) DEFAULT 1 COMMENT 'tbv saldoberekening'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblHistorie`
--

CREATE TABLE `tblHistorie` (
  `hisId` int(11) NOT NULL,
  `stalId` int(11) NOT NULL,
  `datum` date DEFAULT NULL,
  `actId` int(11) DEFAULT NULL,
  `kg` decimal(5,2) DEFAULT NULL,
  `reduId` int(11) DEFAULT NULL,
  `oud_nummer` varchar(12) DEFAULT NULL,
  `skip` tinyint(1) DEFAULT 0,
  `comment` varchar(250) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblHok`
--

CREATE TABLE `tblHok` (
  `hokId` int(11) NOT NULL,
  `hokIdc` int(11) DEFAULT NULL,
  `lidId` int(11) NOT NULL,
  `hoknr` varchar(25) NOT NULL,
  `scan` varchar(5) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT 1,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblInkoop`
--

CREATE TABLE `tblInkoop` (
  `inkId` int(11) NOT NULL,
  `dmink` date DEFAULT NULL,
  `artId` int(11) NOT NULL,
  `charge` varchar(25) DEFAULT NULL COMMENT 'chargenummer',
  `dmvval` date DEFAULT NULL COMMENT 'vervaldatum',
  `inkat` int(11) NOT NULL COMMENT 'Hoeveelheid',
  `enhuId` int(11) NOT NULL,
  `prijs` decimal(6,2) NOT NULL,
  `btw` int(11) DEFAULT NULL,
  `relId` int(11) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblLeden`
--

CREATE TABLE `tblLeden` (
  `lidId` int(11) NOT NULL,
  `alias` varchar(10) DEFAULT NULL COMMENT 'Toelichting in passw.php',
  `login` varchar(100) DEFAULT NULL,
  `passw` varchar(200) DEFAULT NULL,
  `roep` varchar(25) DEFAULT NULL,
  `voegsel` varchar(10) DEFAULT NULL,
  `naam` varchar(25) DEFAULT NULL,
  `relnr` int(20) DEFAULT NULL COMMENT 'relatienummer',
  `vbn` int(12) DEFAULT NULL COMMENT 'meldingeenheid',
  `urvo` varchar(20) DEFAULT NULL COMMENT 'username rvo',
  `prvo` varchar(20) DEFAULT NULL COMMENT 'password rvo',
  `tel` varchar(11) DEFAULT NULL,
  `mail` varchar(50) DEFAULT NULL,
  `kar_werknr` int(2) DEFAULT 5 COMMENT 'Aantal karakters werknr',
  `actief` tinyint(1) DEFAULT 1,
  `ingescand` date DEFAULT NULL,
  `prod` varchar(1) DEFAULT 'J' COMMENT 'Productieomgeving?',
  `beheer` tinyint(1) DEFAULT 0,
  `histo` tinyint(1) NOT NULL DEFAULT 1,
  `groei` tinyint(4) NOT NULL DEFAULT 1,
  `meld` tinyint(1) DEFAULT 1,
  `tech` tinyint(1) NOT NULL DEFAULT 1,
  `fin` tinyint(1) NOT NULL DEFAULT 0,
  `reader` varchar(20) DEFAULT NULL,
  `readerkey` varchar(64) DEFAULT NULL,
  `root_files` varchar(200) DEFAULT 'c:/domains/schapencentrummaasenwaal.nl/subdomeinen/ovis/wwwroot',
  `laatste_inlog` timestamp NULL DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblLiquiditeit`
--

CREATE TABLE `tblLiquiditeit` (
  `liqId` int(11) NOT NULL,
  `rubuId` int(11) NOT NULL,
  `datum` date DEFAULT NULL,
  `bedrag` decimal(6,2) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblMelding`
--

CREATE TABLE `tblMelding` (
  `meldId` int(11) NOT NULL,
  `reqId` int(11) DEFAULT NULL,
  `hisId` int(11) DEFAULT NULL,
  `meldnr` int(15) DEFAULT NULL,
  `skip` tinyint(1) DEFAULT 0 COMMENT 'niet gemeld',
  `fout` varchar(200) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblMoment`
--

CREATE TABLE `tblMoment` (
  `momId` int(11) NOT NULL,
  `moment` varchar(25) NOT NULL,
  `geb` tinyint(1) DEFAULT 0,
  `fok` tinyint(1) DEFAULT 0,
  `actief` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblMomentuser`
--

CREATE TABLE `tblMomentuser` (
  `momuId` int(11) NOT NULL,
  `lidId` int(11) NOT NULL,
  `momId` int(11) NOT NULL,
  `scan` varchar(4) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblNuttig`
--

CREATE TABLE `tblNuttig` (
  `nutId` int(11) NOT NULL,
  `hisId` int(11) DEFAULT NULL,
  `inkId` int(11) NOT NULL,
  `nutat` double(6,2) DEFAULT NULL COMMENT 'toedien aantal',
  `stdat` decimal(5,2) DEFAULT NULL COMMENT 'standaard verbruik',
  `reduId` int(11) DEFAULT NULL,
  `correctie` tinyint(1) DEFAULT 0,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblOpgaaf`
--

CREATE TABLE `tblOpgaaf` (
  `opgId` int(11) NOT NULL,
  `rubuId` varchar(25) NOT NULL,
  `datum` date DEFAULT NULL COMMENT 'tbv maand',
  `bedrag` decimal(8,2) DEFAULT NULL,
  `toel` varchar(50) DEFAULT NULL,
  `liq` tinyint(1) DEFAULT 1,
  `his` tinyint(1) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblPartij`
--

CREATE TABLE `tblPartij` (
  `partId` int(11) NOT NULL,
  `lidId` int(11) NOT NULL,
  `ubn` int(7) DEFAULT NULL,
  `naam` varchar(40) NOT NULL,
  `tel` varchar(15) DEFAULT NULL COMMENT 'algemeen',
  `fax` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `site` varchar(50) DEFAULT NULL,
  `banknr` varchar(30) DEFAULT NULL,
  `relnr` int(15) DEFAULT NULL,
  `wachtw` varchar(20) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT 1,
  `naamreader` varchar(20) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblPeriode`
--

CREATE TABLE `tblPeriode` (
  `periId` int(11) NOT NULL,
  `hokId` int(2) DEFAULT NULL,
  `doelId` int(11) DEFAULT NULL,
  `doel` varchar(8) DEFAULT NULL,
  `dmafsluit` date DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblPersoon`
--

CREATE TABLE `tblPersoon` (
  `persId` int(11) NOT NULL,
  `partId` int(11) NOT NULL,
  `geslacht` varchar(1) NOT NULL,
  `letter` varchar(10) DEFAULT NULL COMMENT 'Voorletter(s)',
  `roep` varchar(30) DEFAULT NULL COMMENT 'roepnaam',
  `voeg` varchar(10) DEFAULT NULL COMMENT 'tussenvoegsel',
  `naam` varchar(30) NOT NULL COMMENT 'achternaam',
  `tel` varchar(15) DEFAULT NULL,
  `gsm` varchar(11) DEFAULT NULL,
  `mail` varchar(50) DEFAULT NULL,
  `functie` varchar(30) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT 1,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblRasuser`
--

CREATE TABLE `tblRasuser` (
  `rasuId` int(11) NOT NULL,
  `lidId` int(11) NOT NULL,
  `rasId` int(11) NOT NULL,
  `scan` varchar(4) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblReden`
--

CREATE TABLE `tblReden` (
  `redId` int(11) NOT NULL,
  `reden` varchar(50) NOT NULL,
  `actief` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblRedenuser`
--

CREATE TABLE `tblRedenuser` (
  `reduId` int(11) NOT NULL,
  `redId` int(11) NOT NULL,
  `lidId` int(11) NOT NULL,
  `uitval` tinyint(1) NOT NULL DEFAULT 0,
  `pil` tinyint(1) NOT NULL DEFAULT 0,
  `afvoer` tinyint(1) NOT NULL DEFAULT 0,
  `sterfte` tinyint(1) NOT NULL DEFAULT 0,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblRelatie`
--

CREATE TABLE `tblRelatie` (
  `relId` int(11) NOT NULL,
  `partId` int(11) NOT NULL,
  `relatie` varchar(25) NOT NULL,
  `uitval` tinyint(1) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT 1,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblRequest`
--

CREATE TABLE `tblRequest` (
  `reqId` int(11) NOT NULL,
  `lidId_new` int(11) DEFAULT NULL,
  `code` varchar(3) DEFAULT NULL COMMENT 'Type_Melding',
  `def` varchar(1) DEFAULT 'N' COMMENT 'vastleggen',
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp(),
  `dmmeld` timestamp NULL DEFAULT NULL,
  `dmresponse` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblRubriekhfd`
--

CREATE TABLE `tblRubriekhfd` (
  `rubhId` int(11) NOT NULL,
  `rubriek` varchar(25) NOT NULL,
  `actief` tinyint(1) DEFAULT 1,
  `sort` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblRubriekuser`
--

CREATE TABLE `tblRubriekuser` (
  `rubuId` int(11) NOT NULL,
  `rubId` int(11) NOT NULL,
  `lidId` int(11) NOT NULL,
  `actief` tinyint(1) DEFAULT 1,
  `sal` tinyint(1) DEFAULT 1 COMMENT 'tbv saldoberekening'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblSalber`
--

CREATE TABLE `tblSalber` (
  `salbId` int(11) NOT NULL,
  `datum` date DEFAULT NULL,
  `tbl` varchar(2) DEFAULT NULL,
  `tblId` int(11) DEFAULT NULL,
  `aantal` decimal(7,2) DEFAULT NULL,
  `waarde` decimal(7,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblSchaap`
--

CREATE TABLE `tblSchaap` (
  `schaapId` int(11) NOT NULL,
  `levensnummer` varchar(12) DEFAULT NULL,
  `fokkernr` varchar(10) DEFAULT NULL,
  `rasId` int(11) DEFAULT NULL,
  `geslacht` varchar(5) DEFAULT NULL,
  `volwId` int(11) DEFAULT NULL,
  `indx` int(11) DEFAULT NULL,
  `momId` int(11) DEFAULT NULL,
  `redId` int(11) DEFAULT NULL,
  `transponder` varchar(23) DEFAULT NULL,
  `dmcreatie` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblStal`
--

CREATE TABLE `tblStal` (
  `stalId` int(11) NOT NULL,
  `lidId` int(7) DEFAULT NULL,
  `ubnId` int(11) DEFAULT NULL,
  `schaapId` int(11) NOT NULL,
  `kleur` varchar(6) DEFAULT NULL,
  `halsnr` int(11) DEFAULT NULL,
  `scan` varchar(5) DEFAULT NULL,
  `rel_herk` int(11) DEFAULT NULL,
  `rel_best` int(11) DEFAULT NULL,
  `dmcreatie` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblUbn`
--

CREATE TABLE `tblUbn` (
  `ubnId` int(11) NOT NULL,
  `lidId` int(11) NOT NULL,
  `ubn` int(12) NOT NULL,
  `adres` varchar(40) DEFAULT NULL,
  `plaats` varchar(30) DEFAULT NULL,
  `actief` tinyint(4) NOT NULL DEFAULT 1,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblVersiebeheer`
--

CREATE TABLE `tblVersiebeheer` (
  `Id` int(11) NOT NULL,
  `versieId` int(11) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `versie` varchar(10) NOT NULL,
  `bestand` varchar(80) NOT NULL,
  `app` varchar(25) DEFAULT NULL,
  `comment` varchar(250) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblVervoer`
--

CREATE TABLE `tblVervoer` (
  `vervId` int(11) NOT NULL,
  `partId` int(11) NOT NULL,
  `kenteken` varchar(10) DEFAULT NULL,
  `aanhanger` varchar(10) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblVoeding`
--

CREATE TABLE `tblVoeding` (
  `voedId` int(11) NOT NULL,
  `periId` int(11) DEFAULT NULL,
  `inkId` int(11) NOT NULL,
  `nutat` double(7,2) DEFAULT NULL COMMENT 'toedien aantal',
  `stdat` decimal(5,2) DEFAULT NULL COMMENT 'standaard verbruik',
  `datum` date DEFAULT NULL COMMENT 'Datum uit reader',
  `correctie` tinyint(1) DEFAULT 0,
  `readerId` int(11) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblVolwas`
--

CREATE TABLE `tblVolwas` (
  `volwId` int(11) NOT NULL,
  `readId` int(11) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `hisId` int(11) DEFAULT NULL,
  `mdrId` int(11) DEFAULT NULL,
  `vdrId` int(11) DEFAULT NULL,
  `drachtig` tinyint(1) DEFAULT NULL,
  `grootte` int(11) DEFAULT NULL,
  `verloop` varchar(20) DEFAULT NULL,
  `dmcreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `impAgrident`
--
ALTER TABLE `impAgrident`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `actId` (`actId`),
  ADD KEY `levensnummer` (`levensnummer`),
  ADD KEY `ubn` (`ubn`),
  ADD KEY `reden` (`reden`),
  ADD KEY `moeder` (`moeder`),
  ADD KEY `hokId` (`hokId`),
  ADD KEY `rasId` (`rasId`),
  ADD KEY `momId` (`momId`),
  ADD KEY `lidId` (`lidId`),
  ADD KEY `artId` (`artId`),
  ADD KEY `transponder` (`transponder`),
  ADD KEY `nieuw_transponder` (`nieuw_transponder`),
  ADD KEY `moedertransponder` (`moedertransponder`),
  ADD KEY `doelId` (`doelId`),
  ADD KEY `vdrId` (`vdrId`),
  ADD KEY `ubnId` (`ubnId`);

--
-- Indexen voor tabel `impReader`
--
ALTER TABLE `impReader`
  ADD PRIMARY KEY (`readId`),
  ADD KEY `lidId` (`lidId`),
  ADD KEY `levnr_geb` (`levnr_geb`),
  ADD KEY `rascode` (`rascode`),
  ADD KEY `moeder` (`moeder`),
  ADD KEY `hokcode` (`hokcode`);

--
-- Indexen voor tabel `impRespons`
--
ALTER TABLE `impRespons`
  ADD PRIMARY KEY (`respId`),
  ADD KEY `reqId` (`reqId`),
  ADD KEY `levensnummers` (`levensnummer`),
  ADD KEY `levensnummers_new` (`levensnummer_new`);

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
-- Indexen voor tabel `tblAdres`
--
ALTER TABLE `tblAdres`
  ADD PRIMARY KEY (`adrId`),
  ADD KEY `relId` (`relId`);

--
-- Indexen voor tabel `tblAlert`
--
ALTER TABLE `tblAlert`
  ADD PRIMARY KEY (`Id`);

--
-- Indexen voor tabel `tblAlertselectie`
--
ALTER TABLE `tblAlertselectie`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `volgnr` (`volgnr`),
  ADD KEY `lidId` (`lidId`);

--
-- Indexen voor tabel `tblArtikel`
--
ALTER TABLE `tblArtikel`
  ADD PRIMARY KEY (`artId`),
  ADD KEY `soort` (`soort`),
  ADD KEY `enhuId` (`enhuId`),
  ADD KEY `relId` (`relId`),
  ADD KEY `rubuId` (`rubuId`),
  ADD KEY `actief` (`actief`);

--
-- Indexen voor tabel `tblBezet`
--
ALTER TABLE `tblBezet`
  ADD PRIMARY KEY (`bezId`),
  ADD KEY `periId` (`periId`),
  ADD KEY `hisId` (`hisId`),
  ADD KEY `hokId` (`hokId`),
  ADD KEY `dmcreate` (`dmcreate`);

--
-- Indexen voor tabel `tblCombireden`
--
ALTER TABLE `tblCombireden`
  ADD PRIMARY KEY (`comrId`),
  ADD KEY `itemId` (`artId`),
  ADD KEY `reduId` (`reduId`),
  ADD KEY `item` (`tbl`);

--
-- Indexen voor tabel `tblDeklijst`
--
ALTER TABLE `tblDeklijst`
  ADD PRIMARY KEY (`dekId`),
  ADD KEY `lidId` (`lidId`),
  ADD KEY `dekat` (`dekat`),
  ADD KEY `dmcreate` (`dmcreate`);

--
-- Indexen voor tabel `tblDoel`
--
ALTER TABLE `tblDoel`
  ADD PRIMARY KEY (`doelId`),
  ADD KEY `aanv` (`aanv`),
  ADD KEY `ints` (`ints`);

--
-- Indexen voor tabel `tblDracht`
--
ALTER TABLE `tblDracht`
  ADD PRIMARY KEY (`draId`),
  ADD KEY `readId` (`readId`),
  ADD KEY `volwId` (`volwId`),
  ADD KEY `hisId` (`hisId`),
  ADD KEY `dmcreate` (`dmcreate`);

--
-- Indexen voor tabel `tblEenheid`
--
ALTER TABLE `tblEenheid`
  ADD PRIMARY KEY (`eenhId`);

--
-- Indexen voor tabel `tblEenheiduser`
--
ALTER TABLE `tblEenheiduser`
  ADD PRIMARY KEY (`enhuId`),
  ADD KEY `lidId` (`lidId`),
  ADD KEY `eenhId` (`eenhId`);

--
-- Indexen voor tabel `tblElement`
--
ALTER TABLE `tblElement`
  ADD PRIMARY KEY (`elemId`),
  ADD KEY `eenheid` (`eenheid`);

--
-- Indexen voor tabel `tblElementuser`
--
ALTER TABLE `tblElementuser`
  ADD PRIMARY KEY (`elemuId`),
  ADD KEY `elemId` (`elemId`),
  ADD KEY `lidId` (`lidId`);

--
-- Indexen voor tabel `tblHistorie`
--
ALTER TABLE `tblHistorie`
  ADD PRIMARY KEY (`hisId`),
  ADD KEY `stalId` (`stalId`),
  ADD KEY `datum` (`datum`),
  ADD KEY `actId` (`actId`),
  ADD KEY `skip` (`skip`),
  ADD KEY `reduId` (`reduId`),
  ADD KEY `dmcreate` (`dmcreate`);

--
-- Indexen voor tabel `tblHok`
--
ALTER TABLE `tblHok`
  ADD PRIMARY KEY (`hokId`),
  ADD KEY `lidId` (`lidId`),
  ADD KEY `scan` (`scan`);

--
-- Indexen voor tabel `tblInkoop`
--
ALTER TABLE `tblInkoop`
  ADD PRIMARY KEY (`inkId`),
  ADD KEY `artId` (`artId`),
  ADD KEY `enhuId` (`enhuId`),
  ADD KEY `relId` (`relId`),
  ADD KEY `dmcreate` (`dmcreate`);

--
-- Indexen voor tabel `tblLeden`
--
ALTER TABLE `tblLeden`
  ADD PRIMARY KEY (`lidId`),
  ADD KEY `ubn` (`vbn`),
  ADD KEY `readerkey` (`readerkey`),
  ADD KEY `roep` (`roep`,`voegsel`,`naam`),
  ADD KEY `reader` (`reader`),
  ADD KEY `ingescand` (`ingescand`),
  ADD KEY `groei` (`groei`);

--
-- Indexen voor tabel `tblLiquiditeit`
--
ALTER TABLE `tblLiquiditeit`
  ADD PRIMARY KEY (`liqId`),
  ADD KEY `rubuId` (`rubuId`),
  ADD KEY `datum` (`datum`);

--
-- Indexen voor tabel `tblMelding`
--
ALTER TABLE `tblMelding`
  ADD PRIMARY KEY (`meldId`),
  ADD KEY `reqId` (`reqId`),
  ADD KEY `hisId` (`hisId`),
  ADD KEY `dmcreate` (`dmcreate`);

--
-- Indexen voor tabel `tblMoment`
--
ALTER TABLE `tblMoment`
  ADD PRIMARY KEY (`momId`),
  ADD KEY `geb` (`geb`),
  ADD KEY `fok` (`fok`),
  ADD KEY `actief` (`actief`);

--
-- Indexen voor tabel `tblMomentuser`
--
ALTER TABLE `tblMomentuser`
  ADD PRIMARY KEY (`momuId`),
  ADD KEY `lidId` (`lidId`),
  ADD KEY `momId` (`momId`),
  ADD KEY `scan` (`scan`);

--
-- Indexen voor tabel `tblNuttig`
--
ALTER TABLE `tblNuttig`
  ADD PRIMARY KEY (`nutId`),
  ADD KEY `hisId` (`hisId`),
  ADD KEY `inkId` (`inkId`),
  ADD KEY `reduId` (`reduId`),
  ADD KEY `correctie` (`correctie`);

--
-- Indexen voor tabel `tblOpgaaf`
--
ALTER TABLE `tblOpgaaf`
  ADD PRIMARY KEY (`opgId`),
  ADD KEY `rubuId` (`rubuId`),
  ADD KEY `datum` (`datum`),
  ADD KEY `liq` (`liq`),
  ADD KEY `his` (`his`);

--
-- Indexen voor tabel `tblPartij`
--
ALTER TABLE `tblPartij`
  ADD PRIMARY KEY (`partId`),
  ADD KEY `lidId` (`lidId`),
  ADD KEY `ubn` (`ubn`),
  ADD KEY `naam` (`naam`);

--
-- Indexen voor tabel `tblPeriode`
--
ALTER TABLE `tblPeriode`
  ADD PRIMARY KEY (`periId`),
  ADD KEY `hokId` (`hokId`),
  ADD KEY `doel` (`doel`),
  ADD KEY `dmafsluit` (`dmafsluit`),
  ADD KEY `doelId` (`doelId`),
  ADD KEY `dmcreate` (`dmcreate`);

--
-- Indexen voor tabel `tblPersoon`
--
ALTER TABLE `tblPersoon`
  ADD PRIMARY KEY (`persId`),
  ADD KEY `partId` (`partId`);

--
-- Indexen voor tabel `tblRas`
--
ALTER TABLE `tblRas`
  ADD PRIMARY KEY (`rasId`),
  ADD KEY `eigen` (`eigen`);

--
-- Indexen voor tabel `tblRasuser`
--
ALTER TABLE `tblRasuser`
  ADD PRIMARY KEY (`rasuId`),
  ADD KEY `lidId` (`lidId`),
  ADD KEY `rasId` (`rasId`);

--
-- Indexen voor tabel `tblReden`
--
ALTER TABLE `tblReden`
  ADD PRIMARY KEY (`redId`);

--
-- Indexen voor tabel `tblRedenuser`
--
ALTER TABLE `tblRedenuser`
  ADD PRIMARY KEY (`reduId`),
  ADD KEY `redId` (`redId`),
  ADD KEY `lidId` (`lidId`);

--
-- Indexen voor tabel `tblRelatie`
--
ALTER TABLE `tblRelatie`
  ADD PRIMARY KEY (`relId`),
  ADD KEY `partId` (`partId`);

--
-- Indexen voor tabel `tblRequest`
--
ALTER TABLE `tblRequest`
  ADD PRIMARY KEY (`reqId`),
  ADD KEY `lidId_new` (`lidId_new`),
  ADD KEY `code` (`code`),
  ADD KEY `dmresponse` (`dmresponse`);

--
-- Indexen voor tabel `tblRubriek`
--
ALTER TABLE `tblRubriek`
  ADD PRIMARY KEY (`rubId`),
  ADD KEY `rubhId` (`rubhId`);

--
-- Indexen voor tabel `tblRubriekhfd`
--
ALTER TABLE `tblRubriekhfd`
  ADD PRIMARY KEY (`rubhId`);

--
-- Indexen voor tabel `tblRubriekuser`
--
ALTER TABLE `tblRubriekuser`
  ADD PRIMARY KEY (`rubuId`),
  ADD KEY `rubId` (`rubId`),
  ADD KEY `lidId` (`lidId`);

--
-- Indexen voor tabel `tblSalber`
--
ALTER TABLE `tblSalber`
  ADD PRIMARY KEY (`salbId`),
  ADD KEY `tbl` (`tbl`),
  ADD KEY `tblId` (`tblId`);

--
-- Indexen voor tabel `tblSchaap`
--
ALTER TABLE `tblSchaap`
  ADD PRIMARY KEY (`schaapId`),
  ADD KEY `levensnummer` (`levensnummer`),
  ADD KEY `rasId` (`rasId`),
  ADD KEY `volwId` (`volwId`),
  ADD KEY `momId` (`momId`),
  ADD KEY `redId` (`redId`),
  ADD KEY `dmcreatie` (`dmcreatie`),
  ADD KEY `transponder` (`transponder`);

--
-- Indexen voor tabel `tblStal`
--
ALTER TABLE `tblStal`
  ADD PRIMARY KEY (`stalId`),
  ADD KEY `lidId` (`lidId`),
  ADD KEY `schaapId` (`schaapId`),
  ADD KEY `rel_herk` (`rel_herk`),
  ADD KEY `rel_best` (`rel_best`),
  ADD KEY `dmcreatie` (`dmcreatie`),
  ADD KEY `scan` (`scan`);

--
-- Indexen voor tabel `tblUbn`
--
ALTER TABLE `tblUbn`
  ADD PRIMARY KEY (`ubnId`),
  ADD UNIQUE KEY `ubn` (`ubn`),
  ADD KEY `lidId` (`lidId`),
  ADD KEY `actief` (`actief`);

--
-- Indexen voor tabel `tblVersiebeheer`
--
ALTER TABLE `tblVersiebeheer`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `versieId` (`versieId`),
  ADD KEY `datum` (`datum`),
  ADD KEY `versie` (`versie`),
  ADD KEY `bestand` (`bestand`),
  ADD KEY `app` (`app`),
  ADD KEY `dmcreate` (`dmcreate`);

--
-- Indexen voor tabel `tblVervoer`
--
ALTER TABLE `tblVervoer`
  ADD PRIMARY KEY (`vervId`),
  ADD KEY `partId` (`partId`);

--
-- Indexen voor tabel `tblVoeding`
--
ALTER TABLE `tblVoeding`
  ADD PRIMARY KEY (`voedId`),
  ADD KEY `periId` (`periId`),
  ADD KEY `inkId` (`inkId`),
  ADD KEY `correctie` (`correctie`),
  ADD KEY `readerId` (`readerId`);

--
-- Indexen voor tabel `tblVolwas`
--
ALTER TABLE `tblVolwas`
  ADD PRIMARY KEY (`volwId`),
  ADD KEY `mdrId` (`mdrId`),
  ADD KEY `vdrId` (`vdrId`),
  ADD KEY `drachtig` (`drachtig`),
  ADD KEY `hisId` (`hisId`),
  ADD KEY `grootte` (`grootte`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `impAgrident`
--
ALTER TABLE `impAgrident`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `impReader`
--
ALTER TABLE `impReader`
  MODIFY `readId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `impRespons`
--
ALTER TABLE `impRespons`
  MODIFY `respId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblActie`
--
ALTER TABLE `tblActie`
  MODIFY `actId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblAdres`
--
ALTER TABLE `tblAdres`
  MODIFY `adrId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblAlert`
--
ALTER TABLE `tblAlert`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblAlertselectie`
--
ALTER TABLE `tblAlertselectie`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblArtikel`
--
ALTER TABLE `tblArtikel`
  MODIFY `artId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblBezet`
--
ALTER TABLE `tblBezet`
  MODIFY `bezId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblCombireden`
--
ALTER TABLE `tblCombireden`
  MODIFY `comrId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblDeklijst`
--
ALTER TABLE `tblDeklijst`
  MODIFY `dekId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblDoel`
--
ALTER TABLE `tblDoel`
  MODIFY `doelId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblDracht`
--
ALTER TABLE `tblDracht`
  MODIFY `draId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblEenheid`
--
ALTER TABLE `tblEenheid`
  MODIFY `eenhId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblEenheiduser`
--
ALTER TABLE `tblEenheiduser`
  MODIFY `enhuId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblElement`
--
ALTER TABLE `tblElement`
  MODIFY `elemId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblElementuser`
--
ALTER TABLE `tblElementuser`
  MODIFY `elemuId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblHistorie`
--
ALTER TABLE `tblHistorie`
  MODIFY `hisId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblHok`
--
ALTER TABLE `tblHok`
  MODIFY `hokId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblInkoop`
--
ALTER TABLE `tblInkoop`
  MODIFY `inkId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblLeden`
--
ALTER TABLE `tblLeden`
  MODIFY `lidId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblLiquiditeit`
--
ALTER TABLE `tblLiquiditeit`
  MODIFY `liqId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblMelding`
--
ALTER TABLE `tblMelding`
  MODIFY `meldId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblMoment`
--
ALTER TABLE `tblMoment`
  MODIFY `momId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblMomentuser`
--
ALTER TABLE `tblMomentuser`
  MODIFY `momuId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblNuttig`
--
ALTER TABLE `tblNuttig`
  MODIFY `nutId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblOpgaaf`
--
ALTER TABLE `tblOpgaaf`
  MODIFY `opgId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblPartij`
--
ALTER TABLE `tblPartij`
  MODIFY `partId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblPeriode`
--
ALTER TABLE `tblPeriode`
  MODIFY `periId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblPersoon`
--
ALTER TABLE `tblPersoon`
  MODIFY `persId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblRas`
--
ALTER TABLE `tblRas`
  MODIFY `rasId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblRasuser`
--
ALTER TABLE `tblRasuser`
  MODIFY `rasuId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblReden`
--
ALTER TABLE `tblReden`
  MODIFY `redId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblRedenuser`
--
ALTER TABLE `tblRedenuser`
  MODIFY `reduId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblRelatie`
--
ALTER TABLE `tblRelatie`
  MODIFY `relId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblRequest`
--
ALTER TABLE `tblRequest`
  MODIFY `reqId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblRubriek`
--
ALTER TABLE `tblRubriek`
  MODIFY `rubId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblRubriekhfd`
--
ALTER TABLE `tblRubriekhfd`
  MODIFY `rubhId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblRubriekuser`
--
ALTER TABLE `tblRubriekuser`
  MODIFY `rubuId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblSalber`
--
ALTER TABLE `tblSalber`
  MODIFY `salbId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblSchaap`
--
ALTER TABLE `tblSchaap`
  MODIFY `schaapId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblStal`
--
ALTER TABLE `tblStal`
  MODIFY `stalId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblUbn`
--
ALTER TABLE `tblUbn`
  MODIFY `ubnId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblVersiebeheer`
--
ALTER TABLE `tblVersiebeheer`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblVervoer`
--
ALTER TABLE `tblVervoer`
  MODIFY `vervId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblVoeding`
--
ALTER TABLE `tblVoeding`
  MODIFY `voedId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tblVolwas`
--
ALTER TABLE `tblVolwas`
  MODIFY `volwId` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
