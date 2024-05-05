<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        School::create([
            'id' => 'bcd5695c-89ea-4c58-bc49-bc1562970ed1',
            'school_name' => 'SMKN 1 Purwokerto'
        ]);
        School::create([
            'id' => '05cfb4d5-33c2-4ac9-9d58-c329d41cf5b9',
            'school_name' => 'SMKN 2 Purwokerto'
        ]);
        School::create([
            'id' => '8e99e7b5-3945-4647-9c0f-861d9aa3f905',
            'school_name' => 'SMAN 1 Purwokerto'
        ]);
    }
}
