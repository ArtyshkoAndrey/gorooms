<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(PagesSeeder::class);
        $this->call(AddSeoSettingsSeeder::class);
        $this->call(FixTypeHotelSeeder::class);
        $this->call(FixPageDescriptionsSeeder::class);
        $this->call(PageContactSeeder::class);
    }
}
