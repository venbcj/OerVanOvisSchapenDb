<?php

define("MIN_PER_PAGE", 10);
define("MAX_PER_PAGE", 100);

class Paginator {

    private $table;
    private $condition;
    private $link_id;
    public $total_records; // public, want unit test. Hmm. TODO
    public $rpp; // records per pagina
    public $page; // Getoonde pagina nummer
    private $total_pages; // totaal aantal pagina's
    private $offset; // record waar vanaf getoond moet worden vb : 60 betekend tonen x records (per pagina) vanaf 61 (paginanr - 1 * records per pagina)

    private $base_url;

    public function __construct($table, $condition = "", $link_id = null, $paginasessie = null, $rpp = 60, $base_url = '') {
        $this->table = $table;
        $this->condition = $condition;
        $this->link_id = $link_id;
        $this->total_records = $this->count_records();
        $this->rpp = $this->determine_records_per_page($rpp);
        $this->total_pages = ceil($this->total_records / $this->rpp);
        $this->page = $this->determine_page($paginasessie);
        $this->offset = ($this->page - 1) * $this->rpp;
        $this->base_url = $base_url;
    }

    private function determine_records_per_page($rpp) {
        return isset($_GET['rpp']) && is_numeric($_GET['rpp']) && $_GET['rpp'] >= MIN_PER_PAGE && $_GET['rpp'] <= MAX_PER_PAGE ? $_GET['rpp'] : $rpp;
    }

    private function determine_page($paginasessie) {
        return isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $this->total_pages
            ? $_GET['page']
            : $paginasessie;
    }

    private function count_records() {
        $res = mysqli_query($this->link_id, "SELECT count(*) tot FROM " . $this->table . " " . $this->condition);
        if (!$res) {
            throw new Exception(mysqli_error($this->link_id));
        }
        while ($row = mysqli_fetch_assoc($res)) {
            $k = $row['tot'];
        }
        return $k;
    }

    function fetch_data($fields = "*", $order = "") {
        $statement = "SELECT " . $fields . " FROM " . $this->table . " " . $this->condition . " " . $order . " LIMIT " . $this->offset . "," . $this->rpp;
        $res = mysqli_query($this->link_id, $statement);
        if (mysqli_error($this->link_id)) {
            throw new Exception(mysqli_error($this->link_id) . PHP_EOL . $statement);
        }
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        if (isset($data)) {
            return $data;
        }
    }


    public function show_rpp() {
        $str = <<<JS
<script>
        function openUrl() {
            var control = document.getElementById('rpp');
            window.location = "$this->base_url?page=1&rpp="+control.options[control.selectedIndex].value;
        }
        </script>
JS
        . <<<HTML
        <select id="rpp" onchange="openUrl();">
HTML;
        for ($i = MIN_PER_PAGE; $i <= MAX_PER_PAGE; $i += 10) {
            $selected = '';
            if ($i == $this->rpp) {
                $selected = ' selected="selected"';
            }
            $str .= <<<HTML
<option value="$i"$selected>$i</option>

HTML;
        }
        $str .= <<<HTML
</select>
HTML;
        return $str;
    }

    // wordt niet aangeroepen
    public function prev_next() {
        $str = ($this->page > 1) 
            ? '<a href="paginas.php?page=' . ($this->page - 1) . '&amp;rpp=' . $this->rpp . '" title="Vorige Pagina">&laquo;&laquo;</a>' 
            : '<span style="color:#aaa">&laquo;&laquo;</span>';
        $str .= '&nbsp;&nbsp;&nbsp;';
        $str .= ($this->page < $this->total_pages) 
            ? '<a href="paginas.php?page=' . ($this->page + 1) . '&amp;rpp=' . $this->rpp . '" title="Volgende Pagina">&raquo;&raquo;</a>' 
            : '<span style="color:#aaa">&raquo;&raquo;</span>';
        return $str;
    }

    public function show_page_numbers($num_page_links = 7) {
        if ($this->total_pages > 1) {
            $num_page_links = $num_page_links % 2 ? $num_page_links : $num_page_links + 1;
            $pagenumbers = 'Pagina: <strong>' . $this->page . '</strong> van ' . $this->total_pages . '<br />';
            if ($this->total_pages > $num_page_links) {
                $cutoff = floor($num_page_links / 2);
                $start = $this->page - $cutoff;
                $end   = $this->page + $cutoff;
                // BCB: comments indicate refactoring opportunities... do Extract Method
                /********
                *    No Pagenumbers Less then 1 && Greater then total_pages
                *    BCB: adjust start/end to sit within 1 and total_pages
                ********/
                while ($start < 1) {
                    $start++;
                    $end++;
                }
                while ($end > $this->total_pages) {
                    $start--;
                    $end--;
                }
                /********
                *    Pagina nummers opmaken en uitspugen
                *    BCB: create output
                ********/
                if ($this->page > $cutoff + 1) {
                    $pagenumbers .= '<a href="' . $this->base_url . '?page=1&amp;rpp=' . $this->rpp . '" title="First Page (1)">...</a>&nbsp; ';
                }
                for ($i = $start; $i <= $end; $i++) {
                    $pagenumbers .= ($i == $this->page) ? '<strong style="text-decoration:underline;">' . $i . '</strong>&nbsp; ' . "\r\n" : '<a href="' . $this->base_url . '?page=' . $i . '&amp;rpp=' . $this->rpp . '" title="Go to Page ' . $i . '">' . $i . '</a>&nbsp; ' . "\r\n";
                }
                if ($this->page < $this->total_pages - $cutoff) {
                    $pagenumbers .= '<a href="' . $this->base_url . '?page=' . $this->total_pages . '&amp;rpp=' . $this->rpp . '" title="Last Page (' . $this->total_pages . ')">...</a>&nbsp; ';
                }
            } else {
                for ($i = 1; $i <= $this->total_pages; $i++) {
                    $pagenumbers .= ($i == $this->page) ? '<strong style="text-decoration:underline;">' . $i . '</strong>&nbsp; ' . "\r\n" : '<a href="' . $this->base_url . '?page=' . $i . '&amp;rpp=' . $this->rpp . '" title="Go to Page ' . $i . '">' . $i . '</a>&nbsp; ' . "\r\n";
                }
            }
            return rtrim($pagenumbers);
        } else {
            return null;
        }
    }

}
