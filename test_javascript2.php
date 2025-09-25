<!DOCTYPE html>
<html>
  
<head>
 <title>
 </title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js">
</script>


<p id="toggle">
    <span> Left </span>
    <span> Right </span>
</p>

<div id="left"> LEFT CONTENT </div>
<div id="right"> RIGHT CONTENT </div>

<script>
$('#toggle > span').click(function() {
    var ix = $(this).index();

    $('#left').toggle( ix === 0 );
    $('#right').toggle( ix === 1 );
});

</script>
</body>
  
</html>  