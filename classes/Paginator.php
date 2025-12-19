<?php

define("MIN_PER_PAGE", 10);
define("MAX_PER_PAGE", 100);

class Paginator {

    private $table;
    private $condition;
    private $db;
    public $total_records; // public, want unit test. Hmm. TODO
    public $records_per_page; // records per pagina
    public $page; // Getoonde pagina nummer
    private $total_pages; // totaal aantal pagina's
    private $offset; // record waar vanaf getoond moet worden vb : 60 betekend tonen x records (per pagina) vanaf 61 (paginanr - 1 * records per pagina)

    private $base_url;

    public function __construct($table, $condition = "", $link_id = null, $current_page = null, $rpp = 60, $base_url = '') {
        $this->table = $table;
        $this->condition = $condition;
        $this->db = $link_id;
        if (is_null($link_id)) {
            $this->db = Db::instance();
        }
        $this->total_records = $this->count_records();
        $this->records_per_page = $this->determine_records_per_page($rpp);
        $this->total_pages = ceil($this->total_records / $this->records_per_page);
        $this->page = $this->determine_page($current_page);
        $this->offset = ($this->page - 1) * $this->records_per_page;
        $this->base_url = $base_url;
    }

    private function determine_records_per_page($rpp) {
        return isset($_GET['rpp']) && is_numeric($_GET['rpp']) && $_GET['rpp'] >= MIN_PER_PAGE && $_GET['rpp'] <= MAX_PER_PAGE ? $_GET['rpp'] : $rpp;
    }

    private function determine_page($current_page) {
        return isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $this->total_pages
            ? $_GET['page']
            : $current_page;
    }

    private function count_records() {
        [$where, $args] = $this->get_condition();
        $statement = <<<SQL
SELECT count(*)
FROM $this->table
$where
SQL
        ;
        $res = $this->db->run_query($statement, $args);
        if (!$res) {
            throw new Exception($this->db->error . PHP_EOL . $statement);
        }
        return $res->fetch_row()[0];
    }

    function fetch_data($fields = "*", $order = "") {
        [$where, $args] = $this->get_condition();
        $statement = <<<SQL
SELECT $fields
FROM $this->table
$where
$order
LIMIT $this->offset, $this->records_per_page
SQL
        ;
        $res = $this->db->run_query($statement, $args);
        if ($this->db->error) {
            throw new Exception($this->db->error . PHP_EOL . $statement);
        }
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    private function get_condition() {
        if (is_array($this->condition)) {
            return $this->condition;
        }
        return [$this->condition, []];
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
            if ($i == $this->records_per_page) {
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
            ? '<a href="paginas.php?page=' . ($this->page - 1) . '&amp;rpp=' . $this->records_per_page . '" title="Vorige Pagina">&laquo;&laquo;</a>' 
            : '<span style="color:#aaa">&laquo;&laquo;</span>';
        $str .= '&nbsp;&nbsp;&nbsp;';
        $str .= ($this->page < $this->total_pages) 
            ? '<a href="paginas.php?page=' . ($this->page + 1) . '&amp;rpp=' . $this->records_per_page . '" title="Volgende Pagina">&raquo;&raquo;</a>' 
            : '<span style="color:#aaa">&raquo;&raquo;</span>';
        return $str;
    }

    public function show_page_numbers($num_page_links = 7) {
        if ($this->total_pages > 1) {
            $num_page_links = $num_page_links % 2 ? $num_page_links : $num_page_links + 1;
            $output = 'Pagina: <strong>' . $this->page . '</strong> van ' . $this->total_pages . '<br />';
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
                    $output .= <<<HTML
<a href="$this->base_url?page=1&amp;rpp=$this->records_per_page" title="First Page (1)">...</a>&nbsp;
HTML;
                }
                for ($i = $start; $i <= $end; $i++) {
                    $output .= $this->link_to_unless_current($i);
                }
                if ($this->page < $this->total_pages - $cutoff) {
                    $output .= <<<HTML
<a href="$this->base_url?page=$this->total_pages&amp;rpp=$this->records_per_page" title="Last Page ($this->total_pages)">...</a>&nbsp; 
HTML;
                }
            } else {
                for ($i = 1; $i <= $this->total_pages; $i++) {
                    $output .= $this->link_to_unless_current($i);
                }
            }
            return rtrim($output);
        } else {
            return null;
        }
    }

    private function link_to_unless_current($i) {
        return ($i == $this->page)
            ? <<<HTML
<strong style="text-decoration:underline;">$i</strong>&nbsp;
HTML
        : <<<HTML
<a href="$this->base_url?page=$i&amp;rpp=$this->records_per_page" title="Go to Page $i">$i</a>&nbsp;
HTML
        . PHP_EOL
            ;
    }

}
