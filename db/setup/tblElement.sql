truncate tblElement;
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
ALTER TABLE `tblElement`
  MODIFY `elemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
