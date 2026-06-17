@props(['ads' => collect(), 'placement' => '', 'label' => 'Advertisement'])
@php($ad = $ads[$placement] ?? null)
@if($ad?->code)
<div class="portal-ad portal-ad-{{ $placement }}" aria-label="{{ $label }}">
        {!! $ad->code !!}
</div>
@endif
