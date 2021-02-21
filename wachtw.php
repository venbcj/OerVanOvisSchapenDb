
<?php
$str = '1507131';


$ww = md5($str);
$ww1 = md5($str.'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');
/*
if ( md5($str) === '1f3870be274f6c49b3e31a0c6728957f') {
    echo "Would you like a green or red apple?";
} */

echo $ww. "<br>";
echo '$ww_new = '.$ww1. "<br>". "<br>";

$sIrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

echo $sIrl;

echo "<br>". "<br>". "<br>". "<br>";
$subject = 'a-, b-, c-, d-, e-, f-, g-, h-, i-, j-, k-, l-, m-, n-, o-, p-, q-, r-, s-, t-, u-, v-, w-, x-, y-, z-';


function numeriek($subject) {
	
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {

  //var_dump($matches[1]);	
	return 1;
}

}

//echo $result. "<br>";
if ( numeriek('100024575441') <> 1) { echo "Zo had ik controle numeriek inderdaad gewild"; }
else {echo "Zo dus niet";}
?> 
