<?php

require_once("autoload.php");



$versie = "16-12-2017"; /* Rapport gemaakt */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
 session_start(); ?>

<html>
<head>
<title>Groeiresultaat schapen</title>
</head>
<body>

<center>
<?php
$titel = 'Groei resultaten per schaap';
$subtitel = ''; 
 
include "header.tpl.php"; ?>

		<TD width = 960 height = 400 valign = "top" >
<?php
$file = "Groeiresultaat.php";
include "login.php";
if (Auth::is_logged_in()) { if($modtech ==1) { ?>

<table border = 0 >
<tr>
<td> </td>
<td>	


<?php
$result = mysqli_query($db,"
SELECT schaapId, werknr, geslacht, gebkg, wg1, spkg, wg2, wg3, afvkg, round(groei/coalesce(dagen,1)*1000,2) gemgroei
from (

select s.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, s.geslacht, hg.gebkg, w1_voorsp.wg1, hs.spkg, w1_nasp.wg2, w2_nasp.wg3, haf.afvkg,
 coalesce(hg.datum,coalesce(w1_voorsp.datum,coalesce(hs.dmspeen,coalesce(w1_nasp.datum,coalesce(w2_nasp.datum,haf.datum))))) minkg,
 coalesce(haf.datum,coalesce(w2_nasp.datum,coalesce(w1_nasp.datum,coalesce(hs.dmspeen,coalesce(w1_voorsp.datum,hg.datum))))) maxkg,
 coalesce(haf.afvkg,coalesce(w2_nasp.wg3,coalesce(w1_nasp.wg2,coalesce(hs.spkg,coalesce(w1_voorsp.wg1,hg.gebkg))))) - coalesce(hg.gebkg,coalesce(w1_voorsp.wg1,coalesce(hs.spkg,coalesce(w1_nasp.wg2,coalesce(w2_nasp.wg3,haf.afvkg))))) groei,
 datediff(
 	coalesce(haf.datum,coalesce(w2_nasp.datum,coalesce(w1_nasp.datum,coalesce(hs.dmspeen,coalesce(w1_voorsp.datum,hg.datum))))),
 	coalesce(hg.datum,coalesce(w1_voorsp.datum,coalesce(hs.dmspeen,coalesce(w1_nasp.datum,coalesce(w2_nasp.datum,haf.datum)))))
 ) dagen
from tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 left join (
 	select schaapId, kg gebkg, h.datum
 	from tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and actId = 1 and skip = 0 and h.kg is not null
 ) hg on (hg.schaapId = s.schaapId)
 left join (
	select wg1.schaapId, h.kg wg1, h.datum
	from (
		select st.schaapId, min(wg1.hisId) hisId
		from tblHistorie wg1
		 join tblStal st on (wg1.stalId = st.stalId)
		 left join (
		 	select stalId, kg spkg, datum dmaanw from tblHistorie where actId = 3 and skip = 0
		) aw on (aw.stalId = st.stalId)
		 left join (
		 	select stalId, kg spkg, datum dmspeen from tblHistorie where actId = 4 and skip = 0
		) sp on (sp.stalId = st.stalId)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and wg1.actId = 9 and skip = 0 
		 and (wg1.datum < sp.dmspeen or isnull(sp.dmspeen))
		 and isnull(aw.dmaanw)
		group by st.schaapId
	 ) wg1
	 join tblHistorie h on (wg1.hisId = h.hisId)
 ) w1_voorsp on (w1_voorsp.schaapId = s.schaapId)
 left join (
 	select st.schaapId, h.kg spkg, h.datum dmspeen
 	from tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	where actId = 4 and skip = 0 and h.kg is not null
 ) hs on (hs.schaapId = hg.schaapId)
 left join (
	select wg_nasp.schaapId, h.kg wg2, h.datum
	from tblHistorie h
	 join (
		select w1.schaapId, w1.hisId, count(w1.lidId) rank
		from (
			select st.lidId, st.schaapId, wg.hisId, wg.datum, aw.dmaanw, sp.dmspeen
			from tblHistorie wg
			 join tblStal st on (wg.stalId = st.stalId)
			 left join (
			 	select stalId, datum dmaanw
				from tblHistorie
				where actId = 3 and skip = 0
			) aw on (aw.stalId = st.stalId)
			left join (
			 	select stalId, datum dmspeen
				from tblHistorie
				where actId = 4 and skip = 0
			) sp on (sp.stalId = st.stalId)
			
			where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and wg.actId = 9 and skip = 0 and ( (aw.stalId is not null and wg.datum > aw.dmaanw) or (sp.stalId is not null and wg.datum > sp.dmspeen) )
		 ) w1
		 join (
			select st.lidId, st.schaapId, wg.hisId, wg.datum, aw.dmaanw, sp.dmspeen
			from tblHistorie wg
			 join tblStal st on (wg.stalId = st.stalId)
			 left join (
			 	select stalId, datum dmaanw
				from tblHistorie
				where actId = 3 and skip = 0
			) aw on (aw.stalId = st.stalId)
			left join (
			 	select stalId, datum dmspeen
				from tblHistorie
				where actId = 4 and skip = 0
			) sp on (sp.stalId = st.stalId)
			
			where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and wg.actId = 9 and skip = 0 and ( (aw.stalId is not null and wg.datum > aw.dmaanw) or (sp.stalId is not null and wg.datum > sp.dmspeen) )
			) w2 on (w1.lidId = w2.lidId and w1.schaapId = w2.schaapId and w1.datum >= w2.datum)
		group by w1.lidId, w1.schaapId, w1.hisId, w1.datum, w1.dmaanw, w1.dmspeen
		having (count(w1.lidId) = 1 )
	 ) wg_nasp on (h.hisId = wg_nasp.hisId)
) w1_nasp on (w1_nasp.schaapId = s.schaapId)
left join (
	select wg_nasp.schaapId, h.kg wg3, h.datum
	from tblHistorie h
	 join (
		select w1.schaapId, w1.hisId, count(w1.lidId) rank
		from (
			select st.lidId, st.schaapId, wg.hisId, wg.datum, aw.dmaanw, sp.dmspeen
			from tblHistorie wg
			 join tblStal st on (wg.stalId = st.stalId)
			 left join (
			 	select stalId, datum dmaanw
				from tblHistorie
				where actId = 3 and skip = 0
			) aw on (aw.stalId = st.stalId)
			left join (
			 	select stalId, datum dmspeen
				from tblHistorie
				where actId = 4 and skip = 0
			) sp on (sp.stalId = st.stalId)
			
			where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and wg.actId = 9 and skip = 0 and ( (aw.stalId is not null and wg.datum > aw.dmaanw) or (sp.stalId is not null and wg.datum > sp.dmspeen) )
		 ) w1
		 join (
			select st.lidId, st.schaapId, wg.hisId, wg.datum, aw.dmaanw, sp.dmspeen
			from tblHistorie wg
			 join tblStal st on (wg.stalId = st.stalId)
			 left join (
			 	select stalId, datum dmaanw
				from tblHistorie
				where actId = 3 and skip = 0
			) aw on (aw.stalId = st.stalId)
			left join (
			 	select stalId, datum dmspeen
				from tblHistorie
				where actId = 4 and skip = 0
			) sp on (sp.stalId = st.stalId)
			
			where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and wg.actId = 9 and skip = 0 and ( (aw.stalId is not null and wg.datum > aw.dmaanw) or (sp.stalId is not null and wg.datum > sp.dmspeen) )
			) w2 on (w1.lidId = w2.lidId and w1.schaapId = w2.schaapId and w1.datum >= w2.datum)
		group by w1.lidId, w1.schaapId, w1.hisId, w1.datum, w1.dmaanw, w1.dmspeen
		having (count(w1.lidId) = 2 )
	 ) wg_nasp on (h.hisId = wg_nasp.hisId)
) w2_nasp on (w2_nasp.schaapId = s.schaapId)
left join (
 	select schaapId, kg afvkg, h.datum
 	from tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and actId = 12 and skip = 0 and h.kg is not null
 ) haf on (haf.schaapId = s.schaapId)

where s.levensnummer is not null and st.lidId = ".mysqli_real_escape_string($db,$lidId)." and (hg.gebkg is not null or w1_voorsp.wg1 is not null or hs.spkg is not null)
) a
order by werknr
") or die (mysqli_error($db));
?>
 
<tr style = "font-size:12px;">
<th width = 0 height = 30></th>
<th style = "text-align:center;"valign="bottom";width= 80>Werknr<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 50>Geslacht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 50>Geboorte kg<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 200>Weging 1<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 200>Speen kg<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 60>Weging 2<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Weging 3<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Aflever kg<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 60>Gem groei per dag<hr></th>

<th style = "text-align:center;"valign="bottom";width= 80></th>
<th width = 600></th>

	

<th width= 60 ></th>
 </tr>
<?php
		while($row = mysqli_fetch_array($result))
		{ ?>
		
<tr align = "center">	
	   <td width = 0> </td>			
	   
	   <td width = 100 style = "font-size:15px;"> <?php echo $row['werknr']; ?> <br> </td>
	   <td width = 1> </td>	   	   
	   <td width = 100 style = "font-size:15px;"> <?php echo $row['geslacht']; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $row['gebkg']; ?> <br> </td>
	   <td width = 1> </td>	
	   <td width = 200 style = "font-size:15px;"> <?php echo $row['wg1']; ?> <br> </td>
	   <td width = 1> </td>

	   <td width = 200 style = "font-size:15px;"> <?php echo $row['spkg']; ?> <br> </td>

	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $row['wg2']; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 80 style = "font-size:15px;"> <?php echo $row['wg3']; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 80 style = "font-size:15px;"> <?php echo $row['afvkg']; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 60 style = "font-size:15px;"> <?php echo $row['gemgroei']; ?> <br> </td>
	   
	   
</tr>				

		
<?php		} ?>
</tr>				
</table>


		</TD>
<?php } else { ?> <img src='resultHok_php.jpg'  width='970' height='550'/> <?php }
include "menuRapport.php"; } ?>
</body>
</html>
