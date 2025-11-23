delete from tblSchaap;
delete from tblStal;
delete from tblHistorie;

insert into tblSchaap(schaapId, levensnummer, geslacht) values
(1, 'dertien', 'ram'),
(2, 'dertien', 'ram'),
(3, 'dertien', 'ram')
;
insert into tblStal(stalId, schaapId, ubnId, kleur, halsnr, rel_best) values
(1, 1, 1, 'rood', '13', null),
(2, 2, 1, 'rood', '13', null),
(3, 3, 1, 'rood', '13', null)
;
insert into tblHistorie(hisId, stalId, actId) values
(1, 1, 3),
(2, 2, 3),
(3, 3, 3)
;
