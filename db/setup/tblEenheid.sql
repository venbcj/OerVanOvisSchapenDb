truncate tblEenheid;
INSERT INTO `tblEenheid` (`eenhId`, `eenheid`) VALUES
(1, 'cc'),
(2, 'ltr'),
(3, 'kg'),
(4, 'stuks');
ALTER TABLE `tblEenheid`
  MODIFY `eenhId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
