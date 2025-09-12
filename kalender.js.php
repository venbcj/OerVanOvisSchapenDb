  <script>



$(document).ready(function() {

  var options = {
    dateFormat: "dd-mm-yy",
    dayNamesMin: [ "zo", "ma", "di", "wo", "do", "vr", "za" ],
    monthNames: [ "Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December" ]
    }
        
    $('#datepicker1').datepicker(options);
    $('#datepicker2').datepicker(options);
    $('#datepicker3').datepicker(options);
    $('#datepicker4').datepicker(options);
    $('#datepicker5').datepicker(options);
    $('#datepicker6').datepicker(options);

      /*$('#datepicker2').datepicker({
      dateFormat: "dd-mm-yy",
      dayNamesMin: [ "zo", "ma", "di", "wo", "do", "vr", "za" ],
      monthNames: [ "Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December" ]
    });*/
    
    
});

  </script>
