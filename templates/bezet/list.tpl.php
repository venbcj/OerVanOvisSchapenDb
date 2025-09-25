<form action="Bezet.php" method="post">
<table BORDER=0 width=960 align="center">
<tr>
  <td colspan=5> 
    <i style="font-size : 13px;" > Verblijflijsten per doelgroep : &nbsp  
<?php
    if ($aantal_zonder_speendatum > 0) {
        echo View::link_to('Geboren', 'Hoklijst.php?pstgroep=1', ['style' => 'color: blue;']);
    }
    if ($aantal_met_speendatum) {
        echo "&nbsp;&nbsp;";
        echo View::link_to('Gespeend', 'Hoklijst.php?pstgroep=2', ['style' => 'color: blue;']);
    }
?>
    </i>
  </td>
  <td colspan=8 align="right">
<?php if ($aantal_zonder_verblijf > 0) {
echo View::link_to('Schapen zonder verblijf', 'Loslopers.php', ['style' => 'color: blue;']);
} ?>
  </td>
</tr>
<tr style="font-size:12px;">
<th colspan=4 ></th>
<th colspan=2 align =center valign=bottom style="text-align:center;" >Totaal</th>
<th colspan=6 ></th>
</tr>
<tr style="font-size:12px;">
 <th width=0 height=30></th>
 <th style="text-align:center;" valign="bottom" width= 150>Verblijf<hr></th>
 <th style="text-align:center;" valign="bottom" width= 110>Eerste in<hr></th>
 <th style="text-align:center;" valign="bottom" width= 110>Meest recente eruit<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>voor spenen<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>na spenen<hr></th>
 <th style="text-align:center;" valign="bottom" width= 80>Lam aanwezig<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>Doelgroep verlaten<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>Overge- plaatst<hr></th>
 <th style="text-align:center;" valign="bottom" width= 50>Uitval<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>Moeders van lammeren<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>Volwassen aanwezig<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>Volwassen<br> totaal geteld<hr></th>
 <th style="text-align:center;" valign="bottom"><hr></th>
 <th width=60></th>
</tr>
<?php
    // Loop alle verblijven in gebruik
while ($row = $zoek_verblijven_in_gebruik->fetch_assoc()) {
$extra = $closure($row);
?>
<tr align="center">    
    <td width=0> </td>            
    <td width=150 style="font-size:15px;">     
<?php echo View::link_to($row['hoknr'], "Hoklijsten.php?pst={$row['hokId']}", ['style' => 'color: blue;']); ?>
 <br/>  
    </td>       
    <td width=110 style="font-size:13px;"> <?php echo $extra['van']; ?> </td>       
    <td width=110 style="font-size:13px;"> <?php echo $extra['tot']; ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php echo View::nonzero($row['maxgeb']); ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php echo View::nonzero($row['maxspn']); ?> </td>
    <td width=60 style="font-size:15px; color:blue; "> <?php echo $extra['aanwezig']; ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php echo $extra['uit']; ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php echo $extra['overpl']; ?> </td>
    <td width=50 style="font-size:15px; color:grey; "> <?php echo $extra['uitval']; ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php echo $extra['mdrs']; ?> </td>
    <td width=60 style="font-size:15px; color:blue; "> <?php echo View::nonzero($extra['aanwezig3']); ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php echo View::nonzero($row['maxprnt']); ?> </td>
    <td width=200 style="font-size:13px;">
    <?php if ($extra['dmvan'] && $extra['dmvan'] < $extra['today']) { ?>
      <?php echo View::link_to('Periode sluiten', "HokAfsluiten.php?pstId={$row['hokId']}", ['style' => 'color: blue']); ?>
    <?php } ?>
    </td>
</tr>
<?php
} // Einde Loop alle verblijven in gebruik ?>
</tr>            
</table>
</form>
