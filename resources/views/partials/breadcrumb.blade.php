@props(['items' => []])
<nav aria-label="breadcrumb" class="mb-3" data-aos="fade-up">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-mk text-decoration-none"><i class="bi bi-house-door me-1"></i>Beranda</a></li>
        @foreach($items as $item)
            @if(!empty($item['url']))
                <li class="breadcrumb-item"><a href="{{ $item['url'] }}" class="text-mk text-decoration-none">{{ $item['label'] }}</a></li>
            @else
                <li class="breadcrumb-item active" aria-current="page">{{ $item['label'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
