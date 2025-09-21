delete from tblHistorie where stalId=2;
delete from tblActie where actId=1;
delete from tblStal;
delete from tblSchaap where schaapId=9;

insert into tblHistorie(datum, stalId, actId, skip) values('1990-01-01', 2, 1, 0);
insert into tblActie(op, actId, actie) values(1, 1, 'geboorte');
insert into tblStal(stalId, lidId, rel_best, schaapId) values(2, 1, null, 9);
insert into tblSchaap(schaapId, levensnummer, geslacht) values(9, '', 'ooi');
