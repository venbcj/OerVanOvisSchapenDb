delete from tblEenheiduser;
delete from tblArtikel;
delete from tblInkoop;
delete from tblNuttig;
delete from tblHistorie;
delete from tblStal;
delete from tblSchaap;

insert into tblEenheiduser(eenhId, enhuId, lidId) values(3, 1, 1);
insert into tblArtikel(artId, enhuId, soort, naam) values(1, 1, 1, 'a');
insert into tblInkoop(artId, inkId, inkat, enhuId, prijs) values(1, 1, 1, 1, 1);
insert into tblNuttig(inkId, hisId) values(1, 1);
insert into tblHistorie(hisId, stalId, datum) values(1, 1, '1970-01-01');
insert into tblStal(stalId, schaapId) values(1, 1);
insert into tblSchaap(schaapId, geslacht) values(1, 'x');
