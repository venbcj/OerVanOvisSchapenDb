<?php

class OptieCountTest extends IntegrationCase {

    public function test() {
        $this->output = '<select class="pur" name="dinges">'.PHP_EOL
            .'<option value="vier">4</option>'.PHP_EOL
            .'</select>';
        $this->assertOptieCount('dinges', 1);
    }

}
