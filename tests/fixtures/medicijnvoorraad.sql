delete from tblInkoop;
delete from tblNuttig;
delete from tblArtikel;
delete from tblSchaap;
delete from tblStal;

insert into tblInkoop(inkId, artId, inkat, enhuId, prijs) values(1, 93, 2, 1, 1);
insert into tblNuttig(inkId, nutat, stdat) values(1, 1, 1);
insert into tblArtikel(artId, naam, stdat, enhuId, soort) values(93, 'wortel', 4, 3, 'GRE');
insert into tblSchaap(schaapId, levensnummer) values(4, '131072');
insert into tblStal(ubnId, schaapId) values(1, 4);
