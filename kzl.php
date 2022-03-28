<html>
<body>

<?php
include "url.php";
		
			$opties= array($kzlkey=>$kzlvalue);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if((isset($kzlId) && $kzlId == $key) || (isset($_POST[$name]) && $_POST[$name] == $key) )
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		

 ?>
 
 </body>
</html>