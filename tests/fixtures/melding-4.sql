delete from tblMelding;
delete from tblRequest;
delete from tblHistorie;
delete from tblStal;
delete from tblSchaap;
insert into tblMelding(meldId, skip, fout, reqId, hisId)
values(4, 0, 0, 5,6);
insert into tblRequest(reqId, code, def)
values(5, 'GER', 1);
insert into tblHistorie(datum, hisId, stalId, actId)
values
('2002-6-23', 6, 49, 1),
('1972-5-12', 7, 49, 2)
;
insert into tblStal(rel_herk, rel_best, stalId, schaapId)
values(13, 13, 49, 72);
insert into tblSchaap(schaapId, levensnummer, geslacht)
values(72, 131072, 'ooi'),
(73, 524288, 'ram'),
(75, 524288, 'ooi')
;
