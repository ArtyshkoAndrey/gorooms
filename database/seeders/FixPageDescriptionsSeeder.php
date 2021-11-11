<?php

namespace Database\Seeders;

use App\Models\PageDescription;
use Illuminate\Database\Seeder;

class FixPageDescriptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages = ['/', '/hotels', '/rooms', '/rooms/hot'];

        $pageDescriptions = PageDescription::all();
        foreach ($pageDescriptions as $description) {
            if(substr($description->url, -1) == '/' && $description->url != '/' && !in_array(substr($description->url,0,-1), $pages) ) {
                $description->update([
                    'url' => substr($description->url,0,-1)
                ]);
            }
        }

        foreach ($pages as $page) {
            $currentPage = PageDescription::where('url', $page)->first();
            if ($currentPage) {
                $currentPage->update([
                    'model_type' => null
                ]);
            }
        }
    }
}
