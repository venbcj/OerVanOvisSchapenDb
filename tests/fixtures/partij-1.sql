delete from tblPartij;
delete from tblRelatie;
delete from tblVervoer;

insert into tblPartij(partId, lidId, ubn, naam)
values (1, 1, '442', 'Henk');
insert into tblRelatie(relId, partId, relatie)
values(1, 1, 'kaas');
insert into tblVervoer(vervId, partId) values(1, 1);
