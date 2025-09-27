delete from tblInkoop;
delete from tblNuttig;
delete from tblArtikel;
delete from tblEenheiduser;
delete from tblSchaap;
delete from tblStal;

insert into tblInkoop(inkId, artId, inkat, enhuId, prijs) values(1, 1, 5, 1, 1);
insert into tblNuttig(inkId, nutat, stdat) values(1, 1, 1);
insert into tblArtikel(artId, naam, stdat, enhuId, soort) values(1, 'wortel', 1, 1, 'GRE');
insert into tblEenheiduser(lidId, enhuId, eenhId) values(1, 1, 3);
insert into tblSchaap(schaapId, levensnummer) values(4, '131072');
insert into tblStal(lidId, schaapId) values(1, 4);
