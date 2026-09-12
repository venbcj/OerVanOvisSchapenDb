<?php

class SchaapTest extends UnitCase {

	public function test_aanmaken() {
		$schaap = new Schaap();

		$this->assertInstanceOf(Schaap::class, $schaap);

	}
}