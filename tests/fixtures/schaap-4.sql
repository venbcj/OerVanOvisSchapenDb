delete from tblSchaap;
delete from tblStal;
delete from tblHistorie;

insert into tblSchaap(schaapId, levensnummer, geslacht) values(4,'4','ram');
insert into tblStal(rel_herk, rel_best, stalId, schaapId, lidId)
values(1, null, 1, 4, 1);

