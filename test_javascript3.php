<!DOCTYPE html>
<html>
  
<head>
    <title>
        How to hide an HTML element
        by class in JavaScript
    </title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js">
</script>

    <script>

function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("myTable");
  switching = true;
  // Set the sorting direction to ascending:
  dir = "asc";
  /* Make a loop that will continue until
  no switching has been done: */
  while (switching) {
    // Start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /* Loop through all table rows (except the
    first, which contains table headers): */
    for (i = 1; i < (rows.length - 1); i++) {
      // Start by saying there should be no switching:
      shouldSwitch = false;
      /* Get the two elements you want to compare,
      one from current row and one from the next: */
      x = rows[i].getElementsByTagName("TD")[n];

//x = convertDate(x);
      var xx = x.innerHTML.toLowerCase();
     // var xxx = convertDate(xx);

      y = rows[i + 1].getElementsByTagName("TD")[n];

      var yy = y.innerHTML.toLowerCase();
      //var yyy = convertDate(yy);

//y = convertDate(y);
      /* Check if the two rows should switch place,
      based on the direction, asc or desc: */
      if (dir == "asc") {
        if (xx > yy) {
            
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          //alert(shouldSwitch);

          break;
        }
      } else if (dir == "desc") {
        if (xx < yy) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /* If a switch has been marked, make the switch
      and mark that a switch has been done: */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      // Each time a switch is done, increase this count by 1:
      switchcount ++;
    } else {
      /* If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again. */
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }

//toon_teken();
//alert(rows[5]);
}



function myFunction1() {
  var x = document.getElementById("kolom1");
  x.style.display = "none";

  var xu = document.getElementById("kolom1_up");
  if (xu.style.display === "none") {
    xu.style.display = "block";
  } else {
    xu.style.display = "none";
  }

  var xd = document.getElementById("kolom1_down");
  if (xd.style.display === "block") {
    xd.style.display = "none";
  } else {
    xd.style.display = "block";
  }

 var y = document.getElementById("kolom2");
  y.style.display = "block";
var yu = document.getElementById("kolom2_up");
  xu.style.display = "none";
  var yd = document.getElementById("kolom2_up");
  yd.style.display = "none";
}


function myFunction2() {
  var y = document.getElementById("kolom2");
  y.style.display = "none";

  var yu = document.getElementById("kolom2_up");
  if (yu.style.display === "none") {
    yu.style.display = "block";
  } else {
    yu.style.display = "none";
  }

  var yd = document.getElementById("kolom2_down");
  if (yd.style.display === "block") {
    yd.style.display = "none";
  } else {
    yd.style.display = "block";
  }


}


    </script>

    <script type="text/javascript">

    </script>
    <style>
Thead th span.icon-arrow {
    display: inline-block;
    width: 1.3rem;
    height: 1.3rem;
    color: #6c00bd;
    border: 1.4px solid #6c00bd;
    border-radius: 50%;

    text-align: center;
    font-size: 1rem;

    margin-left: .5rem;
}

        
/*#kolom1 {
  display: none;
}*/
#kolom1_up {
  display: none;
}
#kolom1_down {
  display: none;
}

/*#kolom2_up {
  display: none;
}
  #kolom2_down {
  display: none;
}*/

/* // bron : https://stackoverflow.com/questions/4936715/how-to-toggle-between-two-divs
#UP { display:none; }*/
#DOWN { display:none; }
    </style>
</head>
  
<body>
    <main class="table">
        <section class="table_header">
            <h1>Customer's Orders</h1>
            <div class="input-group">
                <input type="search" placeholder="Zoek naar iets .....">
                <img src="images/search.png" alt="">
            </div>
        </section>        
        <section class="table_body">
            <table>
                <thead>
                    <tr>
                        <th> Id <span class="icon-arrow">&UpArrow;</span> </th>
                        <th> Customer <span class="icon-arrow">&UpArrow;</span> </th>
                        <th> Location <span class="icon-arrow">&UpArrow;</span> </th>
                        <th> Order Date <span class="icon-arrow">&UpArrow;</span> </th>
                        <th> Amount <span class="icon-arrow">&UpArrow;</span> </th>
                        <th>  </th>
                    </tr>
                </thead>
            </table>
        </section>
    </main>

<p>Click the "Try it" button to toggle between hiding and showing the DIV element:</p>

<button onclick="myFunction1()">Try it 1</button>
<button onclick="myFunction2()">Try it 2</button>
<table border = 2 id="myTable" >
    <thead>
<tr>
 <th width="100" id="kolom1" onclick="sortTable(0)" > Kolom 1 <span class="icon-arrow">&UpArrow; </span> </th>
 <th width="100" id="kolom1_up" onclick="sortTable(0)" > Kolom 11 &#8593; </th>
 <th width="100" id="kolom1_down" onclick="sortTable(0)" > Kolom 1 &#8595; </th>

 <th width="100" id="kolom2" onclick="sortTable(1)" > Kolom 2 </th>
 <th width="100" id="kolom2_up" onclick="sortTable(1)" > Kolom 2 &#8593; </th>
 <th width="100" id="kolom2_down" onclick="sortTable(1)" > Kolom 2 &#8595; </th>
</tr>
    </thead>

<tr>
 <td> 1 </td>
 <td> 14 </td>
</tr>
<tr>
 <td> 2 </td>
 <td> 11 </td>
</tr>
<tr>
 <td> 3 </td>
 <td> 12 </td>
</tr>
<tr>
 <td> 4 </td>
 <td> 15 </td>
</tr>
<tr>
 <td> 5 </td>
 <td> 13 </td>
</tr>

</table>

<button onClick="klik_kolomkop()">
        click here
    </button>

<p id="GFG_DOWN" style="color: green;">
    </p>

<p><b>Note:</b> The element will not take up any space when the display property 
is set to "none".</p>

<button onclick="myToggleFunction()">Probeer het</button>

<div id="myDIV">
&#8593
</div>


<!-- bron : https://stackoverflow.com/questions/4936715/how-to-toggle-between-two-divs -->
<p id="toggle">
    <span> Left </span>
  <!--  <span> Right </span> -->
</p>

<div id="UP"> Kolom 1 &#8593 </div>
<div id="DOWN"> Kolom 1 &#8595 </div>










<script>
    function klik_kolomkop() {

    var x = document.getElementById("kolom1");
    var xu = document.getElementById("kolom1_up");
 
    x.style.display = "none";
    xu.style.toggle();
    //x.style.display = "none";

}

function Toon_teken() {
           
            $('#GFG_DOWN').text("Kolom &#8593");  

        }

function myToggleFunction() {
   var x = document.getElementById("myDIV");
   if (x.innerHTML === "&#8593") {
    x.innerHTML = "&#8595";
    } else {
      x.innerHTML = "&#8593";
    }
}


// bron : https://stackoverflow.com/questions/4936715/how-to-toggle-between-two-divs
$('#toggle > span').click(function() {
    var ix = $(this).index();

    $('#UP').toggle();
    $('#DOWN').toggle();
});


</script>
</body>
  
</html>    