truncate tblLeden;
truncate tblUbn;

INSERT INTO tblLeden(login,passw,lidId,
    meld,tech,fin,beheer,
    relnr, urvo, prvo
) 
VALUES('harm', '6edffa2b54fe663ac77c316115a0e44a',1,
    1,1,1,1,
    13,18,22
);
INSERT INTO tblUbn(lidId, ubnId, ubn) VALUES(1, 1, 63);
