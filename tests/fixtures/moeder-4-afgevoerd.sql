delete from tblHistorie where stalId=2;
delete from tblActie where actId=2;
delete from tblStal where schaapId=9;
delete from tblSchaap where schaapId=9;
insert into tblHistorie(datum, stalId, actId, skip) values('1970-01-01', 2, 2, 0);
insert into tblActie(af, actId, actie) values(1, 2, 'geboorte');
insert into tblStal(stalId, lidId, rel_best, schaapId) values(2, 1, null, 9);
insert into tblSchaap(schaapId, levensnummer, geslacht) values(9, '', 'ooi');
