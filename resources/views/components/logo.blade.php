@props(['variant' => 'horizontal', 'height' => '40'])

@if($variant === 'icon')
    <img src="{{ asset('img/logo-icon.svg') }}" alt="Logo MagangKu" height="{{ $height }}" style="width:auto;">
@elseif($variant === 'white')
    <img src="{{ asset('img/logo-white.svg') }}" alt="MagangKu" height="{{ $height }}" style="width:auto;">
@elseif($variant === 'vertical')
    <img src="{{ asset('img/logo-vertical.svg') }}" alt="MagangKu" height="{{ $height }}" style="width:auto;">
@else
    <img src="{{ asset('img/logo.svg') }}" alt="MagangKu" height="{{ $height }}" style="width:auto;">
@endif
