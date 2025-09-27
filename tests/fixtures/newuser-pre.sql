delete from tblHok;
delete from tblPartij where lidId<>1;
delete rel from tblRelatie rel inner join tblPartij par USING (partId) where lidId<>1;
