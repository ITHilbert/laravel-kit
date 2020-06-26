<?php
namespace ITHilbert\LaravelKit\Database\Seeders;

use Illuminate\Database\Seeder;

class AnredeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('anrede')->delete();

        \DB::table('anrede')->insert(array (
            0 =>
            array (
                'id' => 1,
                'anrede' => 'Herr',
            ),
            1 =>
            array (
                'id' => 2,
                'anrede' => 'Frau',
            ),
        ));


    }
}
