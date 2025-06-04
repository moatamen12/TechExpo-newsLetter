@props(['vars'])

<table class="table table-hover">
    <thead>
        <tr >
            <th scope="col">ID</th>
            <th scope="col">Title</th>
            <th scope="col">Satus</th>
            <th scope="col">Views</th>
            <th scope="col">Date</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @forelse ($vars as $var)
            <a href="{{route('articles.show', $var['article_id'])}}">
            <tr>
                <th scope="row">{{$var['article_id']}}</th>
                <td>{{$var['title']}}</td>
                <td>
                    @if ($var['statu'] == 'published')
                        <span class="badge published">Published</span>
                    @elseif ($var['statu'] == 'draft')
                        <span class="badge bg-secondary">Draft</span>
                    @elseif ($var['statu'] == 'archived')
                        <span class="badge bg-info">Archived</span>
                    @endif
                </td>
                <td>{{$var['view_count']}}</td>
                <td>{{$var['created_at']}}</td>
                <td>@mdo</td>
            </tr></a>
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