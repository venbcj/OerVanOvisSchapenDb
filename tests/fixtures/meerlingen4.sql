INSERT INTO tblSchaap(schaapId, levensnummer, volwId) VALUES
(9, 'moeder', null),
(2, 'lam1', 1),
(3, 'lam2', 1),
(4, 'lam3', 1),
(5, 'lam4', 1)
;

INSERT INTO tblStal(stalId, schaapId, ubnId) VALUES
(1, 9, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1)
;

INSERT INTO tblVolwas(volwId, mdrId) VALUES
(1, 9)
;

INSERT INTO tblHistorie(hisId, stalId, actId, datum) VALUES
(1, 2, 1, '2010-02-02'),
(2, 3, 1, '2010-02-02'),
(3, 4, 1, '2010-02-02'),
(4, 5, 1, '2010-02-02')
;
