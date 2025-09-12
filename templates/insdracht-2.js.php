<script language="javascript">

var jArray_Id = <?php echo json_encode($array_readId); ?>;

for (let i = 0; i < jArray_Id.length; i++) {

var ram = 'ram_' + jArray_Id[i];

    document.getElementById(ram).value = null; // veld leegmaken indien gevuld
    $('.' + jArray_Id[i]).toggle();
}
</script>
