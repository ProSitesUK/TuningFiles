<?php

namespace App\Services;

use App\Models\SeoMeta;
use App\Models\SiteSetting;
use Illuminate\Support\Str;

class SeoService
{
    /**
     * Resolve effective SEO meta for the given subject.
     * Fallback chain: subject-specific override -> $entityDefaults -> site defaults -> hardcoded.
     *
     * @param  string  $subjectType  'route' | 'make' | 'model' | 'page'
     * @param  string  $subjectKey   route name OR model/make id (as string)
     * @param  array   $entityDefaults  title/description/og_image derived from the entity (e.g. make/model name)
     */
    public function resolve(string $subjectType, string $subjectKey, array $entityDefaults = []): array
    {
        $override = SeoMeta::forSubject($subjectType, $subjectKey);

        $siteName    = SiteSetting::get('site_name', config('app.name', 'TuningFiles'));
        $defaultDesc = SiteSetting::get('default_description', 'Professional ECU files delivered in minutes. Stage 1 to custom remaps from a vetted tuner network.');
        $defaultOg   = SiteSetting::get('default_og_image');
        $defaultRobots = SiteSetting::get('default_robots', 'index,follow');

        $titleTemplate = SiteSetting::get('title_template', '{title} · {site}');

        // Title resolution
        $rawTitle = $override?->title
            ?? $entityDefaults['title']
            ?? $siteName;

        $title = $rawTitle === $siteName
            ? $siteName
            : Str::of($titleTemplate)
                ->replace('{title}', $rawTitle)
                ->replace('{site}', $siteName)
                ->__toString();

        // Description
        $description = $override?->description
            ?? $entityDefaults['description']
            ?? $defaultDesc;

        // OG image
        $ogImage = $override?->og_image
            ?? $entityDefaults['og_image']
            ?? $defaultOg;

        // Canonical
        $canonical = $override?->canonical
            ?? $entityDefaults['canonical']
            ?? url()->current();

        // Robots
        $robots = $override?->robots
            ?? $entityDefaults['robots']
            ?? $defaultRobots;

        // Structured data
        $structuredData = $override?->structured_data
            ?? $entityDefaults['structured_data']
            ?? null;

        return compact('title', 'description', 'ogImage', 'canonical', 'robots', 'structuredData', 'siteName');
    }

    /**
     * Best-effort: detect the current request's "subject" for default SEO.
     * Returns [type, key, defaults].
     */
    public function currentSubject(): array
    {
        $route = request()->route();
        $routeName = $route?->getName() ?? 'unknown';

        $post = $route?->parameter('post');
        if ($post instanceof \App\Models\Post) {
            return ['post', (string) $post->id, $this->postDefaults($post)];
        }

        $model = $route?->parameter('model');
        if ($model instanceof \App\Models\VehicleModel) {
            $make = $route->parameter('make') ?: $model->make;
            return ['model', (string) $model->id, $this->modelDefaults($make, $model)];
        }

        $make = $route?->parameter('make');
        if ($make instanceof \App\Models\VehicleMake) {
            return ['make', (string) $make->id, $this->makeDefaults($make)];
        }

        return ['route', $routeName, []];
    }

    private function postDefaults(\App\Models\Post $post): array
    {
        $desc = $post->seo_description ?: $post->excerpt
            ?: \Illuminate\Support\Str::limit(strip_tags($post->bodyHtml()), 160);

        return [
            'title'       => $post->title,
            'description' => $desc,
            'og_image'    => $post->cover_image,
            'structured_data' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
                            ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title],
                        ],
                    ],
                    array_filter([
                        '@type'         => 'Article',
                        'headline'      => $post->title,
                        'description'   => $desc,
                        'image'         => $post->cover_image,
                        'datePublished' => optional($post->published_at)->toAtomString(),
                        'dateModified'  => optional($post->updated_at)->toAtomString(),
                        'author'        => $post->author ? [
                            '@type' => 'Person',
                            'name'  => $post->author->name,
                        ] : null,
                        'publisher' => [
                            '@type' => 'Organization',
                            'name'  => \App\Models\SiteSetting::get('site_name', 'TuningFiles'),
                        ],
                        'url' => route('blog.show', $post),
                    ]),
                ],
            ],
        ];
    }

    private function makeDefaults(\App\Models\VehicleMake $make): array
    {
        $title = "{$make->name} Tuning Files & ECU Remaps";
        $desc = $make->seo_description
            ?: "Professional {$make->name} tuning files — stage 1, stage 2, custom remaps and DPF / EGR / AdBlue solutions. Vetted tuners, dyno-validated, original retained.";

        return [
            'title'       => $title,
            'description' => $desc,
            'og_image'    => $make->image_url,
            'structured_data' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',     'item' => route('home')],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Vehicles', 'item' => route('vehicles')],
                            ['@type' => 'ListItem', 'position' => 3, 'name' => $make->name],
                        ],
                    ],
                    [
                        '@type'       => 'CollectionPage',
                        'name'        => $title,
                        'description' => $desc,
                        'url'         => route('vehicles.make', $make),
                    ],
                ],
            ],
        ];
    }

    private function modelDefaults(\App\Models\VehicleMake $make, \App\Models\VehicleModel $model): array
    {
        $title = "{$make->name} {$model->name} Tuning Files & Remaps";
        $desc = $model->seo_description
            ?: "Professional ECU tuning files for the {$make->name} {$model->name}. Stage 1, stage 2 and custom remaps from a vetted tuner network. Dyno-validated, checksum-correct, original file retained.";

        return [
            'title'       => $title,
            'description' => $desc,
            'og_image'    => $model->image_url ?: $make->image_url,
            'structured_data' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',     'item' => route('home')],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Vehicles', 'item' => route('vehicles')],
                            ['@type' => 'ListItem', 'position' => 3, 'name' => $make->name, 'item' => route('vehicles.make', $make)],
                            ['@type' => 'ListItem', 'position' => 4, 'name' => $model->name],
                        ],
                    ],
                    [
                        '@type'       => 'Service',
                        'name'        => "{$make->name} {$model->name} ECU Remap",
                        'description' => $desc,
                        'url'         => route('vehicles.model', [$make, $model]),
                        'provider'    => [
                            '@type' => 'Organization',
                            'name'  => \App\Models\SiteSetting::get('site_name', 'TuningFiles'),
                            'url'   => url('/'),
                        ],
                        'areaServed'  => ['United Kingdom', 'European Union'],
                        'category'    => 'Automotive tuning',
                    ],
                ],
            ],
        ];
    }
}
