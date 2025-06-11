@props(['vars','tableID'])
<table class="table table-hover" id="{{ $tableID }}">
    <thead>
        <tr>
            <th scope="col">Title</th>
            <th scope="col">Status</th>
            <th scope="col">Recipients</th>
            <th scope="col">Sent/Failed</th>
            <th scope="col">Created Date</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody class="table-group-divider" id="{{ $tableID }}-content">
        @forelse ($vars as $newsletter)
            <tr class="clickable-row" data-href="{{ route('newsletter.show', $newsletter->id) }}">
                <td>
                    <div class="d-flex align-items-center">
                        @if($newsletter->featured_image)
                            <img src="{{ asset('storage/' . $newsletter->featured_image) }}" 
                                 alt="Newsletter image" 
                                 class="rounded me-2" 
                                 style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-newspaper text-muted"></i>
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-0">{{ Str::limit($newsletter->title, 40) }}</h6>
                            <small class="text-muted">{{ Str::limit($newsletter->summary ?? strip_tags($newsletter->content), 60) }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    @switch($newsletter->status)
                        @case('sent')
                            <span class="badge bg-success">Sent</span>
                            @break
                        @case('draft')
                            <span class="badge bg-secondary">Draft</span>
                            @break
                        @case('scheduled')
                            <span class="badge bg-warning">Scheduled</span>
                            @break
                        @default
                            <span class="badge bg-light text-dark">{{ ucfirst($newsletter->status) }}</span>
                    @endswitch
                </td>
                <td>
                    <span class="badge bg-info">
                        {{ ucfirst(str_replace('_', ' ', $newsletter->recipient_type)) }}
                    </span>
                </td>
                <td>
                    @if($newsletter->status === 'sent')
                        <div>
                            <small class="d-block text-success">
                                <i class="fas fa-paper-plane me-1"></i>{{ $newsletter->total_sent ?? 0 }} sent
                            </small>
                            @if($newsletter->total_failed > 0)
                                <small class="d-block text-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $newsletter->total_failed }} failed
                                </small>
                            @endif
                        </div>
                    @else
                        <small class="text-muted">Not sent</small>
                    @endif
                </td>
                <td>{{ $newsletter->created_at->format('F j, Y') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn le" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <bold>•••</bold>
                        </button>
                        <ul class="dropdown-menu">
                            
                            <li><a class="dropdown-item" href="{{ route('newsletter.show', $newsletter->id) }}">View</a></li>
                            @if($newsletter->status === 'draft')
                                <li><a class="dropdown-item" href="{{ route('newsletter.edit', $newsletter->id) }}">Edit</a></li>
                                <li><a class="dropdown-item" href="{{ route('newsletter.send-options', $newsletter->id) }}">Send</a></li>
                            @endif
                            @if(in_array($newsletter->status, ['draft', 'scheduled']))
                                <li>
                                    <form action="{{ route('newsletter.destroy', $newsletter->id) }}" method="POST" class="dropdown-item text-danger delete-item-form" onsubmit="return confirm('Are you sure you want to delete this newsletter?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger border-0 bg-transparent">Delete</button>
                                    </form>
                                </li>
                            @endif
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-3">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-newspaper fa-2x text-muted mb-3 d-block"></i>
                        No newsletters found.
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