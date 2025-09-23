delete from tblHok;
delete from tblPartij;
delete from tblRelatie;

delete from tblMoment;
insert into tblMoment(momId, moment) values
(1, 1),
(2, 1),
(3, 1);

delete from tblEenheid;
insert into tblEenheid(eenhId, eenheid) values
(1, 1),
(2, 1);

delete from tblElement;
insert into tblElement(elemId, element) values
(1, 1),
(2, 1),
(3, 1),
(4, 1);

delete from tblRubriek;
insert into tblRubriek(rubId, rubhId, credeb) values
(1, 1, 1);
