@if($jobs->hasPages())
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            @if($jobs->onFirstPage())
                <li class="page-item disabled"><span class="page-link rounded-3 me-1 border-0 bg-soft">Prev</span></li>
            @else
                <li class="page-item"><a class="page-link rounded-3 me-1 border-0 bg-soft text-mk fw-semibold" href="#" data-page-link="{{ $jobs->currentPage() - 1 }}">Prev</a></li>
            @endif

            @foreach($jobs->getUrlRange(1, $jobs->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $jobs->currentPage() ? 'active' : '' }}">
                    <a class="page-link rounded-3 mx-1 border-0 {{ $page == $jobs->currentPage() ? 'bg-mk-gradient text-white' : 'bg-soft text-mk' }}" href="#" data-page-link="{{ $page }}">{{ $page }}</a>
                </li>
            @endforeach

            @if($jobs->hasMorePages())
                <li class="page-item"><a class="page-link rounded-3 ms-1 border-0 bg-soft text-mk fw-semibold" href="#" data-page-link="{{ $jobs->currentPage() + 1 }}">Next</a></li>
            @else
                <li class="page-item disabled"><span class="page-link rounded-3 ms-1 border-0 bg-soft">Next</span></li>
            @endif
        </ul>
    </nav>
@endif
