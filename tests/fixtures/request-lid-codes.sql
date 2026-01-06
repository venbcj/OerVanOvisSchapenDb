delete from tblRequest;
delete from tblMelding;
delete from tblHistorie;
delete from tblStal;
delete from tblUbn;
insert into tblRequest(reqId, code, dmmeld) values
(1, 'GER', null),
(2, 'AFV', null),
(3, 'DOO', null),
(4, 'AAN', null),
(5, 'VMD', null)
;
insert into tblMelding(reqId, hisId, meldId, skip, fout) values
(1, 11, 1, 0, null),
(2, 12, 2, 0, null),
(3, 13, 3, 0, null),
(4, 14, 4, 0, null),
(5, 15, 5, 0, null)
;
insert into tblHistorie(hisId, stalId, datum, skip) values
(11, 1, '1900-01-01', 0),
(12, 1, '1900-01-01', 0),
(13, 1, '1900-01-01', 0),
(14, 1, '1900-01-01', 0),
(15, 1, '1900-01-01', 0)
;
insert into tblStal(stalId, ubnId, schaapId) values(1, 1, 0);
INSERT INTO tblUbn(lidId, ubnId, ubn) values(1, 1, '333');
