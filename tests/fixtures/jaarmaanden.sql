delete from tblPeriode;
delete from tblHok;

insert into tblPeriode(hokId, dmafsluit, doelId) values
(1, '2021-10-11', 1),
(1, '2021-11-11', 1)
;
insert into tblHok(hokId, hoknr, lidId) values(1, 1, 1);

insert into tblInkoop(artId, inkat, enhuId, prijs) values(1, 1, 1, 1);
