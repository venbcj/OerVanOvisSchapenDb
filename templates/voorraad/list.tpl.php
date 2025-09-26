<table border = 0>
<tr><td colspan = 5 style = "font-size : 18px;"><b> Voorraad Voer </b></td></tr>
<tr valign = "bottom">
 <td><i><sub>Omschrijving</sub></i><hr></td>
 <td colspan = 2 width = 100 align = "center"><i><sub>Aantal nog toe te dienen</sub></i><hr></td>
 <td colspan = 2 width = 80><i><sub>Totale hoeveelheid</sub></i><hr></td>
 <td width = 70><i><sub></sub></i><hr></td>
 </tr>
<?php 
        while ($row = mysqli_fetch_assoc($voer))    {
            $row['stdat'] = str_replace('.00', '', $row['stdat']);
            $row['toedat'] = str_replace('.00', '', $row['toedat']);
?>
<tr>
 <td width = 300 ><table border = 0><tr><td><?php echo $row['naam'];?></td>
 <td><i style = "font-size : 13px;"><?php echo "&nbsp &nbsp per {$row['stdat']} {$row['eenheid']}"; ?></i></td></tr></table></td>
 <td align = "right" ><?php echo $row['toedat']; ?></td>
 <td><i style = "font-size : 14px;" > <?php echo ' x '.$row['stdat'].$row['eenheid']; ?> </i></td>
 <td align = "right"><?php echo $row['vrdat']; ?></td>
 <td><i style = "font-size : 13px;"><?php echo $row['eenheid']; ?></i></td>
 <td></td>
 <td>
<?php echo View::link_to('Corrigeren', 'Voorraadcorrectie.php?pst='.$row['artId'], ['style' => 'color: blue; font-size: 13px']); ?>
</td>
 <td></td>
</tr>
<?php
        }
?>
</table>

<hr><br/>

<table border = 0>
<tr><td colspan = 5 style = "font-size : 18px;"><b> Voorraad medicijn </b></td></tr>
<tr valign = "bottom">
 <td><i><sub>Omschrijving</sub></i><hr></td>
 <td colspan = 2 width = 100 align = "center"><i><sub>Aantal nog toe te dienen</sub></i><hr></td>
 <td colspan = 2 width = 80><i><sub>Totale hoeveelheid</sub></i><hr></td>
 <td width = 180><i><sub> &nbsp &nbsp Chargenummer</sub></i><hr></td>
 </tr>
<?php
        while ($row = mysqli_fetch_assoc($pil))    {
            $row['stdat'] = str_replace('.00', '', $row['stdat']);
            $row['toedat'] = str_replace('.00', '', $row['toedat']);
?>
<tr>
 <td width = 300 ><table border = 0><tr><td><?php echo $row['naam'];?></td>
 <td><i style = "font-size : 13px;"><?php echo "&nbsp &nbsp per {$row['stdat']} {$row['eenheid']}"; ?></i></td></tr></table></td>
 <td width = 40 align = "right" ><?php echo $row['toedat']; ?> </td>
 <td><i style = "font-size : 14px;" > <?php echo ' x '.$row['stdat'].$row['eenheid']; ?> </i></td>
 <td align = "right"><?php echo $row['vrdat']; ?></td>
 <td><i style = "font-size : 13px;"><?php echo $row['eenheid']; ?></i></td>
 <td><?php echo "&nbsp &nbsp &nbsp".$row['charge']; ?></td>
 <td>
<?php echo View::link_to('Corrigeren', 'Voorraadcorrectie.php?pst='.$row['artId'], ['style' => 'color: blue; font-size: 13px']); ?>
</td>
</tr>
<?php
        } ?>
</table>
