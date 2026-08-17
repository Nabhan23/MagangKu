@php $current = app()->getLocale(); @endphp
<div class="locale-switch" role="group" aria-label="Language / Bahasa">
    <a href="{{ route('locale.switch', 'id') }}" class="{{ $current === 'id' ? 'active' : '' }}" title="Bahasa Indonesia">ID</a>
    <a href="{{ route('locale.switch', 'en') }}" class="{{ $current === 'en' ? 'active' : '' }}" title="English">EN</a>
</div>
