delete from tblSchaap;
delete from tblStal;
delete from tblHistorie;
delete from tblRelatie where relId=4;
delete from tblPartij where partId=1;

insert into tblSchaap(schaapId) values(5);
insert into tblStal(stalId, lidId, schaapId, rel_best) values(1, 1, 5, 4);
insert into tblHistorie(hisId, stalId, datum, skip, actId) values(1, 1, '2020-04-04', 0, 12);
insert into tblRelatie(relId, relatie, partId, uitval) values(4, 'Heiniken', 1, 1);
insert into tblPartij(partId, naam, lidId) values(1, 'haler', 1);
