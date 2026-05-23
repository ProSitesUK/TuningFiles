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
     */
    public function currentSubject(): array
    {
        $routeName = request()->route()?->getName();
        if ($routeName) {
            return ['route', $routeName];
        }
        return ['route', 'unknown'];
    }
}
