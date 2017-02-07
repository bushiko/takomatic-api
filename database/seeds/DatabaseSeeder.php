<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Setting;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call(UserTableSeeder::class);

        $this->call('SettingsSeeder');

        Model::reguard();
    }
}

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create(['key' => 'SOUTH_WEST_BOUND_LAT', 'value' => '19.3575374']);
        Setting::create(['key' => 'SOUTH_WEST_BOUND_LNG', 'value' => '-99.1908466']);
        Setting::create(['key' => 'NORTH_EAST_BOUND_LAT', 'value' => '19.4140716']);
        Setting::create(['key' => 'NORTH_EAST_BOUND_LNG', 'value' => '-99.1535356']);

        Setting::create(['key' => 'SIMULATION_STATUS', 'value' => 0]);
    }
}
