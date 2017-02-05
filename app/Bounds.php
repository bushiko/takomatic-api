<?php

namespace App;

class Bounds
{
	protected $sw_bounds;
	protected $ne_bounds;

	public function __construct() {
		$this->sw_bounds = array(
			'lat' => 19.3575374,
			'lng' => -99.1908466
		);

		$this->ne_bounds = array(
			'lat' => 19.4140716,
			'lng' => -99.1535356
		);
	}

	public function getBounds() {
		return array(
			'south_west' => $this->sw_bounds,
			'north_east' => $this->ne_bounds
		);
	}

	public function getRandomLat() {
		return $this->getRandomFloat($this->sw_bounds['lat'], $this->ne_bounds['lat']);
	}

	public function getRandomLng() {
		return $this->getRandomFloat($this->sw_bounds['lng'], $this->ne_bounds['lng']);
	}


	private function getRandomFloat($lim1, $lim2) {
		$float = mt_rand() / mt_getrandmax()
			* (max($lim1, $lim2) - min($lim1, $lim2))
			+ (min($lim1, $lim2));
			
		return round($float, 6);
	}

}
