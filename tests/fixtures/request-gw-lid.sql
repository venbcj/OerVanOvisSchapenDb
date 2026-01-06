insert into tblRequest(reqId, code, def) values(1, 'v42', 1);
insert into tblMelding(reqId, hisId, meldId, skip, fout) values(1, 1, 1, 0, 'helaas');
insert into tblHistorie(hisId, stalId, datum) values(1, 1, '1900-01-01');
insert into tblStal(stalId, ubnId, rel_herk, rel_best, schaapId) values(1, 1, 1, 1, 1);
insert into tblSchaap(schaapId, levensnummer, geslacht) values(1, '434609', 'ram');
