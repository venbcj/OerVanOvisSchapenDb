<!-- 8-03-2016 : gemaakt --> 

   

<?php 
include "connect_db.php";

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}

if(empty($ubn)) { $fout = "Gebruikersnaam (ubn) is niet ingevuld."; }
else if(empty($pword)) { $fout = "Wachtwoord is niet ingevuld."; }
else if(empty($ctr_p)) { $fout = "Bevestig het wachtwoord."; }
else if(empty($tel) && empty($mail)) { $fout = "1 van de 2 contactgevens moet zijn ingevuld."; }
else if(!empty($mail) ) {

	$isValid = true;
	$atIndex = strrpos($mail, "@");
	if (is_bool($atIndex) && !$atIndex)
	{
	   $isValid = false;
	}
	else
	{
	   $domain = substr($mail, $atIndex+1);
	   $local = substr($mail, 0, $atIndex);
	   // ... work with domain and local parts
	}
	if(!isset($domain)) { $isValid = false; } else {
		// controle top level domain mail
		$domIndex = strrpos($domain, ".");
		if (is_bool($domIndex) && !$domIndex)
		{
		   $isValid = false;
		}
		else
		{
		   $top_level_domain = substr($domain, $domIndex+1);
		   $domain_name = substr($domain, 0, $domIndex);
		   // ... work with domain and local parts
		}
	if(!isset($top_level_domain)) { $isValid = false; } else {
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		$top_l_domainLen = strlen($top_level_domain);

		if ($localLen < 1 || $localLen > 64)
		{
		   // local part length exceeded
		   $isValid = false;
		}
		else if ($domainLen < 1 || $domainLen > 255)
		{
		   // domain part length exceeded
		   $isValid = false;
		}
		else if ($top_l_domainLen < 2 || $top_l_domainLen > 3)
		{
		   // top level domain part length exceeded
		   $isValid = false;
		}

		// echo $local."<br>";	echo $domain."<br>";	echo $domain_name."<br>";	echo $top_level_domain."<br>"; 
	 }
	 
	 }
		if ($isValid == false) { $fout = "mailadres is foutief.";}
 
 }

if (!empty($ubn) && !empty($pword) && !empty($ctr_p) && $pword == $ctr_p && (!empty($tel) || (!empty($mail) && $isValid == true) ) )  {

$zoek_ubn = mysqli_query($db,"SELECT count(*) aant FROM tblLeden WHERE ubn = '$ubn' ") or die (mysqli_error($db));
	while ($zk = mysqli_fetch_assoc($zoek_ubn))
	{ $aantal = $zk['aant']; }
	
	if($aantal > 0) { $fout = "Dit ubn bestaat al." ;} 

	//else if(!empty($ubn) && strlen("$ubn")<> 7) {$fout = "Dit is geen ubn";}
	else if( !empty($ubn) && numeriek($ubn) == 1 ) {$fout = "Dit ubn wordt niet herkend.";}
	else if( $ubn == 1234567 || $ubn == 2345678 || $ubn == 3456789 || $ubn == 4567890 || $ubn == 0123456) {echo "Nee, Dit is geen ubn";}
	else { // Nu kan worden ingelezen
	
	$insert_tblLeden= "INSERT INTO tblLeden SET login = ".mysqli_real_escape_string($db,$ubn).", passw = '".mysqli_real_escape_string($db,$passw)."', ubn = ".mysqli_real_escape_string($db,$ubn).", meld = 0, tech = 1, fin = 1, tel = '".mysqli_real_escape_string($db,$tel)."', mail = '".mysqli_real_escape_string($db,$mail)."' ";
			mysqli_query($db,$insert_tblLeden) or die (mysqli_error($db));
	

// Sessie gegevens ophalen
$qrylidId = mysqli_query($db,"SELECT lidId, alias, fin FROM tblLeden 
								   WHERE ubn = '".mysqli_real_escape_string($db,$ubn)."' 
					  and passw = '".mysqli_real_escape_string($db,$passw)."' ;") or die (mysqli_error($db));
	while($row = mysqli_fetch_assoc($qrylidId))
			{
				$lId = $row['lidId'];
			}	

 $_SESSION["U1"] = $ubn;
 $_SESSION["W1"] = $passw;
 $_SESSION["I1"] = $lId;
 $_SESSION["UB"] = $ubn;
 
 $lidId = $lId;
 include "demo_table_insert.php";
 
 //if (!empty($ubn) && !empty($pword) && !empty($ctr_p) && $pword == $ctr_p && (!empty($tel) || (!empty($mail) && $isValid == true) ))  {
 header("location: ".$url."Home.php");
//}
}
}
   

?> 
