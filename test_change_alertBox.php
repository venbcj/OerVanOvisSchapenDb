<?php

 ?>



   <style>

    p {
      width: 1200px;
      font-size: 14px;
      color: blue;
    }
    </style>

      <script type="text/javascript">

  $( function() {
    $( "#dialog" ).dialog();
  } );

  </script>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
.alert {
  padding: 20px;
  margin-left: 150px;
  width: 600px;
  background-color: #9EB368;
  
}

.closebtn {
  margin-left: 15px;
  font-weight: bold;
  float: right;
  font-size: 32px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}

.closebtn:hover {
  color: black;
}
</style>
</head>
<body>

<h2>Alert Messages</h2>

<p>Click on the "x" symbol to close the alert message.</p>
<!-- <div class="alert">
  <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
  <strong>Danger!</strong> Indicates a dangerous or potentially negative action.
</div> -->

<div class="alert">
  <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
  <strong>Uitleg schaapinfo </strong> <br> <p id="demo"></p>
</div>

<script>
  z = "In de readerApp zit een knop 'Schaapinfo inlezen'. Met deze knop wordt een databse ingelezen in de reader met schaap gegevens. Bij het scannen van een schaap worden de volgende gegevens van dat schaap getoond op de reader: Geslacht : Dit is het geslacht van het schaap \n Ras : Dit is het ras van het schaap \r\n Laatst gedekt : Als het een ooi betreft wordt hier de laatste dekdatum getoond mits deze bestaat \n Laatste dekram : Dit is het werknr van de ram bij de laatste dekking van de gescande ooi \n";

document.getElementById("demo").innerHTML = z;
  </script>
</body>
</html>

