<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PdfGenerator
{
    public function fromUrl(string $url, bool $isLandscape = false): string
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer '.config('app.pdf-service-auth'),
        ])
            ->post(config('app.pdf-service'), [
                'url' => $url,
                'viewport' => [
                    'width' => $isLandscape ? 800 : 600,
                    'height' => $isLandscape ? 600 : 800,
                    'isLandscape' => $isLandscape,
                ],
            ])
            ->throw()
            ->body();
    }
}
