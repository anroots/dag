<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organizations = [
            'Paradise Island',
            'Banana Tree',
            'Big Banana Tree',
            'Yellow Banana',
            'Brown Banana',
            'Green Banana',
            'Black Banana',
            'Phoneutria Spider'
        ];

        foreach ($organizations as $i => $name) {
            DB::table('organizations')
                ->insert(['id' => $i + 1, 'name' => $name]);
        }
    }
}
