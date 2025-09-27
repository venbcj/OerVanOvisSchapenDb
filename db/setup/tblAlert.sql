truncate tblAlert;
INSERT INTO `tblAlert` (`Id`, `alert`, `actief`) VALUES
(1, 'uit worp van 1', 1),
(2, 'uit worp van 2', 1),
(3, 'uit worp van 3', 1),
(4, 'uit worp van 4', 1),
(5, 'uit worp van 5', 1),
(6, 'uit worp van 6', 1);

ALTER TABLE `tblAlert`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
