@props(['vars','tableID'])
<table class="table table-hover" id="{{ $tableID }}">
    <thead>
        <tr>
            <th scope="col">Title</th>
            <th scope="col">catigory</th>
            <th scope="col">Status</th>
            <th scope="col">Reactions</th>
            <th scope="col">Publish Date</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody class="table-group-divider" id="{{ $tableID }}-content">
        @forelse ($vars as $var)
            <tr class="clickable-row" data-href="{{ route('articles.show', $var['article_id']) }}">
                <td>{{$var['title']}}</td>
                <td>{{$var->categorie->name ?? 'N/A'}}</td>
                <td>
                    @if ($var['status'] == 'published')
                        <span class="badge published">Published</span>
                    @elseif ($var['status'] == 'draft')
                        <span class="badge bg-secondary">Draft</span>
                    @elseif ($var['status'] == 'scheduled')
                        <span class="badge bg-info">Scheduled</span>
                    @endif
                </td>
                <td>
                    <div>
                        <small class="d-block text-muted"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="14" height="14" style="margin-right: 3px; vertical-align: middle;"><path d="M288 80c-65.2 0-118.8 29.6-159.9 67.7C89.6 183.5 63 226 49.4 256c13.6 30 40.2 72.5 78.6 108.3C169.2 402.4 222.8 432 288 432s118.8-29.6 159.9-67.7C486.4 328.5 513 286 526.6 256c-13.6-30-40.2-72.5-78.6-108.3C406.8 109.6 353.2 80 288 80zM95.4 112.6C142.5 68.8 207.2 32 288 32s145.5 36.8 192.6 80.6c46.8 43.5 78.1 95.4 93 131.1c3.3 7.9 3.3 16.7 0 24.6c-14.9 35.7-46.2 87.7-93 131.1C433.5 443.2 368.8 480 288 480s-145.5-36.8-192.6-80.6C48.6 356 17.3 304 2.5 268.3c-3.3-7.9-3.3-16.7 0-24.6C17.3 208 48.6 156 95.4 112.6zM288 336c44.2 0 80-35.8 80-80s-35.8-80-80-80c-.7 0-1.3 0-2 0c1.3 5.1 2 10.5 2 16c0 35.3-28.7 64-64 64c-5.5 0-10.9-.7-16-2c0 .7 0 1.3 0 2c0 44.2 35.8 80 80 80zm0-208a128 128 0 1 1 0 256 128 128 0 1 1 0-256z"/></svg>{{ $var['view_count'] ?? 0 }}</small>
                        <small class="d-block text-muted"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="14" height="14" style="margin-right: 3px; vertical-align: middle;"><path fill="#ff8787" d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/></svg> {{ $var['like_count'] ?? 0 }}</small>
                    </div>
                </td>
                <td>{{ $var['created_at']->format('F j, Y') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn le" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <bold>•••</bold>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('articles.edit', $var['article_id']) }}">Edit</a></li>
                            <li><a class="dropdown-item" href="{{ route('articles.show', $var['article_id']) }}">View State</a></li>
                            <li>
                                <form action="{{route('articles.destroy',$var['article_id'])}}" method="POST" class="dropdown-item text-danger delete-item-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-danger border-0">Delete</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-3">
                    <div class="alert alert-warning mb-0">
                        No articles found.
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if(method_exists($vars, 'hasPages') && $vars->hasPages())
    <div class="custom-pagination mt-4">
        <div class="d-flex justify-content-center align-items-center">
            @if($vars->hasMorePages())
            <div class="text-center">
                <button 
                    type="button" 
                    id="load-more-btn-{{ $tableID }}" 
                    data-table="{{ $tableID }}"
                    data-current-page="{{ $vars->currentPage() }}"
                    data-last-page="{{ $vars->lastPage() }}"
                    class="btn btn-subscribe load-more-btn">
                    Load More
                </button>
            </div>
            @endif
        </div>
    </div>
@endif

@push('scripts')
    <script src="{{ asset('assets/js/DashTablesLink.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreButtons = document.querySelectorAll('.load-more-btn');
            
            loadMoreButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tableId = this.getAttribute('data-table');
                    let currentPage = parseInt(this.getAttribute('data-current-page'));
                    const lastPage = parseInt(this.getAttribute('data-last-page'));
                    const nextPage = currentPage + 1;
                    
                    // Disable button during load
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    
                    // Get the current URL and add page parameter
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', nextPage);
                    
                    // Fetch next page
                    fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Create a temporary div to parse the HTML
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Get the table content from the response
                        const newRows = doc.querySelectorAll(`#${tableId}-content tr`);
                        const tableBody = document.querySelector(`#${tableId}-content`);
                        
                        // Append new rows
                        newRows.forEach(row => {
                            tableBody.appendChild(document.importNode(row, true));
                        });
                        
                        // Update current page
                        this.setAttribute('data-current-page', nextPage);
                        
                        // Re-enable button
                        this.disabled = false;
                        this.innerHTML = 'Load More';
                        
                        // Hide button if we reached the last page
                        if (nextPage >= lastPage) {
                            this.style.display = 'none';
                        }
                        
                        // Reinitialize click handlers for new rows
                        if (typeof initTableRowLinks === 'function') {
                            initTableRowLinks();
                        }
                    })
                    .catch(error => {
                        console.error('Error loading more items:', error);
                        this.disabled = false;
                        this.innerHTML = 'Load More';
                    });
                });
            });
        });
    </script>
@endpush