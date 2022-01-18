<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>


  <input type="checkbox" name="chk" id='click_2018'>
  Toon 2018
  <div id='hide_2018'>hide 1
<table>
<tr><td>rij1vld1</td><td>rij1vld2</td></tr>
<tr><td>rij2vld1</td><td>rij2vld2</td></tr>
</table>
 </div>



 

<input type="checkbox" name="chk" id='click_2019'>  
  Toon 2019
  <div id='hide_2019'>hide 2</div>


<script>
  function toggle_div(id_A, id_B) {
    for (var i = 2018; i <= 2019; i++) {
      var new_A = id_A + i;
      var new_B = id_B + i;

      (function(a, b) {
        $(a).click(function() {
          $(b).toggle();
        })
      })(new_A, new_B);
    }
  }

  toggle_div('#click_', '#hide_');
</script>