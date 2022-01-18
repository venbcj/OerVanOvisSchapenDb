<!DOCTYPE html>
<html>
  
<head>
    <title>
        How to hide an HTML element
        by class in JavaScript
    </title>
    <script src=
"https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js">
    </script>
    <style>
        body {
            text-align: center;
        }
          
        h1 {
            color: green;
        }
          
        .geeks {
            color: green;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
  
<body>
    <h1> 
        GeeksforGeeks 
    </h1>
    <b> 
        Click on button to hide the element 
        by class name
    </b>
    <br>
    <div class="outer">
        <div class="child1">Child 1</div>
        <div class="child1">Child 1</div>
        <div class="child2">Child 2</div>
        <div class="child2">Child 2</div>
    </div>
    <br>
    <button onClick="GFG_Fun()">
        click here
    </button>
    <p id="geeks">
    </p>
    <script>
        var down = document.getElementById('GFG_DOWN');
  
        function GFG_Fun() {
            document.getElementsByClassName('child1')[1].
            style.visibility = 'hidden';
            down.innerHTML = "Element is hidden";
        }
    </script>
</body>
  
</html>    