truncate tblActie;
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
ALTER TABLE `tblActie`
  MODIFY `actId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
