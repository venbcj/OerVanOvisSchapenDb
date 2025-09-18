delete from tblBezet where bezId=1;
delete from tblHistorie where HisId in(1,2);
delete from tblStal where stalId=1;
delete from tblSchaap where schaapId=1;
insert into tblBezet(bezId, hisId, hokId) values(1, 1, 1);
insert into tblHistorie(hisId, stalId, skip, actId)
values(1, 1, 0, 1),
(2, 1, 0, 4);
insert into tblStal(stalId, schaapId) values(1, 1);
insert into tblSchaap(schaapId) values(1);

