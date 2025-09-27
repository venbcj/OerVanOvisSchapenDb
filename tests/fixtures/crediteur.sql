delete from tblPartij where partId=4;
delete from tblRelatie where relId=4;

insert into tblPartij(partId, lidId, ubn, naam) values(4, 1, 13, 'partij');
insert into tblRelatie(relId, relatie, partId, uitval) values(4, 'dirk', 4, 1);
