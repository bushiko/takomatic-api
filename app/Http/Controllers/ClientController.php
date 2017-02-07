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
use App\Jobs\UpdateRoute;
use App\Jobs\GenerateRoute;

use App\Bounds;

use App\Route;
use App\RouteStep;

class ClientController extends Controller
{
    private $Bounds;


    public function __construct() {
        $this->Bounds = new Bounds();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $drivers = User::where('role', 'CLIENT')->get();

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

            $client = new User;
            $client->name = Input::get('name');
            $client->role = 'CLIENT';

            // random initial coords
            $client->lat = $this->Bounds->getRandomLat();
            $client->lng = $this->Bounds->getRandomLng();

            $client->save();

            // TODO: como valido si se guardo?

            Pusher::trigger('tako-channel', 'new-client', ['client' => $client]);

            $job = (new GenerateRoute($client->id));
            dispatch($job);


            return response()->json($client);
        }
        catch(\Exception $e) 
        {
            return response()->json(
                ['type' => 'error', 'message' => $e->getMessage()], 
                Response::HTTP_BAD_REQUEST
            );
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
        $client = User::where('role', 'CLIENT')->find($id);

        if(is_null($client))
        {
            return response('Client Not Found', Response::HTTP_NOT_FOUND);
        }

        return response()->json($client);
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
        $client = User::where('role', 'CLIENT')->find($id);

        if(is_null($client))
        {
            return response('Client Not Found', Response::HTTP_NOT_FOUND);
        }

        if($client->route)
        {
            $client->route->steps()->delete();
            $client->route()->delete();
        }

        Pusher::trigger('tako-channel', 'deleted-client', ['clientId' => $client->id]);
        $client->delete();

        return response()->json(
            ['type' => 'success', 'message' => 'Client deleted'], 
            Response::HTTP_OK
        );
    }
}
