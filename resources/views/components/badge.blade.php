@props(['color' => 'default'])
<span {{ $attributes->merge(['class' => "badge badge-{$color}"]) }}>{{ $slot }}</span>
