<script language="javascript">
$(function(){

     // add multiple select / deselect functionality
     $("#selectall").click(function () {
              $('.checkall').attr('checked', this.checked);
     });

     // if all checkbox are selected, check the selectall checkbox
     // and viceversa
     $(".checkall").click(function(){

            if($(".checkall").length == $(".checkall:checked").length) {
                 $("#selectall").attr("checked", "checked");
            } else {
                 $("#selectall").removeAttr("checked");
            }

     });

    // add multiple select / deselect functionality
    $("#selectall_del").click(function () {
          $('.delete').attr('checked', this.checked);
    });

    // if all checkbox are selected, check the selectall_del checkbox
    // and viceversa
    $(".delete").click(function(){

        if($(".delete").length == $(".delete:checked").length) {
            $("#selectall_del").attr("checked", "checked");
        } else {
            $("#selectall_del").removeAttr("checked");
        }

    });

    // uit InsAfvoer
    // add multiple select / deselect functionality
    $("#selectall_kg").click(function () {
          $('.weight').attr('checked', this.checked);
    });

    // if all checkbox are selected, check the selectall_del checkbox
    // and viceversa
    $(".weight").click(function(){

        if($(".weight").length == $(".weight:checked").length) {
            $("#selectall_kg").attr("checked", "checked");
        } else {
            $("#selectall_kg").removeAttr("checked");
        }

    });
});
</script>
