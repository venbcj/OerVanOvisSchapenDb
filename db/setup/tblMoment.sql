truncate tblMoment;
INSERT INTO `tblMoment` (`momId`, `moment`, `geb`, `fok`, `actief`) VALUES
(1, 'dood geboren', 1, 1, 1),
(2, 'onvolledig dood geboren', 1, 1, 1);
ALTER TABLE `tblMoment`
  MODIFY `momId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
