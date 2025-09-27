truncate tblDoel;
INSERT INTO `tblDoel` (`doelId`, `doel`, `aanv`, `ints`) VALUES
(1, 'Geboren', 1, 1),
(2, 'Gespeend', 0, 1),
(3, 'Stallijst', 1, 0);

ALTER TABLE `tblDoel`
  MODIFY `doelId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;
