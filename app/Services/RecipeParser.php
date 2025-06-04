<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;

class RecipeParser
{
    public static function fetchRecipeFromUrl(string $url): array
    {
        $html = Http::get($url)->body();
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $xpath = new DOMXpath($dom);
        $jsonScripts = $xpath->query('//script[@type="application/ld+json"]');

        return collect($jsonScripts)
            ->map(fn ($tag) => json_decode($tag->nodeValue, true))
            ->first(fn ($tag) => strtolower($tag['@type']) === 'recipe');
    }
}
