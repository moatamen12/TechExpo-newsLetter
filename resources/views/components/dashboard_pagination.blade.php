@props('article')
@if ($article->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $article->links('pagination::bootstrap-5') }}
    </div>
@endif