<?php

/********************   MEERDERE PAGINA'S  ********************/
/********
*    Define Some vars
********/
define("MIN_PER_PAGE", 10); 
define("MAX_PER_PAGE", 100);
define("DEF_PER_PAGE", $RPP); //Standaard aantal per pagina in eerste instantie bepaald in login.php
define("OFF_PER_PAGE", 100); //
define("THIS_PAGE", $_SERVER['PHP_SELF']); // Geeft de pagina naam terug bijv. /Schapendb/InsGeboortes.php bron : https://www.w3schools.com/php/php_form_validation.asp


/********
*    CLASS PAGE NUMBERS
********/

class Page_numbers
{
    var $table;
    var $condition;
    var $link_id;
    var $total_records;
    var $rpp; // records per pagina
    var $total_pages; // totaal aantal pagina's
    var $page; // Getoonde pagina nummer
    var $offset; // record waar vanaf getoond moet worden vb : 60 betekend tonen x records (per pagina) vanaf 61 (paginanr - 1 * records per pagina)
    var $query_string;


    /********
    *    Constructor, setting some vars
    ********/
    function Page_numbers($table, $condition="", $link_id=NULL, $paginasessie)
    {
        $this->table = $table;
        $this->condition = $condition;
        $this->link_id = $link_id;
        $this->total_records = $this->count_records();
        $this->rpp = isset($_GET['rpp']) && is_numeric($_GET['rpp']) && $_GET['rpp'] >= MIN_PER_PAGE && $_GET['rpp'] <= MAX_PER_PAGE ? $_GET['rpp'] : DEF_PER_PAGE;
        $this->total_pages = ceil($this->total_records / $this->rpp);
        $this->page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $this->total_pages ? $_GET['page'] : $paginasessie;
        $this->offset = ($this->page - 1) * $this->rpp;
        //$this->query_string = $this->get_query_string();
       // $this->pagina_string = $this->get_pagina_string();
    }


    /********
    *    Records tellen voor het uitrekenen van de pagenumbers
    ********/
    function count_records()
    {
        $res = @mysqli_query($this->link_id,"SELECT count(*) tot FROM ".$this->table." ".$this->condition);
        while($row = mysqli_fetch_assoc($res))
        {
            $k = $row['tot'];
        }
       // $k = mysqli_result($res, 0, "count(*)");
        return $k;
    }


    /********
    *    Data uit de database trekken van het pagenummer waar we op zitten
    ********/
    function fetch_data($fields="*", $order="")
    {
        $res = @mysqli_query($this->link_id,"SELECT ".$fields." FROM ".$this->table." ".$this->condition." ".$order." LIMIT ".$this->offset.",".$this->rpp);
        while($row = mysqli_fetch_assoc($res))
        {
            $data[] = $row;
        }
        if(isset($data)) { return $data; } // als alle records zijn ingelezen (inlezen reader) bestaat $data niet meer !!
    }


    /********
    *    query sting opmaken
    ********/
    function get_query_string($query_string="")
    {
        foreach($_GET as $key => $value)
        {
            if($key != 'page' && $key != 'rpp')
            {
                $query_string .= '&amp;'.$key.'='.$value;
            }
        }
        return $query_string;
    }

     /********
    *    paginanummer string opmaken
    ********/
    function get_pagina_string($pagina_string="")
    {
        foreach($_GET as $key => $value)
        {
            if($key == 'page')
            {
                $pagina_string .= '&amp;'.$key.'='.$value;
            }
        }
        return $pagina_string;
    }


