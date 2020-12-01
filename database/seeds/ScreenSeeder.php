<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();
        $faker = Faker\Factory::create();

        for ($i=1; $i <= 2; $i++) { 
            DB::table('screens')->insert([
                'uuid' => $faker->uuid,
                'name' => "TV Testing #$i",
                'serial' => "Serial Testing #$i",
                'brand' => "Brand Testing #$i",
                'manufacturer' => "Mnf. Testing #$i",
                'os' => "OS Testing #$i",
                'version' => "Ver. Testing #$i",
                'offline' => 0,
                'lng' => $faker->longitude(),
                'lat' => $faker->latitude(),
                'user_id' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]);

        }
    }
}
