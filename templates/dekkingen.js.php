<script type="text/javascript">

function toon_txtDatum(id, datum, aantal) {

var txtDrachtdm = 'drachtdatum_' + id;
var kzlDrachtig = 'drachtig_' + id;
var txtWorp = 'worp_' + id;

dracht = document.getElementById(kzlDrachtig);        var dr = dracht.value;

// if(mr.length > 0) alert(jArray_vdr[mr]);
  if(dr == 'ja') {

      document.getElementById(txtDrachtdm).style.display = "inline-block";
      document.getElementById(txtDrachtdm).value = datum;
      document.getElementById(txtWorp).style.display = "inline-block";
      if(aantal > 0) {
      document.getElementById(txtWorp).value = aantal;
      }

  }
  else
  {
      document.getElementById(txtDrachtdm).style.display = "none";
      document.getElementById(txtDrachtdm).value = null;
      document.getElementById(txtWorp).style.display = "none";
      document.getElementById(txtWorp).value = null;
  }

}



</script>
