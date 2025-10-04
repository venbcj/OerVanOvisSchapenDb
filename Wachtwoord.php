<?php 

require_once("autoload.php");

$versie = '19-3-2015'; /* bestand gemaakt*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "center" align = "center" > gewijzigd naar <TD valign = 'top' align = 'center'> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
$titel = 'Wijzigen inloggegevens';
$file = "Wachtwoord.php";
include "login.php";
?>
        <TD valign = 'top' align = 'center'>
<?php
if (Auth::is_logged_in()) {
// CODE T.B.V. WIJZIGEN WACHTWOORD
# #0004124 als dit toch alleen mag in Wachtwoord... waarom dan niet opnemen in Wachtwoord? --BCB
if ($curr_url == $url."Wachtwoord.php") {
    // $curr_url gedeclareerd is url.php
    $veld = "submit";
    if (isset($_POST['knpChange'])) {
        $lid_gateway = new LidGateway();
        // TODO: #0004184 inline temp
        $stored_user = $lid_gateway->findLoginPasswById($lid);
        $txtuser = $_POST['txtUser'];
        $txtuserold = $_POST['txtUserOld'];
        $wwold = $_POST['txtOld'];
        $ww = md5($_POST['txtOld'].'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');
        $txtpassw = $_POST['txtNew'];
        $wwnew = md5($txtpassw.'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');
        if (empty($txtuser) || empty($_POST['txtOld'])) {
            $fout = "Gebruikersnaam of wachtwoord is onbekend.";
            unset($ww);
        //} elseif (empty($txtpassw)) {
        // $fout = "Nieuw wachtwoord is leeg";
        } elseif ($txtpassw <> $_POST['txtBevest']) {
            $fout = "Het nieuwe wachtwoord komt niet overeen met de bevestiging.";
            unset($ww);
        } elseif ($ww <> $passw && $_POST['txtOld'] <> $passw) {
            $fout = "Het oude wachtwoord is onjuist.";
            unset($ww);
        } elseif (!empty($txtpassw) && strlen($txtpassw)< 6) {
            $fout = "Het wachtwoord moet uit minstens 6 karakters bestaan.";
            unset($ww);
        //} elseif ($txtuser == $txtuserold && $wwold == $passw) {
        //  unset($ww);
        } else {
            // controle of combinatie tussen user en passw al bestaat
            if (empty($txtpassw)) {
                $wwnew = $ww;
            }
            echo "user $txtuser password $wwnew";
            $num_rows = $lid_gateway->countUserByLoginPassw($txtuser, $wwnew);
            if ($num_rows > 0) {
                $fout = "Deze combinatie tussen gebruikersnaam en wachtwoord bestaat al. Kies een andere combinatie.";
            } else {
            // EINDE controle of combinatie tussen user en passw al bestaat
                // username en wachtwoord wijzigen
                if ($txtuser <> $stored_user['user']) {
                // username wijzigen
                    $lid_gateway->update_username($lid, $txtuser);
                    Session::set("U1", $txtuser); /* tbv de query $result in login.php*/
                    $goed = "De inloggegevens zijn gewijzigd";
                    $veld = "hidden";
                } elseif (isset($wwnew) && $stored_user['passw'] <> $wwnew) {
                    // wachtwoord wijzigen
                    $lid_gateway->update_password($lid, $wwnew);
                    $passw = $wwnew; /*tbv de query $result in login.php */
                    Session::set("W1", $txtpassw); /* tbv (nieuwe) sessie gegevens */
                    $goed = "De inloggegevens zijn gewijzigd." ;
                    $veld = "hidden";
                }
            }
        }
        if (isset($fout)) {
            unset($ww);
        }
    }
}
// EINDE CODE T.B.V. WIJZIGEN WACHTWOORD

$name = Session::get("U1");  ?>

<form method="POST" action=" <?php echo $file; ?> ">
<p>
<table>
 <tr height = 50 ><td> Gebruikersnaam : </td>
    <td> <input type="text" name="txtUser" size="20" value = <?php echo $name; ?> ></td>
    <td> <input type="hidden" name="txtUserOld" size="20" value = <?php echo $name; ?> ></td> <!-- hiddden -->
</tr>
 <tr><td> Oud wachtwoord : </td>
    <td> <input type="password" name="txtOld"       size="20" value = <?php if (isset($ww)) { echo $ww;} // $ww gedeclareerd in passw ?> ></td> 
    <td> <input type="hidden" name="txtOldcntr" size="20" value = <?php echo $passw; ?> ></td> <!-- hiddden -->
 </tr>
 <tr><td> Nieuw wachtwoord : </td>
    <td> <input type="password" name="txtNew" size="20" value = <?php ; ?> ></td>
 </tr>
 <tr><td> Bevestig wachtwoord : </td>
    <td> <input type="password" name="txtBevest" size="20" value = <?php ; ?> ></td>
 </tr>
 <tr height = 100 ><td colspan = 2 align = 'center'> <input type=<?php echo $veld; ?>  value="Opslaan" name="knpChange"></td></tr>
 </table></p>
 </form>
 
 
    </TD>
<?php
include "menuBeheer.php"; } ?>
</tr>

</table>

</body>
</html>
