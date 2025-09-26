const table_rows = document.querySelectorAll('tbody tr'), /* tbody is een element binnen test_javascript4.php */
      table_headings = document.querySelectorAll('thead th'); /* thead is een element binnen test_javascript4.php */


/******* script voor sorteren ******/
/* BRON : https://www.youtube.com/watch?v=WbkPGesI-OY */

/* table_headings.forEach((head) => { /* i kan weg als je niet door de rijen wilt heen lopen */
table_headings.forEach((head, i) => { /* head is een willekeurige naam als variabele. i is nodig als je door de rijen wilt heen lopen bijv. bij code row.querySelectorAll('td')[i] en sortTable(i); */
    let sort_asc = true;  /* Declareer variabele sort_asc met standaard waarde true */
    head.onclick = () => { /* als er op een heading wordt geklikt */
        table_headings.forEach(head => head.classList.remove('active')); /* Haalt bij alle kolomkoppen de opmaak uit class active weg */
        head.classList.add('active'); /* Hanteer de style active op de gekozen header (Zie test4_style.css) */
    
        /* document.querySelectorAll('td').forEach(td => td.classList.remove('active'))  Nodig bij code row.querySelectorAll('td')[i] */
        table_rows.forEach(row => {
            /*console.log(row.querySelectorAll('td')[i]); Zie minuut 7:00 in de bron */
            /*row.querySelectorAll('td')[i].classList.add('active') Geeft alle velden in 1 kolom de opmaak van class active. */
        })

        head.classList.toggle('asc', sort_asc) /* asc is een style class in test4_style.css) */
        sort_asc = head.classList.contains('asc') ? false : true;

        sortTable(i,sort_asc);
    }
})

function sortTable(column, sort_asc) {
    [...table_rows].sort((a,b) => { // 3 punten wordt uitgelegd op minuut 11:55
        let first_row = a.querySelectorAll('td')[column].textContent.toLowerCase(),
            second_row = b.querySelectorAll('td')[column].textContent.toLowerCase();

        /*console.log(first_row, second_row); dit toont tabblad console (F12 op de pagina) het vergelijk per regel tussen 2 regels*/
        return sort_asc ? (first_row < second_row ? -1 : 1) : (first_row < second_row ? 1 : -1); /* -1 sleept de waarde 1 positie omhoog. 1 blijft de positie het zelfde */
    })
        .map(sorted_row => document.querySelector('tbody').appendChild(sorted_row));
}

/******* script voor sorteren ******/

