<script>
function verplicht() {
var naam = document.getElementById("artikel");         var naam_v = naam.value;
var stdat  = document.getElementById("standaard");    var stdat_v = stdat.value;
var eenheid = document.getElementById("eenheid");        var eenheid_v = eenheid.value;
var btw   = document.getElementById("btw");                    var btw_v = btw.value;


     if(naam_v.length == 0) naam.focus()     + alert("De omschrijving ontbreekt.");
else if(stdat_v.length == 0) stdat.focus()     + alert("Het standaard aantal moet zijn ingevuld.");
else if(eenheid_v.length == 0 ) eenheid.focus()     + alert("De eenheid moet zijn ingevuld.");
else if(btw_v.length == 0 ) btw.focus()     + alert("De btw moet zijn ingevuld.");

}

</script>
