INSERT INTO tblLeden(login,passw,lidId,alias,
    meld,tech,fin,beheer,
    relnr, urvo, prvo,
    kar_werknr
) 
-- harms wachtwoord is 'harpje'
VALUES('harm', '6edffa2b54fe663ac77c316115a0e44a',1,'harm',
    1,1,1,1,
    13,18,22,
    5
);
INSERT INTO tblUbn(lidId, ubnId, ubn) VALUES(1, 1, 63);

-- TODO kennis samenvoegen met newreader-keuzelijsten
INSERT INTO tblRedenuser(lidId, redId) SELECT 1, redId FROM tblReden;
UPDATE tblRedenuser SET uitval=1 WHERE lidId=1 AND redId IN(8,13,22,42,43,44);
UPDATE tblRedenuser SET afvoer=1 WHERE lidId=1 AND redId IN(15,45,46,47,48,49,50,51);

INSERT INTO tblEenheiduser(lidId, eenhId, actief) SELECT 1, eenhId, 1 FROM tblEenheid;
