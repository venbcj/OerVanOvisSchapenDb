<?php /* 20-12-2020 : gemaakt */

include "connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	exit();
}
else
{  // Begin van else

$headers = getallheaders(); // geef in een array ook headers terug die ik naar de server heb gestuurd in eerste instantie
//var_dump($headers);
//echo is_array($string) ? 'dit is een array' : 'dit is geen array';
	

if (!isset($headers['Authorization'])) { // Als in de headers geen index 'Autorization voorkomt'
    http_response_code(401); // Unauthorized
    Echo 'authorization header bestaat niet.';
    exit;
} else 
{

    $authorization = explode ( " ", $headers['Authorization'] );
 
 	if (count($authorization) == 2 && trim($authorization[0]) == "Bearer" && strlen(trim($authorization[1])) == 64) {

		$zoek_lidId = mysqli_query($db, "SELECT lidId from tblLeden where readerkey = '".mysqli_real_escape_string($db,$authorization[1])."'" ) or die(mysqli_error($db));

		$result = mysqli_fetch_array($zoek_lidId);

		if($result){
           $lidid = $result['lidId'];
		} else {
			http_response_code(401); // Unauthorized
			echo 'via authorization header wordt de gebruiker niet gevonden.';
	    	exit;
		}

	} else {
    	http_response_code(401); // Unauthorized
    	echo 'authorization header heeft niet de juiste opmaak.';
    	exit;
	}
}
 
// $lidid = 3;

switch ($_SERVER['REQUEST_METHOD']) { // Switch
	case 'GET':      


$laatste_selectie = mysqli_query($db,"
SELECT max(volgnr) volgnr 
FROM tblAlertselectie
WHERE lidId = '".mysqli_real_escape_string($db,$lidid)."'
") or die (mysqli_error($db)); 

while($row = mysqli_fetch_array($laatste_selectie))
		{ $volgnr = $row['volgnr']; }


$result = mysqli_query($db,"
SELECT transponder tran, alertId Id
FROM tblAlertselectie
WHERE volgnr = '".mysqli_real_escape_string($db,$volgnr)."'
") or die (mysqli_error($db)); 

$rows = mysqli_num_rows($result);



if(isset($result) && $rows > 0) {
while($row = mysqli_fetch_array($result))
		{
						
			$opties[] = array('Transponder' => $row['tran'], 'AlertId' => $row['Id']);

			}
}



$vb = json_encode($opties);
echo $vb;



		 break;
	default:
		http_response_code(405); // Methode niet toegestaan
		exit;
	
} // Einde Switch
//echo json_encode(array("Result" => "Tweede goede resultaat "));
http_response_code(200); // Ok alles is goed

 
} // Einde Begin van else
?>