<script>
function verplicht() {

var datum = document.getElementById("datepicker1");     var datum_v = datum.value;
var artikel    = document.getElementById("artikel");        var artikel_v = artikel.value;
var hoeveelheid     = document.getElementById("hoeveelheid");        var hoeveelheid_v = hoeveelheid.value;
var prijs     = document.getElementById("prijs");        var prijs_v = prijs.value;


/*artikel.focus() +*/ //alert('datum_v.length = ' + datum_v);
     if(datum_v.length == 0) datum.focus() + alert("Datum is onbekend.");
else if(artikel_v.length == 0) artikel.focus() + alert("Omschrijving is onbekend.");
else if(hoeveelheid_v.length == 0) hoeveelheid.focus() + alert("Het inkoopaantal is onbekend.");
else if(prijs_v.length == 0) prijs.focus() + alert("De prijs is onbekend.");

}


function eenheid_artikel() {

var artikel     = document.getElementById("artikel");        var artikel_v = artikel.value;


 if(artikel_v.length > 0) { toon_eenheid(artikel_v); }
 else { removeElement(artikel_v); }

}

 var jArray= <?php echo json_encode($array_eenheid); ?>;

function toon_eenheid(e) {
    document.getElementById('aantal').innerHTML = jArray[e] + '&nbsp &nbsp ';
}

function removeElement(e) {
     document.getElementById('aantal').innerHTML = '';
}

</script>
