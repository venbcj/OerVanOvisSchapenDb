punten

Wil je een loop dus meerdere dekkingen in 1 taak? loop ook bij dracht

Wil je dekken en dracht in historie terug zien? 
Zijn hier kosten aan verbonden? => boekingen. Nee

Moet ik nog rekening houden met de taak Dracht in de BioControl? Nee

Moet vaderdier nog op stal zijn of hoe lang geleden afgevoerd? 2 maanden afgevoerd

Vader moet zijn te wijzigen. Ja

Wil je eerdere koppels bewaren?


Moet ik nog rekening houden met 2x achter elkaar hetzelfde koppel inlezen?

De pagina Dracht heb ik hernoemd naar Dekkingen

Geristreer je handmatig een dekking of een dracht?

Is het vastleggen van dracht (in de zin van ja/nee) bij dekken noodzakelijk. Kan dit niet worden herleid van registratie dracht of de worp

Registratie dracht moet ook kunnen als er geen dekking bekend is toch ?
Wil je de dekking dan alsnog registreren in tblHistorie en wat is dan de dekdatum?
Is de 183 dagen dan gebasseerd op de drachtdatum?

Moet bij dracht de ooi kunnen worden gewijzigd?

Loop jaar op pagina Dekking is gebasseerd op 
Dekdatum
Drachtdatum - 0 dagen
worp - 149 dagen 


***

oke : post_readerGeb.php regel 373 : testen bij $modtech == 0

controle '"Bij overlijden moet datum t.b.v. uitval zijn ingevuld."' testen. ($kzlOoi ??)

testen aanvoer bestaand dier.
testen aanvoer bestaand dier zonder volwId => dier aanvoeren incl. ouders registreren


invSchaap : bij aanvoer rel herkomst registreren

volwId weghalen in Dekkingen / Dracht

***


UPDATE impAgrident SET verwerkt = NULL WHERE Id in (3717, 3718, 3719, 3720, 3721, 3722, 3723);
DELETE FROM tblSchaap WHERE schaapId > 15503;
DELETE FROM tblStal WHERE stalId > 15717;
DELETE FROM tblVolwas WHERE volwId > 7706;
UPDATE tblVolwas SET drachtig = NULL, verloop = NULL WHERE volwId > 7700;
UPDATE tblVolwas SET grootte = NULL WHERE volwId > 7702;
DELETE FROM tblHistorie WHERE hisId > 55600;
DELETE FROM tblDracht WHERE draId > 5;
DELETE FROM tblBezet WHERE bezId > 23460;
DELETE FROM tblMelding WHERE meldId > 22610;


SELECT * FROM impAgrident WHERE lidId = 13 AND actId = 1 AND verwerkt IS NULL 

SELECT * FROM impAgrident WHERE lidId = 13 AND actId = 19 AND verwerkt IS NULL 

100190702997

Freija

100213901521 moeder volwId 4707
100138973409 moeder zonder geboorte datum zonder volwId

100213501686 lam	volwId 6274

100129038944 vader zonder geboorte datum zonder volwId



SELECT * FROM impAgrident WHERE lidId = 13 AND actId = 19 AND verwerkt IS NULL 


UPDATE impAgrident SET verwerkt = NULL WHERE Id in (3741, 3743, 3745, 3760);

UPDATE tblVolwas SET grootte = NULL and drachtig = NULL WHERE volwId in (7715, 7716, 7691, 7693);
DELETE FROM tblHistorie WHERE hisId > 55644;
DELETE FROM tblDracht WHERE draId > 8;
