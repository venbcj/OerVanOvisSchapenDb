INSERT INTO tblArtikel(artId, naam, enhuId, stdat, soort)
VALUES(93, 'test', 1, 4, 0);
INSERT INTO tblInkoop(inkId, artId, inkat, enhuId, prijs)
VALUES(1, 93, 2, 1, 1);
INSERT INTO tblNuttig(inkId, nutat, stdat)
VALUES(1, 1, 1);
