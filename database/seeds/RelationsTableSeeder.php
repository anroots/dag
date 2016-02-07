<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RelationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $relations = [
            [1, 3],
            [1, 2],
            [2, 4],
            [2, 5],
            [2, 7],
            [3, 6],
            [3, 5],
            [3, 4],
            [3, 7],
            [7, 8]
        ];

        foreach ($relations as $relation) {
            $head = $relation[0];
            $tail = $relation[1];
            DB::table('relations')->insert(['head' => $head, 'tail' => $tail]);
        }

    }
}
