delete from tblArtikel;
delete from tblInkoop;
delete from tblNuttig;

insert into tblArtikel(artId, naam, stdat, soort, enhuId) values(1, 'test', 1, 'voer', 3);
insert into tblInkoop(enhuId, inkat, inkId, artId, prijs) values(1,10, 1, 1, 1);
insert into tblNuttig(nutat, stdat, inkId) values(1, 1, 1);

insert into tblArtikel(artId, naam, stdat, soort, enhuId) values(2, 'test', 1, 'pil', 4);
insert into tblInkoop(enhuId, inkat, inkId, artId, prijs) values(1,10, 2, 2, 1);
insert into tblNuttig(nutat, stdat, inkId) values(1, 1, 2);
