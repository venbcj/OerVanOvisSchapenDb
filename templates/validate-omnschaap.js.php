
<script>
function verplicht() {
var levnr = document.getElementById("levnr");         var levnr_v = levnr.value;
var datum = document.getElementById("datepicker1");    var datum_v = datum.value;

     if(levnr_v.length == 0) levnr.focus()     + alert("Het nieuwe levensnummer moet zijn ingevuld.");
else if(levnr_v.length > 0 && levnr_v.length != 12) levnr.focus()     + alert("Het levensnummer moet uit 12 cijfers bestaan.");
else if(isNaN(levnr_v)) levnr.focus()     + alert("Het levensnummer bevat een letter.");
else if(datum_v.length == 0) datum.focus()     + alert("De datum moet zijn ingevuld.");

}

</script>
