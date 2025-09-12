      <script>

    function datumControle() {
    
    var x = document.getElementById("datepicker4").value;
    /*document.getElementById("demo").innerHTML = "You selected: " + x;*/
    $nietv = '12-01-2001';
    if(isset($nietv)) { 
     y = 'De datum mag niet voor ' + $nietv + ' liggen.';
        window.alert(y);
    }
    
}
</script>
