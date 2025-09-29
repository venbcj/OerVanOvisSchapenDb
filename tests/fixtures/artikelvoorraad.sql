delete from tblArtikel;
delete from tblInkoop;
delete from tblNuttig;

insert into tblArtikel(artId, naam, stdat, soort) values(1, 'test', 1, 'pil');
insert into tblInkoop(enhuId, inkat, inkId, artId, prijs) values(1,10, 1, 1, 1);
insert into tblNuttig(nutat, stdat, inkId) values(1, 1, 1);
