<!-- BackToTop button javascript 
    bron : https://www.wpromotions.eu/nl/hoe-een-scroll-to-top-knop-toevoegen-aan-website-in-webnode/    -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Deze links komen uit Zoeken.php per 14-12-2024 -->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<!-- Einde Deze links komen uit Zoeken.php per 14-12-2024 -->

<script type="text/javascript">
$(document).ready(function(){
    $(window).scroll(function(){
        if($(this).scrollTop() > 100){
            $('#scroll').fadeIn();
        }else{
            $('#scroll').fadeOut();
        }
    });
    $('#scroll').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });
});
</script>

<a href="javascript;" id="scroll" title="Scroll to Top" style="display: none;">Top<span></span></a> 
<!-- Einde BackToTop button javascript -->
