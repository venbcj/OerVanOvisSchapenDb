truncate tblRasuser;
insert into tblRasuser(lidId, rasId, actief) select 1, rasId, 1 from tblRas;
