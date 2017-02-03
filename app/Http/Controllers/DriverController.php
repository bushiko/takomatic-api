<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;

use App\User;
use Validator;

use Vinkla\Pusher\Facades\Pusher;

class DriverController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $drivers = User::where('role', 'DRIVER')->get();

        return response()->json($drivers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            $rules = array(
                'name' => 'required'
            );

            $validator = Validator::make(Input::all(), $rules);

            if($validator->fails()) 
            {
                return response()->json($validator->messages(), 
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $driver = new User;
            $driver->name = Input::get('name');
            $driver->role = 'DRIVER';

            $driver->save();

            // TODO: como valido si se guardo?

            Pusher::trigger('tako-channel', 'new-driver', ['driver' => $driver]);

            return response()->json($driver);
        }
        catch(\Exception $e) 
        {
            return response()->json(['type' => 'error', 'message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $driver = User::where('role', 'DRIVER')->find($id);

        if(is_null($driver))
        {
            return response('Driver Not Found', Response::HTTP_NOT_FOUND);
        }

        return response()->json($driver);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
