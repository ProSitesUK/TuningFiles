<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $entries = [
            ['loc' => route('home'),       'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => route('vehicles'),   'changefreq' => 'daily',  'priority' => '0.9'],
            ['loc' => route('blog.index'), 'changefreq' => 'daily',  'priority' => '0.8'],
        ];

        foreach (\App\Models\Post::published()->orderByDesc('published_at')->get() as $post) {
            $entries[] = [
                'loc'        => route('blog.show', $post),
                'lastmod'    => optional($post->updated_at)->toAtomString(),
                'changefreq' => 'monthly',
                'priority'   => '0.7',
            ];
        }

        // Every active make + every active model = an indexable landing page.
        $makes = \App\Models\VehicleMake::where('is_active', true)
            ->whereHas('models', fn ($q) => $q->where('is_active', true)->whereHas('variants', fn ($qq) => $qq->where('is_active', true)))
            ->with(['models' => fn ($q) => $q->where('is_active', true)->whereHas('variants', fn ($qq) => $qq->where('is_active', true))])
            ->get();

        foreach ($makes as $make) {
            $entries[] = [
                'loc'        => route('vehicles.make', $make),
                'lastmod'    => optional($make->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ];
            foreach ($make->models as $model) {
                $entries[] = [
                    'loc'        => route('vehicles.model', [$make, $model]),
                    'lastmod'    => optional($model->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority'   => '0.7',
                ];
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
        foreach ($entries as $e) {
            $xml .= '  <url>'.PHP_EOL;
            $xml .= '    <loc>'.htmlspecialchars($e['loc']).'</loc>'.PHP_EOL;
            if (! empty($e['lastmod'])) {
                $xml .= '    <lastmod>'.$e['lastmod'].'</lastmod>'.PHP_EOL;
            }
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
