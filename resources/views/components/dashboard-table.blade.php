@props(['vars','tableID'])
<table class="table table-hover" id="{{ $tableID }}">
    <thead>
        <tr>
            <th scope="col">Title</th>
            <th scope="col">Category</th>
            <th scope="col">Status</th>
            <th scope="col">Reactions</th>
            <th scope="col">Publish Date</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody class="table-group-divider" id="{{ $tableID }}-content">
        @forelse ($vars as $var)
            <tr class="clickable-row" data-href="{{ route('articles.show', $var['article_id']) }}">
                <!-- TITLE COLUMN -->
                <td>
                    <div>
                        <strong>{{ $var['title'] }}</strong>
                    </div>
                </td>
                <!-- CATEGORY COLUMN -->
                <td>{{ $var->categorie->name ?? 'N/A' }}</td>
                <!-- STATUS COLUMN -->
                <td>
                    @if ($var['status'] == 'published')
                        <span class="badge bg-success text-white">
                            <i class="fas fa-check-circle me-1"></i>Published
                        </span>
                    @elseif ($var['status'] == 'draft')
                        <span class="badge bg-secondary text-white">
                            <i class="fas fa-edit me-1"></i>Draft
                        </span>
                    @elseif ($var['status'] == 'scheduled')
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-clock me-1"></i>Scheduled
                        </span>
                    @endif
                </td>
                <!-- REACTIONS COLUMN -->
                <td>
                    <div>
                        <small class="d-block text-muted">
                            <i class="fas fa-eye me-1"></i>{{ $var['view_count'] ?? 0 }}
                        </small>
                        <small class="d-block text-muted">
                            <i class="fas fa-heart me-1 text-danger"></i>{{ $var['like_count'] ?? 0 }}
                        </small>
                    </div>
                </td>
                <!-- PUBLISH DATE COLUMN -->
                <td>{{ $var['created_at']->format('M j, Y') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn le" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <bold>•••</bold>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('articles.show', $var['article_id']) }}">
                                <i class="fas fa-eye me-2"></i>View Article
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('articles.show', $var['article_id']) }}">
                                <i class="fas fa-paper-plane me-2"></i>Send As Newsletter
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('articles.edit', $var['article_id']) }}">
                                <i class="fas fa-edit me-2"></i>Edit
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{route('articles.destroy',$var['article_id'])}}" method="POST" class="dropdown-item text-danger delete-item-form p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link dropdown-item text-danger" 
                                            onclick="return confirm('Are you sure you want to delete this article?')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
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