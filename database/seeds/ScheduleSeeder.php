<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i <= 23; $i++) {
            //$isEven = $i % 2 == 0;
            DB::table('schedules')->insert([
                'init_at' => "$i:00:00",
                'ends_at' => "$i:29:00",
            ]);
            DB::table('schedules')->insert([
                'init_at' => "$i:30:00",
                'ends_at' => "$i:59:00",
            ]);
        }
        
        for ($i=1; $i <= 48; $i++) {
            $isEven = $i % 2 == 0;
            DB::table('schedule_users')->insert([
                'schedule_id' => $i,
                'user_id' => 1,
            ]);
        }
    }
}
