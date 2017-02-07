<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\User;
use App\Route;
use App\RouteStep;
use App\Bounds;
use App\Geo;
use App\Setting;

use App\Jobs\GenerateRoute;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class GenerateRoute extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $userId;
    private $minDuration;
    private $Bounds;
    private $Geo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;

        $this->Bounds = new Bounds();
        $this->Geo = new Geo();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if(Setting::where('key', 'SIMULATION_STATUS')->first()->value == 0)
        {
            return;
        }

        $user = User::find($this->userId);

        if(!$user)
        {
            return;
        }

        $route = new Route();
        $route->start_lat = $user->lat;
        $route->start_lng = $user->lng;
        $route->end_lat = $this->Bounds->getRandomLat();
        $route->end_lng = $this->Bounds->getRandomLng();
        $route->save();

        // todo el insert de una ruta deberia hacerse
        // hasta que obtenga las directions
        $user->route()->save($route);

        $url = env('MAPBOX_DIRECTIONS_API_URL');
        $url .= 'walking/'; // metodo
        $url .= $route->start_lng.','.$route->start_lat.';'.$route->end_lng.','.$route->end_lat;
        $url .= '.json?';
        $url .= 'access_token='.env('MAPBOX_API_KEY');
        $url .= '&steps=true';


        $steps = [];
        $client = new \GuzzleHttp\Client();
        // verify false solo para que funque en windows :(
        $res = $client->request('GET', $url, ['verify' => false]);
        if($res->getStatusCode() === 200) {
            $jsonRes = json_decode($res->getBody());

            $step_number = 0;
            $prevInters = null;
            foreach ($jsonRes->routes[0]->legs[0]->steps as $_step) {
                $duration = ($_step->duration / sizeof($_step->intersections)) / 100;

                foreach ($_step->intersections as $_intersection) {
                    $step = new RouteStep();
                    $step->lat = $_intersection->location[1];
                    $step->lng = $_intersection->location[0];
                    $step->step_number = $step_number++;
                    $step->duration = $duration;

                    array_push($steps, $step);
                }
/*
                for ($i=0, $n=sizeof($_step->intersections); $i < $n; $i++) 
                { 
                    $_intersection = $_step->intersections[$i];
                    if(!is_null($prevInters))
                    {
                        $prevLat = $prevInters->location[1];
                        $prevLng = $prevInters->location[0];
                        $lat = $_intersection->location[1];
                        $lng = $_intersection->location[0];

                        $innerSteps = $this->Geo->getSteps(20, $prevLat, $prevLng, $lat, $lng);
                    
                        // mayor a 2 porque quito el inicial y el ultimo
                        if(sizeof($innerSteps) > 2)
                        {
                            $duration = ($_step->duration 
                                / (sizeof($_step->intersections) + sizeof($innerSteps)));

                            for ($x=1, $y=sizeof($innerSteps)-1; $x < $y; $x++) { 
                                $_innerStep = $innerSteps[$x];

                                $step = new RouteStep();
                                $step->lat = $_innerStep[0];
                                $step->lng = $_innerStep[1];
                                $step->step_number = $step_number++;
                                $step->duration = $duration;

                                array_push($steps, $step);
                            }
                        }
                    }


                    $step = new RouteStep();
                    $step->lat = $_intersection->location[1];
                    $step->lng = $_intersection->location[0];
                    $step->step_number = $step_number++;
                    $step->duration = $duration;

                    array_push($steps, $step);

                    $prevInters = $_intersection;
                }*/
            }
        }
        $route->steps()->saveMany($steps);

        $job = (new UpdateRoute($user->id));
        dispatch($job);
    }
}
