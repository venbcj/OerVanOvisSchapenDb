TRUNCATE tblArtikel;
TRUNCATE tblInkoop;
TRUNCATE tblVoeding;
INSERT INTO tblArtikel(artId, naam, stdat, soort) VALUES(93, 'test', 4, 0);
INSERT INTO tblInkoop(inkId, artId, inkat, enhuId, prijs) VALUES(1, 93, 2, 1, 1);
INSERT INTO tblVoeding(inkId, nutat, stdat) VALUES(1, 1, 1);
