<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $entries = [
            ['loc' => route('home'),     'changefreq' => 'weekly',  'priority' => '1.0'],
            ['loc' => route('vehicles'), 'changefreq' => 'daily',   'priority' => '0.9'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
        foreach ($entries as $e) {
            $xml .= '  <url>'.PHP_EOL;
            $xml .= '    <loc>'.htmlspecialchars($e['loc']).'</loc>'.PHP_EOL;
            $xml .= '    <changefreq>'.$e['changefreq'].'</changefreq>'.PHP_EOL;
            $xml .= '    <priority>'.$e['priority'].'</priority>'.PHP_EOL;
            $xml .= '  </url>'.PHP_EOL;
        }
        $xml .= '</urlset>'.PHP_EOL;

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $sitemap = route('sitemap');
        $policy = SiteSetting::get('default_robots', 'index,follow');

        $body = "User-agent: *".PHP_EOL;
        if (str_contains($policy, 'noindex')) {
            $body .= "Disallow: /".PHP_EOL;
        } else {
            $body .= "Disallow: /app/".PHP_EOL;
            $body .= "Disallow: /admin/".PHP_EOL;
            $body .= "Disallow: /login".PHP_EOL;
            $body .= "Disallow: /register".PHP_EOL;
            $body .= "Allow: /".PHP_EOL;
        }
        $body .= PHP_EOL."Sitemap: {$sitemap}".PHP_EOL;

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