    /********
    *    Message Per Page opmaken en terug geven. Keuzelijst aantal records per pagina
    ********/
    function show_rpp()
    {
        $str = '<script type="text/javascript" language="javascript1.5">
        
        function openUrl()
        {
            var control = document.getElementById(\'rpp\');
            window.location = "'.THIS_PAGE.'?page=1&rpp="+control.options[control.selectedIndex].value+"'.str_replace('&amp;', '&', $this->query_string).'";
        }
        
        </script>
        <select id="rpp" onchange="openUrl();">';
        
        for($i=MIN_PER_PAGE; $i<=MAX_PER_PAGE; $i+=10)
        {
            $str .= '<option value="'.$i.'"'.($i == $this->rpp ? ' selected="selected"': '').'>'.$i.'</option>'."\r\n";
        }
        
        return $str.'</select>';
    }        


    /********
    *    Previous & Next links
    ********/
    function prev_next()
    {
        $str = ($this->page > 1) ? '<a href="paginas.php?page='.($this->page-1).'&amp;rpp='.$this->rpp.$this->query_string.'" title="Vorige Pagina">&laquo;&laquo;</a>' : '<span style="color:#aaa">&laquo;&laquo;</span>';
        $str .= '&nbsp;&nbsp;&nbsp;';
        $str .= ($this->page < $this->total_pages) ? '<a href="paginas.php?page='.($this->page+1).'&amp;rpp='.$this->rpp.$this->query_string.'" title="Volgende Pagina">&raquo;&raquo;</a>' : '<span style="color:#aaa">&raquo;&raquo;</span>';
    
        return $str;
    }

    /********
    *    Pagenumbers opmaken en uitspugen
    ********/
    function show_page_numbers($num_page_links=7)
    {
        if($this->total_pages > 1)
        {
            $num_page_links = $num_page_links % 2 ? $num_page_links : $num_page_links + 1;
            
            $pagenumbers = 'Pagina: <strong>'.$this->page.'</strong> van '.$this->total_pages.'<br />';
            
            if($this->total_pages > $num_page_links)
            {
                
                $cutoff = floor($num_page_links / 2);
                
                $start = $this->page - $cutoff;
                $end   = $this->page + $cutoff;


                /********
                *    No Pagenumbers Less then 1 && Greater then total_pages
                ********/
                while($start < 1)                   { $start++; $end++; }
                while($end > $this->total_pages)    { $start--; $end--; }


                /********
                *    Pagina nummers opmaken en uitspugen
                ********/
                if($this->page > $cutoff + 1) { $pagenumbers .= '<a href="'.THIS_PAGE.'?page=1&amp;rpp='.$this->rpp.$this->query_string.'" title="First Page (1)">...</a>&nbsp; '; }
                
                for($i=$start; $i<=$end; $i++)
                {
                    $pagenumbers .= ($i == $this->page) ? '<strong style="text-decoration:underline;">'.$i.'</strong>&nbsp; '."\r\n" : '<a href="'.THIS_PAGE.'?page='.$i.'&amp;rpp='.$this->rpp.$this->query_string.'" title="Go to Page '.$i.'">'.$i.'</a>&nbsp; '."\r\n";
                }
                
                if($this->page < $this->total_pages - $cutoff) { $pagenumbers .= '<a href="'.THIS_PAGE.'?page='.$this->total_pages.'&amp;rpp='.$this->rpp.$this->query_string.'" title="Last Page ('.$this->total_pages.')">...</a>&nbsp; '; }
                
            }
            else
            {
                for($i=1; $i<=$this->total_pages; $i++)
                {
                    $pagenumbers .= ($i == $this->page) ? '<strong style="text-decoration:underline;">'.$i.'</strong>&nbsp; '."\r\n" : '<a href="'.THIS_PAGE.'?page='.$i.'&amp;rpp='.$this->rpp.$this->query_string.'" title="Go to Page '.$i.'">'.$i.'</a>&nbsp; '."\r\n";
                }
            }
            return rtrim($pagenumbers);
            //gg = ($this->offset + 1).' t/m '. ($this->offset + $this->rpp).' van de '.$this->total_records. ' dieren'.'<br>';

            //return(hh);
        }
        else
        {
            return NULL;
        }
    }


    /********
    *    Regelnumbers opmaken en uitspugen
    ********/

    /*function show_record_numbers()
    {
        if($this->total_pages > 1)
        {
            $einde = ($this->offset + $this->rpp);
            if ($einde > $this->total_records) { $einde = $this->total_records; }
            return ($this->offset + 1).' t/m '. $einde .' van de '.$this->total_records. ' dieren'.'<br>';
        }
        else
        {
            return $this->total_records. ' dieren';
        }

    }*/

} // Einde class Page_numbers




$page_nums = new Page_numbers($tabel, $WHERE, $db, $pag); 

$_SESSION["RPP"] = $page_nums->rpp; $RPP = $_SESSION["RPP"]; // zorgt dat regels per pagina wordt onthouden bij het opnieuw laden van de pagina



$page_numbers = $page_nums->show_page_numbers(7); $_SESSION["PA"] = $page_nums->page; $pag = $_SESSION["PA"]; // zorgt dat paginanummer wordt onthouden bij het opnieuw laden van de pagina

//$record_numbers = $page_nums->show_record_numbers();

$kzlRpp = $page_nums->show_rpp();

/********************   EINDE   MEERDERE PAGINA'S  EINDE    ********************/

?>
