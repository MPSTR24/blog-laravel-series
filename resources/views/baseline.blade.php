<ul>
@foreach ($users as $user)
    <li>{{ $user['name'] }} ({{ $user['posts_count'] }} posts)</li>
@endforeach
</ul>
