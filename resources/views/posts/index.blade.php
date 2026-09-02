<x-layouts.main title="Все посты">
    <a href="{{ route('posts.create') }}">Create post</a>
    <x-form.form method="get" action="{{ route('posts.index') }}">
        <x-form.select name="status" :options="$statuses" label="Выберите статус" :defaultValue="$filters['status'] ?? ''" />
        <x-form.select name="category" :options="$categories" label="Выберите категорию" :defaultValue="$filters['category'] ?? ''"/>
        <x-form.input type="date" name='date' label='Выберите дату' :defaultValue="$filters['date'] ?? ''"/>
        <button type="submit">Применить</button>
    </x-form.form>
    <hr>
    <table>
        <tbody>
            @foreach($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>{{ $post->url }}</td>
                    <td>{{ $post->title }}</td>
                    <td>{{ $post->category->title ?? 'Без категории' }}</td>
                    <td>{{ $post->user->first_name }}</td>
                    <td><a href="{{ route('posts.show', $post->id) }}">Show</a></td>
                    <td><a href="{{ route('posts.edit', $post->id) }}">Edit</a></td>
                    <td>
                        <x-form.form method="delete" action="{{ route('posts.destroy', $post->id) }}">
                            <button type="submit">Delete</button>
                        </x-form.form>
                    </td>
                    @if($post->isDraft)
                        <td>
                        <form method="post" action="{{ route('posts.publish', $post->id) }}">
                            @csrf
                            @method('PATCH')
                            <button>Publish</button>
                        </form>
                            </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.main>