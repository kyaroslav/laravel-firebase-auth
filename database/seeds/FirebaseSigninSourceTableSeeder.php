<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FirebaseSigninSourceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $source = [
            "Google" => 0,
            "Facebook" => 0,
            "Twitter" => 0,
            "Github" => 0,
            "Email" => 0,
            "Phone" => 1
        ];

        $insert = [];

        foreach ($source as $key => $src)
        {

            $insert[] = [
                "name" => $key,
                "active" => $src,
                "created_at" => Carbon::now()
            ];

        }

        DB::table('firebase_signin_source')->insert($insert);

    }
}