<script type="text/javascript">
function verplicht_bij_zoeken() {
var levnr = document.getElementById("levnr");         var levnr_v = levnr.value;

if(levnr_v.length == 0) levnr.focus()     + alert("Levensnummer moet zijn ingevuld.");
}

function verplicht() {
var levnr = document.getElementById("levnr");         var levnr_v = levnr.value;
var fase  = document.getElementById("fase");        var fase_v = fase.value;
var sekse = document.getElementById("sekse");        var sekse_v = sekse.value;
var gebdm = document.getElementById("datepicker1");    var gebdm_v = gebdm.value;
var gewicht = document.getElementById("gewicht");    if(gewicht) { var gewicht_v = gewicht.value; } // bij modtech bestaat variable gewicht niet. Daardoor werkt deze functie niet.
var verblijf = document.getElementById("verblijf");    if(verblijf) { var verblijf_v = verblijf.value; } // bij modtech bestaat variable verblijf niet. Daardoor werkt deze functie niet.
var moment = document.getElementById("moment");        var moment_v = moment.value;
var uitvdm = document.getElementById("datepicker3"); var uitvdm_v = uitvdm.value;
var reden = document.getElementById("reden");         var reden_v = reden.value;
var aanvdm = document.getElementById("datepicker2"); var aanvdm_v = aanvdm.value;


     if(levnr_v.length > 0 && levnr_v.length != 12) levnr.focus()     + alert("Het levensnummer moet uit 12 cijfers bestaan.");

else if(isNaN(levnr_v)) levnr.focus()     + alert("Het levensnummer bevat een letter.");

else if(fase_v.length == 0) fase.focus()     + alert("Generatie moet zijn ingevuld.");

else if((fase_v == 'moeder' && sekse_v == 'ram') || (fase_v == 'vader' && sekse_v == 'ooi')) fase.focus()     + alert("Geslacht en generatie zijn tegenstrijdig !");

else if(window.getComputedStyle(gebdm).display === "inline-block" && gebdm_v.length == 0 && fase_v == 'lam') gebdm.focus()     + alert("De geboortedatum moet zijn ingevuld.");

else if(levnr_v.length > 0 && gewicht_v.length == 0 && fase_v == 'lam' && moment_v.length == 0 && uitvdm_v.length == 0 && reden_v.length == 0) gewicht.focus()     + alert("Het gewicht moet zijn ingevuld.");

else if(verblijf_v.length > 0 && (moment_v.length > 0 || uitvdm_v.length > 0 || reden_v.length > 0))  verblijf.focus()     + alert("U kunt geen dood schaap in een verblijf plaatsen !");

else if(fase_v == 'lam' && aanvdm_v.length > 0)  aanvdm.focus()  + alert("Alleen volwassen dieren kunnen worden aangekocht.");

else if(fase_v != 'lam' && gewicht_v.length > 0)  gewicht.focus()  + alert("Bij invoer van een volwassen dier mag geen gewicht worden ingevoerd.");

}

function kies_generatie() {

var fase  = document.getElementById("fase");        var fase_v = fase.value;

if(fase_v.length == 0) fase.focus()     + alert("Kies eerst een generatie.");

}

function toon_dracht() {

var moeder = document.getElementById("moeder");        var moeder_v = moeder.value;


 if(moeder_v.length > 0) toon_vader_uit_koppel(moeder_v); toon_werpdatum(moeder_v);

}

 var jArray_vdr = <?php echo json_encode($array_vader_uit_koppel); ?>;

function toon_vader_uit_koppel(m) {
    //document.getElementById('result_vader').innerHTML = jArray_vdr[m];

    var fase = document.getElementById("fase");        var fase_v = fase.value;

     if(jArray_vdr[m] != null && fase_v == 'lam')
     {
    document.getElementById('vader').style.display = "none";
      document.getElementById('vader').value = null; // veld leegmaken indien gevuld
      document.getElementById('result_vader').innerHTML = jArray_vdr[m];
    }
      else 
      {
      //document.getElementById('vader').style.display = "block";
    document.getElementById('vader').style.display= "inline-block";
    document.getElementById('result_vader').innerHTML = "";
      }
}

var jArray_worp = <?php echo json_encode($array_worp); ?>;

function toon_werpdatum(m) {

  var fase = document.getElementById("fase");        var fase_v = fase.value;

  if(jArray_worp[m] != null && fase_v == 'lam')
  {
  document.getElementById('datepicker1').style.display = "none";
  document.getElementById('datepicker1').value = null; // veld leegmaken indien gevuld
  document.getElementById('result_werpdatum').innerHTML = jArray_worp[m];
  document.getElementById('bijschrift').innerHTML = "";
  }
  else
  {
  document.getElementById('datepicker1').style.display = "inline-block";
  document.getElementById('result_werpdatum').innerHTML = "";
  document.getElementById('bijschrift').innerHTML = "&nbsp* / **";
  }
}
</script>
