<?php

namespace App;

use \App\Setting;

class Bounds
{
	protected $sw_bounds;
	protected $ne_bounds;

	public function __construct() {
		$settings = Setting::all();

		$this->sw_bounds = array(
			'lat' => Setting::where('key', 'SOUTH_WEST_BOUND_LAT')->first()->value,
			'lng' => Setting::where('key', 'SOUTH_WEST_BOUND_LNG')->first()->value
		);

		$this->ne_bounds = array(
			'lat' => Setting::where('key', 'NORTH_EAST_BOUND_LAT')->first()->value,
			'lng' => Setting::where('key', 'NORTH_EAST_BOUND_LNG')->first()->value
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
