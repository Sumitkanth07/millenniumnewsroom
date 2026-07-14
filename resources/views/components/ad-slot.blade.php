@props(['placement' => '', 'label' => 'Advertisement', 'sticky' => false])

@php
    $service = app(\App\Services\AdvertisementService::class);
    $ad = $service->getAdForPlacement($placement);
@endphp

@if($ad)
    @php
        $device = $service->detectDevice();
        $adCode = $ad->code;
        if ($device === 'desktop' && $ad->code_desktop) {
            $adCode = $ad->code_desktop;
        } elseif ($device === 'tablet' && $ad->code_tablet) {
            $adCode = $ad->code_tablet;
        } elseif ($device === 'mobile' && $ad->code_mobile) {
            $adCode = $ad->code_mobile;
        }

        $imagePath = $ad->image;
        if ($device === 'tablet' && $ad->image_tablet) {
            $imagePath = $ad->image_tablet;
        } elseif ($device === 'mobile' && $ad->image_mobile) {
            $imagePath = $ad->image_mobile;
        }

        $style = '';
        if ($ad->width) {
            $style .= 'min-width: ' . $ad->width . 'px; ';
        }
        if ($ad->height) {
            $style .= 'min-height: ' . $ad->height . 'px; ';
        }
    @endphp

    <div class="portal-ad-wrapper portal-ad-{{ $placement }} {{ $sticky ? 'sticky-sidebar-ad' : '' }}" 
         data-ad-id="{{ $ad->id }}" 
         data-ad-placement="{{ $placement }}"
         style="{{ $style }}"
         aria-label="{{ $label }}">
        <template>
            @if(in_array($ad->type, ['google_adsense', 'google_ad_manager', 'media_net', 'html', 'js']))
                {!! $adCode !!}
            @elseif($ad->type === 'image' || $ad->type === 'affiliate')
                @if($imagePath)
                    <a href="{{ route('ads.track-click', $ad->id) }}" target="_blank" rel="noopener">
                        <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $ad->name }}" style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
                    </a>
                @endif
            @elseif($ad->type === 'iframe')
                <iframe src="{{ $ad->target_url }}" width="{{ $ad->width ?: '100%' }}" height="{{ $ad->height ?: '250' }}" style="border:0; overflow:hidden;" scrolling="no" frameborder="0" allowtransparency="true"></iframe>
            @endif
        </template>
    </div>
@endif
