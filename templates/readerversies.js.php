<script type="text/javascript">
<?php if (isset($last_versieId)) { ?>
var cur_versie = <?php echo $last_versieId; ?> ;
$('.' + cur_versie).toggle();
<?php } ?>
    $(document).ready(function() {
        $('input[type="checkbox"]').click(function() {
            var inputValue = $(this).attr("value");
            $("." + inputValue).toggle();
        });
    });

</script>
