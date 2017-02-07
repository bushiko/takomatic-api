<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Vinkla\Pusher\Facades\Pusher;
use App\Jobs\GenerateRoute;

use \App\Setting;
use App\User;

use Illuminate\Support\Facades\DB;

class SimulationController extends Controller
{
    public function showSettings()
    {
        return response()->json(Setting::all());
    }

    public function startSimulation() {

    	$status = Setting::where('key', 'SIMULATION_STATUS')->first();

    	if($status->value == 1)
    	{
    		return response()->json(
                ['type' => 'error', 'message' => 'Simulation already running'], 
                Response::HTTP_BAD_REQUEST
            );
    	}

    	$status->value = 1;
    	$status->save();

    	$user_ids = User::all()->pluck('id');

    	foreach ($user_ids as $key => $id) {
    		$job = (new GenerateRoute($id));
            dispatch($job);
    	}

    	Pusher::trigger('tako-channel', 'simulation-status-changed', ['status' => $status->value]);

    	return response()->json(
    		['type' => 'success', 'message' => 'Simulation now running'], 
            Response::HTTP_OK);
    }

    public function stopSimulation() {

    	$status = Setting::where('key', 'SIMULATION_STATUS')->first();

    	if($status->value == 0)
    	{
    		return response()->json(
                ['type' => 'error', 'message' => 'Simulation already stopped'], 
                Response::HTTP_BAD_REQUEST
            );
    	}

        DB::table('route_steps')->truncate();
        DB::table('routes')->truncate();

    	$status->value = 0;
    	$status->save();

    	Pusher::trigger('tako-channel', 'simulation-status-changed', ['status' => $status->value]);

    	return response()->json(
    		['type' => 'success', 'message' => 'Simulation now stopped'], 
            Response::HTTP_OK);
    }
}
