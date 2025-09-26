<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
<title>Fixed Header</title>

<style type="text/css">

.header_breed section {
    position: fixed;
    top: 0%;
 /*   padding: 0px;*/
    padding: 12px;
/*    left: 0%; right: 0; /* is nodig na position : sticky om element te centreren bron : https://stackoverflow.com/questions/2005954/center-a-positionsticky-element */
    left: .5%;
    width: 100%;
   /* height: 60px;*/
    height: 37px;
    font-size: 30px;
    background-color: #A6C6EB; /*Blauw*/
}

.header_smal {
    position: fixed;
    top: 47px;
    padding: 0;
    left: .5%; right: 0; /* is nodig na position : sticky om element te centreren bron : https://stackoverflow.com/questions/2005954/center-a-positionsticky-element */
    width: 99.5%;
    height: 25px;
    font-size: 14px;
    /*background-color: #9EB368; Deze kleur wordt bepaald in header.php */ 
}



.header_smal li {
  float: left; /* Dit zorgt ervoor dat de menu-opties naast elkaar worden getoond i.p.v. onder elkaar */ 
}

#rechts_uitlijnen {
    display: block;
    padding: 1px;
    float: right;
}

/*#table1 {
    width: 95%;
    border: 0px solid #A6C6EB;
    left: 20px;
    border-collapse: collapse; 
}*/

table {
  text-align: left;
  position: relative;
  border-collapse: collapse; /* Dit zorgt ervoor dat de cellen tegen elkaar aan staan */
}

tr.StickyHeader th { /* Binnen de table row met class StickyHeader wordt deze opmaak toegepast op alle th velden */
  background: blue;
  position: sticky;
  top: 85px; /* Don't forget this, required for the stickiness */
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}

/*.sticky {
  position: fixed;
  top: 86px;
  padding: 2px;
  background-color: #A6C6EB; /*white;*/
/*  border-spacing: 0px;
}*/

</style>
</head>

<body>

    <div id = "rechts_uitlijnen" class = 'header_breed'><section> </section></div>

<ul class="header_smal" style="background-color : #9EB368;" >
        
    <li id = "rechts_uitlijnen"><a href='<?php echo $url;?>index.php' style = 'color : black'>Inloggen</a></li>




</ul>

<script src="test2_script_header.js"></script>

<table id ="table1">

<tr height = 90> </tr>
<TR>
    <TD>
<table Border = 0 align = "center">

<!-- Aantal dieren -->

<tr>
 <td colspan = 3 align = 'right'> Aantal schapen 871 </td>
 <td colspan = 2 style = 'font-size:13px';> &nbsp waarvan</td>
 <td width ="150"><a href = '<?php echo $url;?>pdf.php' style = 'color : blue' > print pagina </a></td>
 <td colspan = 2 ><a href="export.php"> Export-xlsx </a></td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 2 style = 'font-size:13px';> 284 lammeren </td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 2 style = 'font-size:13px';> 572 moeders </td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 2 style = 'font-size:13px';> 15 vaders </td>
</tr>
</table>


<table Border = 0 align = "center">
<thead style="background-color : #9EB368;">

<tr class="StickyHeader" style = "font-size:12px; cel-spacing: 0px;">
 <th style = "text-align:center;"valign="bottom";width= 100>Transponder<br> bekend <hr></th>

 <th style = "text-align:center;"valign="bottom";width= 100>Werknr <hr></th>

 <th style = "text-align:center;"valign="bottom";width= 100>Levensnummer <hr></th>

 <th style = "text-align:center;"valign="bottom";width= 100>Geboren <hr></th>

 <th style = "text-align:center;"valign="bottom";width= 80>Geslacht <hr></th>

 <th style = "text-align:center;"valign="bottom";width= 80>Generatie <hr></th>

 <th style = "text-align:center;"valign="bottom";width= 50>Laatste<br> controle <hr></th>

 <th width=60></th>
</tr>

