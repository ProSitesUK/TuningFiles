<?php

namespace App\View\Components;

use App\Services\SeoService;
use Illuminate\View\Component;

class Seo extends Component
{
    public array $meta;
    public ?string $ga4Id;
    public ?string $gscVerification;

    public function __construct(
        SeoService $seo,
        ?string $subjectType = null,
        ?string $subjectKey = null,
        array $defaults = [],
    ) {
        if ($subjectType === null || $subjectKey === null) {
            [$subjectType, $subjectKey] = $seo->currentSubject();
        }

        $this->meta = $seo->resolve($subjectType, $subjectKey, $defaults);
        $this->ga4Id = \App\Models\SiteSetting::get('ga4_measurement_id');
        $this->gscVerification = \App\Models\SiteSetting::get('gsc_verification');
    }

    public function render()
    {
        return view('components.seo');
    }
}
