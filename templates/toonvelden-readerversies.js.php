        <script type="text/javascript">
        function toon_velden(id) {
            var chbVersie = 'versieChb_' + id;
            var lblReaderApp = 'readerApp_' + id;
            versiekeuze = document.getElementById(chbVersie);        var vk = versiekeuze.value;
            // if(mr.length > 0) alert(jArray_vdr[mr]);
            if(vk == id) {
                document.getElementById(lblReaderApp).style.display = "inline-block";
                //document.getElementById(txtDrachtdm).value = datum;
                //document.getElementById(txtWorp).style.display = "inline-block";
            } else {
                document.getElementById(lblReaderApp).style.display = "none";
                //document.getElementById(txtDrachtdm).value = null;
                //document.getElementById(txtWorp).style.display = "none";
                //document.getElementById(txtWorp).value = null;
            }
        }
        </script>
