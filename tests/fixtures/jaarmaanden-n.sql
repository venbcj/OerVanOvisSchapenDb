delete from tblPeriode;
delete from tblHok;
delete from tblInkoop;
delete from tblVoeding;

insert into tblPeriode(periId, hokId, dmafsluit, doelId) values
(1, 1, '2021-10-11', 1),
(2, 1, '2021-11-11', 1)
;
insert into tblHok(hokId, hoknr, lidId) values(1, 1, 1);
insert into tblInkoop(inkId, artId, inkat, enhuId, prijs) values(1, 1, 1, 1, 1);
insert into tblVoeding(periId, inkId) values
(1, 1),
(2, 1);
