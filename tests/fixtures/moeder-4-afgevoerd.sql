delete from tblHistorie where stalId=2 or hisId=1;
delete from tblStal where schaapId=9 or stalId=2;
delete from tblSchaap where schaapId=9;

insert into tblHistorie(hisId, datum, stalId, actId, skip) values(1, '1970-01-01', 2, 12, 0);
insert into tblStal(stalId, lidId, rel_best, schaapId) values(2, 1, null, 9);
insert into tblSchaap(schaapId, levensnummer, geslacht) values(9, '', 'ooi');
