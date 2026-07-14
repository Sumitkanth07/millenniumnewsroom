<?php

namespace App\Services;

use App\Repositories\AdvertisementRepository;
use App\Models\Advertisement;

class AdvertisementService
{
    protected AdvertisementRepository $repository;

    public function __construct(AdvertisementRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get the highest priority valid advertisement for a placement.
     */
    public function getAdForPlacement(string $placement): ?Advertisement
    {
        $ads = $this->repository->getActiveAds();
        $device = $this->detectDevice();
        $pageType = $this->detectPageType();

        foreach ($ads as $ad) {
            if ($ad->placement !== $placement) {
                continue;
            }

            if (!$ad->isScheduled()) {
                continue;
            }

            if (!$ad->isUnderLimits()) {
                continue;
            }

            if (!$ad->isValidForDevice($device)) {
                continue;
            }

            if (!$ad->isValidForPage($pageType)) {
                continue;
            }

            return $ad;
        }

        return null;
    }

    /**
     * Detect the user's active device class.
     */
    public function detectDevice(): string
    {
        $ua = request()->userAgent() ?: '';
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
            return 'tablet';
        }
        if (preg_match('/(up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile|iphone|ipod)/i', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Detect current frontend page type.
     */
    public function detectPageType(): string
    {
        $route = request()->route();
        if (!$route) {
            return 'other';
        }

        $routeName = $route->getName();
        switch ($routeName) {
            case 'home':
                return 'homepage';
            case 'category.show':
                return 'category';
            case 'blog.category.show':
                return 'single';
            case 'search':
                return 'search';
            case 'author.show':
                return 'author';
            case 'tag.show':
                return 'tag';
            case 'page.show':
                return 'static';
            default:
                return 'other';
        }
    }
}