</thead>
<tbody>
<?php
for ($i=0; $i <2; $i++) {

?>
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00375 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481500375 <br> </td>
 <td width = 100 style = "font-size:15px;"> 28-08-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 03750 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481103750 <br> </td>
 <td width = 100 style = "font-size:15px;"> 31-08-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 03756 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481903756 <br> </td>
 <td width = 100 style = "font-size:15px;"> 02-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 03758 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481503758 <br> </td>
 <td width = 100 style = "font-size:15px;"> 02-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00021 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481300021 <br> </td>
 <td width = 100 style = "font-size:15px;"> 02-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00036 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481700036 <br> </td>
 <td width = 100 style = "font-size:15px;"> 05-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00038 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481300038 <br> </td>
 <td width = 100 style = "font-size:15px;"> 07-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00044 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481000044 <br> </td>
 <td width = 100 style = "font-size:15px;"> 06-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00045 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481300045 <br> </td>
 <td width = 100 style = "font-size:15px;"> 07-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00046 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481600046 <br> </td>
 <td width = 100 style = "font-size:15px;"> 07-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00047 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481900047 <br> </td>
 <td width = 100 style = "font-size:15px;"> 09-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00055 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481200055 <br> </td>
 <td width = 100 style = "font-size:15px;"> 09-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00056 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481500056 <br> </td>
 <td width = 100 style = "font-size:15px;"> 09-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00058 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481100058 <br> </td>
 <td width = 100 style = "font-size:15px;"> 09-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00061 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481900061 <br> </td>
 <td width = 100 style = "font-size:15px;"> 10-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00063 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481500063 <br> </td>
 <td width = 100 style = "font-size:15px;"> 13-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00067 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481700067 <br> </td>
 <td width = 100 style = "font-size:15px;"> 10-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00068 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481000068 <br> </td>
 <td width = 100 style = "font-size:15px;"> 10-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00069 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481300069 <br> </td>
 <td width = 100 style = "font-size:15px;"> 10-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00070 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481500070 <br> </td>
 <td width = 100 style = "font-size:15px;"> 10-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00071 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481800071 <br> </td>
 <td width = 100 style = "font-size:15px;"> 12-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00072 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481100072 <br> </td>
 <td width = 100 style = "font-size:15px;"> 11-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00080 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481400080 <br> </td>
 <td width = 100 style = "font-size:15px;"> 04-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00084 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481600084 <br> </td>
 <td width = 100 style = "font-size:15px;"> 15-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00088 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481800088 <br> </td>
 <td width = 100 style = "font-size:15px;"> 07-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00089 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481100089 <br> </td>
 <td width = 100 style = "font-size:15px;"> 15-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 00092 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481900092 <br> </td>
 <td width = 100 style = "font-size:15px;"> 28-09-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37500 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481937500 <br> </td>
 <td width = 100 style = "font-size:15px;"> 21-03-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> moeder <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37552 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481037552 <br> </td>
 <td width = 100 style = "font-size:15px;"> 23-04-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> moeder <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37554 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481637554 <br> </td>
 <td width = 100 style = "font-size:15px;"> 20-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37556 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481237556 <br> </td>
 <td width = 100 style = "font-size:15px;"> 22-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37558 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481837558 <br> </td>
 <td width = 100 style = "font-size:15px;"> 24-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37559 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481102459 <br> </td>
 <td width = 100 style = "font-size:15px;"> 24-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37560 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481337560 <br> </td>
 <td width = 100 style = "font-size:15px;"> 24-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37561 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481637561 <br> </td>
 <td width = 100 style = "font-size:15px;"> 23-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37562 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481937562 <br> </td>
 <td width = 100 style = "font-size:15px;"> 24-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37563 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481237563 <br> </td>
 <td width = 100 style = "font-size:15px;"> 24-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37564 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481537564 <br> </td>
 <td width = 100 style = "font-size:15px;"> 24-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37565 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481837565 <br> </td>
 <td width = 100 style = "font-size:15px;"> 24-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37566 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481102466 <br> </td>
 <td width = 100 style = "font-size:15px;"> 24-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ram <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
  
    
<tr align = "center">
 <td width = 100 style = "font-size:13px;"> Ja <br> </td>
 <td width = 100 style = "font-size:15px;"> 37567 <br> </td>
 <td width = 100 style = "font-size:15px;"> 102481437567 <br> </td>
 <td width = 100 style = "font-size:15px;"> 24-12-2023 <br> </td>
 <td width = 100 style = "font-size:15px;"> ooi <br> </td>
 <td width = 80 style = "font-size:15px;"> lam <br> </td>
 <td width = 80 style = "font-size:15px;">  <br> </td>
 <td width = 50> </td>
</tr>
<?php } ?>

</tbody>
</table>
<!-- Einde Aantal dieren -->

<script>
/*window.onscroll = function() {myFunction()};

var header = document.getElementById("stickyHeader");
var sticky = header.offsetTop;

function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
}*/
</script>
</body>

</TD>
</TR>
</table>

</html>
