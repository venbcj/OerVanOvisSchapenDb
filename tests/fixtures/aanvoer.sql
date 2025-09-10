delete from tblRequest;
insert into tblRequest(reqId, dmmeld, code, def) values
(1, null, 'AAN', 'N')
;
delete from tblMelding;
insert into tblMelding(reqId, hisId, skip) values(1, 1, 1);
delete from tblHistorie;
insert into tblHistorie(hisId, stalId, skip, datum, actId) values
(1, 1, 0, '2022-11-11', 2),
(2, 1, 0, '2021-11-10', 3)
;
delete from tblStal;
insert into tblStal(stalId, lidId, schaapId, ubnId, rel_herk)
values(1, 1, 42, 1, 3),
(2,1,43,1,3)
;
delete from tblSchaap;
insert into tblSchaap(schaapId, levensnummer, geslacht) values(42, '100000000011', 'ram');
delete from tblUbn;
insert into tblUbn(lidId, ubnId, ubn) values(1, 1, 63);
