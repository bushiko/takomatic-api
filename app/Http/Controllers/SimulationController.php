<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Bounds;

class SimulationController extends Controller
{
    private $Bounds;

    public function __construct() {
        $this->Bounds = new Bounds();
    }

    public function showBounds()
    {
        return response()->json($this->Bounds->getBounds());
    }
}
