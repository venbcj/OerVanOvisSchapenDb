Bij doodgeboren wordt reden "" omgezet naar 0. Let op aantal dagen geleefd en als momId 0 is !

Bij adopteren transpondernummer moeder opslaan		klaar
Bij overplaatsen transpondernummer schaap opslaan	klaar
Bij aanvoer transpondernummer schaap opslaan		klaar


Fout afhandeling als omschrijving voor en na taak niet klopt

Reden afvoer Geen vervangen door Onbekend 





DELETE FROM impAgrident WHERE dmcreate = '2021-02-12 12:05:37'

SELECT * FROM `impAgrident` WHERE `actId` IN (15,16,2,3,5) AND `dmcreate` = '2021-02-12 12:12:00' 

UPDATE `impAgrident` SET `verwerkt` = '1' WHERE dmcreate like '2021-02-10%' and lidId = 3