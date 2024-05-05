<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => '06f1af0f-723c-4a48-b3bc-c4a3b4cb4b70',
            'name' => 'Joko Bondo',
            'regId' => 'c5TkX9q7IhM:APA91bFRe8EQYv8aE97m6wv3aBl7TfaXjAyE4w4t6mMB70zLs5Xq2pm-WmJbgm8yJIR_8_Zcf5PAG58cDX7av7ZlGr2Uxu0cqAxRUf6POfL7jwDkjVStxzwL-12345678901234567890',
            'phone_number' => '678905432112',
            'school_id' => '8e99e7b5-3945-4647-9c0f-861d9aa3f905',
            'password' => Hash::make('123a$G7G')
        ]);

        User::create([
            'id' => '71d26790-a3a7-46b0-b6a8-1d82cd317475',
            'name' => 'Budi Setiano',
            'regId' => 'r8XyT7o2JlN:APA91bEgw3VvVcEpeQ4FxPqXxEh5F8w8F3L0Iexze9Np3L8oYwCS8-9BMf3b7dSw5uvGwUwA5RQ8UXKmJwz2hBpoZN4ZGgHRPqUM0Xij3D1d9wqR9ml1jRw-abcdefghij12345678901234567890',
            'phone_number' => '098765432109',
            'school_id' => '05cfb4d5-33c2-4ac9-9d58-c329d41cf5b9',
            'password' => Hash::make('*9&76sA3')
        ]);

        User::create([
            'id' => 'bccf2bdc-1010-4a96-9503-a0f7a1a67a75',
            'name' => 'Setyo Tukiman',
            'regId' => 'f3VcN8o1FpL:APA91bGtEgwFyQ3jvRjBvPu62xf0t9-Mo-cYUMZB2pH0JkO8Q10o4sTf-QwUoM9ZBYfKuvNYgJQaCXzJw7hjQrLX2trDkjHszeBx-3xLUVETCVfpiqHq8yjR-klmnopqrstuvw12345678901234567890',
            'phone_number' => '123456789012',
            'school_id' => '05cfb4d5-33c2-4ac9-9d58-c329d41cf5b9',
            'password' => Hash::make('12#As67&')
        ]);

        User::create([
            'id' => '2c574f67-c06c-4ef7-b119-172888ffefbe',
            'name' => 'Tarno Retno',
            'regId' => 't9QpM7o2JlX:APA91bFGcUOgbmC7zTb7oc5_Bq1FYKpr7EQiuPb7dbuoe2iQRU2Af8N15w3m-D5m-JP5mZoXDbAOnw7PxiTWidgglLP2IK0PT9fP1wXkSdYkNPc4ruOM_P8x-klmnopqrstuvwxy1234567890',
            'phone_number' => '876543219009',
            'school_id' => '05cfb4d5-33c2-4ac9-9d58-c329d41cf5b9',
            'password' => Hash::make('12#As67&')
        ]);
    }
}
