delete from tblArtikel;
delete from tblEenheiduser;
delete from tblInkoop;
delete from tblNuttig;

insert into tblArtikel(artId, naam, stdat, soort) values(1, 'test', 1, 'voer');
insert into tblEenheiduser(eenhId, lidId, enhuId) values(3, 1, 1);
insert into tblInkoop(enhuId, inkat, inkId, artId, prijs) values(1,10, 1, 1, 1);
insert into tblNuttig(nutat, stdat, inkId) values(1, 1, 1);

insert into tblArtikel(artId, naam, stdat, soort) values(2, 'test', 1, 'pil');
insert into tblInkoop(enhuId, inkat, inkId, artId, prijs) values(1,10, 2, 2, 1);
insert into tblNuttig(nutat, stdat, inkId) values(1, 1, 2);
