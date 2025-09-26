bestanden en tabellen die zijn aangepast t.b.v. keuze ubn Op 23-08-2025 is dit in productie gezet

TABELLEN:
120_tblUbn.sql
100_1_impAgrident.sql
9_tblStal.sql 
	tblStal bijgewerkt met :
	UPDATE tblStal st 
	 join tblUbn u on (st.lidId = u.lidId) set st.ubnId = u.ubnId
1_tblLeden.sql

BESTANDEN:
Gebruiker.php
Gebruikers.php
Hoklijsten.php
inlezenAgrident.php
InlezenReader.php
importRespons.php
InsAanvoer.php
InsGeboortes.php
InsStallijstscan_controle.php
InsStallijstscan_nieuwe_klant.php
InsTvUitscharen.php
InvSchaap.php
jsonSelectionLists.php
login.php
MeldAanvoer.php 	Is de lijn tussen 2 verschillende UBN wenselijk. Kan nl. niet in Stallijst.php i.v.m. sortering javascript
MeldAfvoer.php
Melden.php
MeldGeboortes.php
Meldingen.php
MeldOmnummer.php
MeldUitval.php
Newuser.php
post_readerAanv.php
post_readerGeb.php
post_readerStalscan.php
readerAgrident_v0.0.11.php
responscheck.php (ubn uit bestandsnaam gehaald) 		RESPONS BESTAND NIET KUNNEN TESTEN !!
Stallijst.php
Systeem.php (veld ubn in tblLeden van naam veranderd i.v.m. niet meer ingebruik !!!)
Ubn_toevoegen.php
Zoeken.php


Frans = 8281735 ubnId 17 Asten 100181996534 schaapId 27541
1 = 6575515 ubnId 16  Ospel Nederweerd 100230309353




6575515 Ospel Nederweerd 100229381803 schaapId 23281 stalId 23497

DELETE FROM tblMelding WHERE meldId > 38056;
DELETE FROM tblBezet WHERE bezId > 52769;
DELETE FROM tblHistorie WHERE hisId > 104837;
DELETE FROM tblStal WHERE stalId > 24929;
UPDATE tblStal SET rel_best = NULL WHERE stalId = 24264; 
UPDATE tblStal SET rel_best = NULL WHERE stalId = 9795; 
UPDATE tblStal SET rel_best = NULL WHERE stalId = 10206; 
UPDATE impAgrident SET verwerkt = NULL WHERE Id in (47268, 47269, 47270,47271); 