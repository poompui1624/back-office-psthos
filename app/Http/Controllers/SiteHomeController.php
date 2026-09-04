<?php

namespace App\Http\Controllers;

use App\Models\SiteBanner;
use App\Models\SiteExecutive;
use App\Models\SiteLink;
use App\Models\SitePage;
use Illuminate\Contracts\View\View;

/**
 * The hospital's public site.
 *
 * Reachable without signing in, so every query here filters to content that has
 * been deliberately published — an inactive row or a banner outside its dates
 * must never reach a visitor.
 */
class SiteHomeController extends Controller
{
    public function index(): View
    {
        return view('site.home', [
            'banners' => SiteBanner::visibleNow()->get(),
            'links' => SiteLink::active()->get(),
            'director' => SiteExecutive::active()->where('is_featured', true)->first(),
            'pages' => SitePage::where('is_active', true)->get()->keyBy('key'),
        ]);
    }

    public function page(string $key): View
    {
        abort_unless(array_key_exists($key, SitePage::KEYS), 404);

        $page = SitePage::where('key', $key)->where('is_active', true)->first();

        abort_unless($page, 404);

        return view('site.page', [
            'page' => $page,
            'executives' => $key === 'structure'
                ? SiteExecutive::active()->get()
                : collect(),
        ]);
    }
}
