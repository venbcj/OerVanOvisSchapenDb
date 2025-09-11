<!doctype html>
<html lang="en">
<head>
<title> How to Highlight Particular Dates in JQuery UI Datepicker </title>
<meta charset="utf-8">
<link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-1.7.min.js" > </script>
<script src="js/jquery-ui-1.8.16.custom.min.js" > </script>
<style>
* {
    margin: 0 auto;
    padding: 0;
}

body {
    background-color: #F2F2F2;
}

.container {
    margin: 0 auto;
    width: 920px;
    padding: 50px 20px;
    background-color: #fff;
}

h3 {
    text-align: center;
}

#calendar {
    margin-top: 40px;
}
    
.event a {
    background-color: #42B373 !important;
    background-image :none !important;
    color: #ffffff !important;
}
</style>
<script type="text/javascript">
    jQuery(document).ready(function() {
        
        // An array of dates
        var eventDates = {};
        eventDates[ new Date( '12/04/2014' )] = new Date( '12/04/2014' );
        eventDates[ new Date( '12/06/2014' )] = new Date( '12/06/2014' );
        eventDates[ new Date( '12/20/2014' )] = new Date( '12/20/2014' );
        eventDates[ new Date( '12/25/2014' )] = new Date( '12/25/2014' );
        
        // datepicker
        jQuery('#calendar').datepicker({
            beforeShowDay: function( date ) {
                var highlight = eventDates[date];
                if( highlight ) {
                     return [true, "event", "highlight"];
                } else {
                     return [true, '', ''];
                }
             }
        });
    });
</script>
</head>
<body>
    <div class="container">
        <h3> Highlight Particular Dates in JQuery UI Datepicker </h3>
        
        <div id="calendar" > </div>
    </div>
</body>
</html>