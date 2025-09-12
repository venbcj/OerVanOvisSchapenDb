<script type="text/javascript">

$(document).ready(function() {
        $('td').click(function() {
            var inputValue = $(this).attr("value");
            //alert(inputValue);
            $("." + inputValue).toggle();
        });
    });

</script>
