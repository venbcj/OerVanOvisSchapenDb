delete from tblSchaap;
delete from tblStal;
delete from tblHistorie;
delete from tblNuttig;
delete from tblInkoop;

insert into tblSchaap(levensnummer, schaapId, geslacht) values
('102', 1, 'ram'),
('103', 2, 'ooi');
insert into tblStal(schaapId, stalId, ubnId) values
(1, 1, 1),
(2, 2, 1);
insert into tblHistorie(stalId, hisId, actId, skip, datum) values
(1, 1, 8, 0, '1970-01-02'),
(2, 2, 8, 0, '1970-01-02')
;
insert into tblNuttig(hisId, inkId, nutat, stdat) values
(1, 1, 1, 1),
(2, 1, 1, 1);
insert into tblInkoop(inkId, artId, inkat, enhuId, prijs) values(1, 1, 1, 1, 1);
