insert into tblPartij(partId, lidId, ubn, naam)
values (11, 1, '442', 'Henk');
insert into tblRelatie(relId, partId, relatie)
values(5, 11, 'kaas');
insert into tblVervoer(vervId, partId) values(1, 11);
