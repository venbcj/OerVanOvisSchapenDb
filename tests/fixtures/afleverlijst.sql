delete from tblHistorie;
delete from tblStal;
delete from tblRelatie;
delete from tblPartij;

insert into tblHistorie(hisId, stalId, datum) values(1, 1, '1942-01-01');
insert into tblStal(stalId, schaapId, rel_best, lidId) values(1, 1, 1, 1);
insert into tblRelatie(relId, partId, relatie) values(1, 1, 'relatie');
insert into tblPartij(partId, lidId, naam) values(1, 1, 'partij');
