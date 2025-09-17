delete from tblSchaap where levensnummer='4';
insert into tblSchaap(schaapId, levensnummer, geslacht) values(4,'4','ram');
delete from tblStal where schaapId=4;
insert into tblStal(rel_herk, rel_best, stalId, schaapId, lidId)
values(1, null, 1, 4, 1);

