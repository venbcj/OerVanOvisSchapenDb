INSERT INTO tblDracht(hisId, volwId) VALUES(2, 1);
delete from tblHistorie where hisId=2;
INSERT INTO tblHistorie(hisId, datum, stalId) VALUES(2, '2019-11-19', 1);
truncate tblVolwas;
INSERT INTO tblVolwas(hisId, volwId) VALUES(2, 1);

