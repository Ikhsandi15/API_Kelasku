<?php

namespace Database\Seeders;

use App\Models\Friendship;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Friendship::create(
            [
                'id' => Str::uuid(),
                'user_id' => 'bccf2bdc-1010-4a96-9503-a0f7a1a67a75',
                'friend_id' => '06f1af0f-723c-4a48-b3bc-c4a3b4cb4b70',
                'status' => 'pending'
            ]
        );
        Friendship::create(
            [
                'id' => Str::uuid(),
                'user_id' => '06f1af0f-723c-4a48-b3bc-c4a3b4cb4b70',
                'friend_id' => '71d26790-a3a7-46b0-b6a8-1d82cd317475',
                'status' => 'accept'
            ]
        );
        Friendship::create(
            [
                'id' => Str::uuid(),
                'user_id' => '71d26790-a3a7-46b0-b6a8-1d82cd317475',
                'friend_id' => 'bccf2bdc-1010-4a96-9503-a0f7a1a67a75',
                'status' => 'accept'
            ]
        );
        Friendship::create(
            [
                'id' => Str::uuid(),
                'user_id' => '71d26790-a3a7-46b0-b6a8-1d82cd317475',
                'friend_id' => '2c574f67-c06c-4ef7-b119-172888ffefbe',
                'status' => 'accept'
            ]
        );
    }
}
