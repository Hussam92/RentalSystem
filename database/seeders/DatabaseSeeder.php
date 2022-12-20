<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        try {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('123123123'),
            ]);
        } catch (QueryException $exception) {
            error_log(sprintf("  \033[31m%s\033[0m", 'Admin already exists. Continue...'));
        }

        $this->call([
            ApartmentSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
