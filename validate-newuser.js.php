<script>
function verplicht() {
var rnaam     = document.getElementById("voornaam");         var rnaam_v = rnaam.value;
var anaam     = document.getElementById("achternaam");        var anaam_v = anaam.value;
var telf     = document.getElementById("telefoon");            var telf_v  = telf.value;
var ubn      = document.getElementById("ubn");                var ubn_v     = +ubn.value; ubn_w = ubn.value;
var relatnr  = document.getElementById("relatienummer");    var relatnr_v  = +relatnr.value;

     if(rnaam_v.length == 0) rnaam.focus() + alert("Roepnaam is onbekend.");
else if(rnaam_v.length > 25) rnaam.focus() + alert("Roepnaam mag maximaal 25 karakters zijn.");
else if(anaam_v.length == 0) anaam.focus() + alert("Achternaam is onbekend.");
else if(anaam_v.length > 25) anaam.focus() + alert("Achternaam mag maximaal 25 karakters zijn.");
else if(telf_v.length > 11)  telf.focus()  + alert("Telefoonnummer mag max 11 karakters zijn.");
else if(ubn_w.length == 0)      ubn.focus()   + alert("Ubn is onbekend.");
else if(isNaN(ubn_v))           ubn.focus()   + alert("Ubn is niet numeriek.");
else if(isNaN(relatnr_v))       relatnr.focus()  + alert("Relatienummer is niet numeriek.");

}
</script>
