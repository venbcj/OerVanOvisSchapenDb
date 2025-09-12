<script>

function toon_dracht(id) { // id = Id uit tabel impAgrident

var ooi = 'ooi_' + id;
var moeder = document.getElementById(ooi);        var mr = moeder.value;

//alert('ooiId = ' + mr); //#/#

// if(mr.length > 0) alert(jArray_vdr[mr]);
  if(mr.length > 0) toon_vader_uit_koppel(mr, id); // mr = schaapId ooi en id = Id uit tabel impAgrident

}

 var jArray_vdr = <?php echo json_encode($array_vader_uit_koppel); // json_encode zet array om in json code ?>;

function toon_vader_uit_koppel(m, i) { // m = schaapId ooi en i = Id uit tabel impAgrident
    //document.getElementById('result_vader').innerHTML = jArray_vdr[m];

// alert('ooiId = ' + m + ' Id(impAgrident) = ' + i); //#/#
// alert('werknr vader = ' + jArray_vdr[m]); //#/#

var ram = 'ram_' + i;    // Dit verwijst naar het element kzlRam_$Id
var resultRam = 'result_ram_' + i; // Dit moet het werknr van de ram tonen na wijzigen van het moederdier
var dbRam = 'dbRam_' + i; // Dit verwijst naar het div element dbRam_$Id en toont het vaderdier na laden van de pagina

     if(jArray_vdr[m] != null) // Als een vaderdier wordt gevonden in een koppel
     {
 //        alert('vaderdier gevonden in koppel');
    document.getElementById(ram).style.display = "none";
      document.getElementById(ram).value = null; // veld leegmaken indien gevuld
      document.getElementById(resultRam).innerHTML = jArray_vdr[m];
    document.getElementById(dbRam).style.display = "none"; // Dit zorgt bij wijzigen ooi dat de oorspronkelijke ram niet wordt getoond    
    }
      else 
      {
      //document.getElementById(ram).style.display = "block";
    document.getElementById(ram).style.display = "inline-block";
    document.getElementById(resultRam).innerHTML = "";
    document.getElementById(dbRam).style.display = "none"; // Dit zorgt bij wijzigen ooi dat de oorspronkelijke ram niet wordt getoond
      }

      //alert('ram = ' + jArray_vdr[m]); #/#
}

</script>
