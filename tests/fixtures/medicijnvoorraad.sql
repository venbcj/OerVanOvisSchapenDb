insert into tblArtikel(artId, naam, stdat, enhuId, soort) values(93, 'wortel', 1, 3, 'GRE');
insert into tblInkoop(inkId, artId, inkat, enhuId, prijs) values(1, 93, 5, 1, 1);
insert into tblNuttig(inkId, nutat, stdat) values(1, 1, 1);
insert into tblSchaap(schaapId, levensnummer) values(4, '131072');
insert into tblStal(stalId, ubnId, schaapId) values(1, 1, 4);
