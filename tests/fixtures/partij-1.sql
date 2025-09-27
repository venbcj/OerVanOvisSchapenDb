delete from tblPartij where lidId<>1 or partId=11;
delete from tblRelatie where partId=11;
delete from tblVervoer;

insert into tblPartij(partId, lidId, ubn, naam)
values (11, 1, '442', 'Henk');
insert into tblRelatie(relId, partId, relatie)
values(5, 11, 'kaas');
insert into tblVervoer(vervId, partId) values(1, 1);
