<script type="text/javascript">
var cur_versie = <?php echo $last_versieId; ?> ; // gedeclareerd in login.php

//$('.' + cur_versie + '.selectt').toggle();
$('.' + cur_versie).toggle();



    $(document).ready(function() {
        $('input[type="checkbox"]').click(function() {
            var inputValue = $(this).attr("value");
            //alert(inputValue);
            $("." + inputValue).toggle();
        });
    });

</script>
