DELETE FROM tblLeden WHERE lidId=42;
ALTER TABLE tblLeden AUTO_INCREMENT=42;
INSERT INTO tblLeden(login,passw,lidId,alias,
    meld,tech,fin,beheer,
    relnr, urvo, prvo,
    roep, voegsel, naam, tel, mail,
    reader
) 
VALUES('kobus', 'c435ea81ca8884510f3453c0a4fbedd5',42, 'koob',
    0,1,1,1,
    13,18,22,
    'Koob', 'de', 'Tester', '030-2434609', 'kobus@alcus.com',
    'Biocontrol'
);

DELETE FROM tblUbn WHERE lidId=42;
INSERT INTO tblUbn(ubnId, lidId, ubn) VALUES(2, 42, 99);
