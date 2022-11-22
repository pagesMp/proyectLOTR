<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert(
            [   
                'id' => '5b606e4b-a37f-43e8-aa26-c41192e36053',
                'name'=> 'user'
            ]
        );

        DB::table('roles')->insert(
            [
                'id' => '4dcdb96f-75c3-45bd-a728-06702d4ab254',
                'name'=> 'admin'    
            ]
        );
    }
}
