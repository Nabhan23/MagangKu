@extends('layouts.guest')

@section('title', 'MagangKu - Langkah Awal Menuju Karier Impian')

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'MagangKu',
    'alternateName' => 'MagangKu - Langkah Awal Menuju Karier Impian',
    'url' => url('/'),
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => url('/lowongan?q={search_term_string}'),
        'query-input' => 'required name=search_term_string',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
    @include('landing.partials.hero', ['stats' => $stats])
    @include('landing.partials.stats', ['stats' => $stats])
    @include('landing.partials.jobs', ['jobs' => $jobs, 'magang' => $magang])
    @include('landing.partials.companies', ['companies' => $companies])
    @include('landing.partials.features')
    @include('landing.partials.differentiators')
    @include('landing.partials.events', ['events' => $events])
    @include('landing.partials.articles', ['articles' => $articles])
    @include('landing.partials.testimonials', ['testimonials' => $testimonials])
    @include('landing.partials.cta')
@endsection
