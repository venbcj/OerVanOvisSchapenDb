<script>
function eenheid_artikel() {

var artikel     = document.getElementById("artikel");        var artikel_v = artikel.value;


 if(artikel_v.length > 0) toon_eenheid(artikel_v);

}

 var jArray= <?php echo json_encode($array_eenheid); ?>;

function toon_eenheid(e) {
    document.getElementById('aantal').innerHTML = jArray[e];
}
</script>
