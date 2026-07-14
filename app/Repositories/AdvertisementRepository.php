<?php

namespace App\Repositories;

use App\Models\Advertisement;
use Illuminate\Support\Facades\Cache;

class AdvertisementRepository
{
    /**
     * Get all active advertisements ordered by priority.
     */
    public function getActiveAds()
    {
        return Cache::remember('portal.ads.all', 3600, function () {
            return Advertisement::where('is_active', true)
                ->orderByDesc('priority')
                ->orderByDesc('id')
                ->get();
        });
    }
}
