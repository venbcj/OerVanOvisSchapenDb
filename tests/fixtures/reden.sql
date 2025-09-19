delete from tblReden;
delete from tblRedenuser;

insert into tblReden(redId, reden, actief) values(1, 'daarom', 1);
insert into tblRedenuser(lidId, pil, redId) values(1, 1, 1);
