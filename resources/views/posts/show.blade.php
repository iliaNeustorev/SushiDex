<x-layouts.main title="{{ $post->title }}">
    <a href="{{ route('posts.index') }}">Все посты</a>
    <hr>
    <div>
        <p>Content - {{ $post->title }}</p>
        <p>URL - {{ $post->url }}</p>
        <p>{{ $post->category->title ?? 'Без категории' }}</p>
        <p>Create - {{ $post->created_at_formatted }}</p>
        <p>Update - {{ $post->updated_at_formatted }}</p>
    </div>
</x-layouts.main>