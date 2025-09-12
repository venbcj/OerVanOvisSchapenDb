<script>
/* SORTEREN TABEL Bron : https://www.youtube.com/watch?v=av5wFcAtuEI */
    let sortOrder = []; // creëer een array met de naam sortOrder

    function sortTable(columnIndex) {
        const table = document.getElementById('sortableTable'),
              tbody = table.querySelector('tbody'),
              tbody_1 = document.getElementById('tbody_1'),
              rows = Array.from(tbody_1.querySelectorAll('tr'));

        // TOGGLE BETWEEN ASCENDING AND DESCENDING ORDER
        sortOrder[columnIndex] = (sortOrder[columnIndex] === 'asc') ? 'desc' : 'asc';

        // UPDATE ARROW INDICATORS IN THE HEADER
        for (let i = 0; i < table.rows[0].cells.length; i++) {
            const arrow = document.getElementById('arrow' + i);
            arrow.className = (i === columnIndex) ? sortOrder[columnIndex] : 'inactive';
        }

        // SORT THE ROWS BASED ON THE CONTENT OF THE SELECTED COLUMN
        rows.sort((a,b) => {
            const aValue = a.children[columnIndex].textContent.trim(),
                  bValue = b.children[columnIndex].textContent.trim();
            return sortOrder[columnIndex] === 'asc'
            ? aValue.localeCompare(bValue, undefined, {numeric: true, sensitivity: 'base'})
            : bValue.localeCompare(aValue, undefined, {numeric: true, sensitivity: 'base'});
        });

        // CLEAR THE EXISTING TABLE BODY
        tbody.innerHTML = '';

        // APPEND THE SORTED ROWS TO THE TABLE BODY
        rows.forEach(row => tbody.appendChild(row));
    }

    function sortTable_2(columnIndex) {
        const table = document.getElementById('sortableTable_2'),
              tbody = table.querySelector('tbody'),
              tbody_2 = document.getElementById('tbody_2'),
              rows = Array.from(tbody_2.querySelectorAll('tr'));

        // TOGGLE BETWEEN ASCENDING AND DESCENDING ORDER
        sortOrder[columnIndex] = (sortOrder[columnIndex] === 'asc') ? 'desc' : 'asc';

        // UPDATE ARROW INDICATORS IN THE HEADER
        for (let i = 0; i < table.rows[0].cells.length; i++) {
            const arrow = document.getElementById('arrow2_' + i);
            arrow.className = (i === columnIndex) ? sortOrder[columnIndex] : 'inactive';
        }

        // SORT THE ROWS BASED ON THE CONTENT OF THE SELECTED COLUMN
        rows.sort((a,b) => {
            const aValue = a.children[columnIndex].textContent.trim(),
                  bValue = b.children[columnIndex].textContent.trim();
            return sortOrder[columnIndex] === 'asc'
            ? aValue.localeCompare(bValue, undefined, {numeric: true, sensitivity: 'base'})
            : bValue.localeCompare(aValue, undefined, {numeric: true, sensitivity: 'base'});
        });

        // CLEAR THE EXISTING TABLE BODY
        tbody.innerHTML = '';

        // APPEND THE SORTED ROWS TO THE TABLE BODY
        rows.forEach(row => tbody.appendChild(row));
    }
/* Einde SORTEREN TABEL */
</script>
