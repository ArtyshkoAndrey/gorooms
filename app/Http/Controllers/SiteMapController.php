<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Article;
use App\Models\Hotel;
use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SiteMapController extends Controller
{
    private $added_links = [];

    public function index()
    {
        $ttl = 60 * 60 * 24 * 7;
        $links = Cache::remember('sitemap.2g', $ttl, function () {
            $articles = Article::orderBy('created_at', 'DESC')->get();
            $hotels = Hotel::all();
            $pages = Page::all();
            $links = [];
            if ($link = $this->makeLink(route('hotels.index')))
                $links[] = $link;
            foreach ($hotels AS $hotel) {
                $links[] = $this->makeLink(route('hotels.show', $hotel), 'monthly', 0.8, $hotel->updated_at->format('Y-m-d'));
            }
            if ($link = $this->makeLink(route('articles.index')))
                $links[] = $link;
            foreach ($articles AS $article) {
                $links[] = $this->makeLink(route('articles.show', $article), 'monthly', 0.8, $article->updated_at->format('Y-m-d'));
            }
            foreach ($pages AS $page) {
                $links[] = $this->makeLink(route('pages.show', $page), 'monthly', 0.8, $page->updated_at->format('Y-m-d'));
            }
            $addresses = Address::all();
            foreach ($addresses AS $address) {
                $params = [
                    'city' => Str::slug($address->city)
                ];
                $links[] = $this->makeLink(route('search.address', $params), 'monthly', 0.8, $address->updated_at->format('Y-m-d'));
                $params = [
                    'city' => Str::slug($address->city),
                    'area' => 'area-'.Str::slug($address->city_area),
                ];
                $links[] = $this->makeLink(route('search.address', $params), 'monthly', 0.8, $address->updated_at->format('Y-m-d'));
                $params = [
                    'city' => Str::slug($address->city),
                    'area' => 'area-'.Str::slug($address->city_area),
                    'district' => 'district-'.Str::slug($address->city_district),
                ];
                $links[] = $this->makeLink(route('search.address', $params), 'monthly', 0.8, $address->updated_at->format('Y-m-d'));
                if ($address->hotel) {
                  foreach ($address->hotel->metros AS $metro) {
                    $params = [
                      'city' => Str::slug($address->city),
                      'area' => 'metro-'.Str::slug($metro->name),
                    ];
                    $links[] = $this->makeLink(route('search.address', $params), 'monthly', 0.8, $address->hotel->updated_at->format('Y-m-d'));
                  }
                }
            }
            return array_filter($links, function ($item) {
                return !is_null($item);
            });
        });
        $map = view('sitemap', compact('links'));
        return response($map)->header('Content-Type', 'application/xml');
    }

    private function makeLink($url, $changefreq = 'monthly', $priority = 0.8, $lastmod = null): ?object
    {
        if ($this->checkUrl($url))
            return null;
        return (object) [
            'loc' => $url,
            'lastmod' => $lastmod ?? Carbon::now()->startOfMonth()->format('Y-m-d'),
            'changefreq' => $changefreq,
            'priority' => $priority
        ];
    }

    private function checkUrl($url):bool
    {
        if (in_array($url, $this->added_links))
            return true;
        $this->added_links[] = $url;
        $robots_path = public_path('robots.txt');
        if (!file_exists($robots_path))
            return false;
        $robots_content = file_get_contents($robots_path);
        preg_match_all("/Disallow: (.*?)\s\n/imU", $robots_content, $matches);
        $robots = $matches[1];

        foreach($robots as $item) {
            $item = str_replace('*','.*', $item);
            $item = str_replace('+','\+', $item);
            $item = str_replace('.','\.', $item);
            $item = str_replace('/','\/', $item);
            if(preg_match('/^'.$item.'/', $url))
                return true;
        }
        return false;
    }
}
