<script type="text/javascript">
var cur_year = new Date().getFullYear();

//$('.' + cur_year + '.selectt').toggle();
$('.' + cur_year).toggle();



    $(document).ready(function() {
        $('input[type="checkbox"]').click(function() {
            var inputValue = $(this).attr("value");
            //alert(inputValue);
            $("." + inputValue).toggle();
        });
    });



var jArray_Id = <?php echo json_encode($array_drachtdatum); ?>;

for (let i = 0; i < jArray_Id.length; i++) {

    //alert(i);

var drachtdm = 'drachtdatum_' + jArray_Id[i];

    document.getElementById(drachtdm).value = null; // veld leegmaken indien gevuld
    $('.' + jArray_Id[i]).toggle();
}



</script>
