<x-layouts.main title="Удаленные посты">
    @if(session()->has('success'))
        <hr>
            {{ session()->get('success') }}
        <hr>
    @endif
    <table>
        <tbody>
            @foreach($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>{{ $post->url }}</td>
                    <td>{{ $post->title }}</td>
                    <td>{{ $post->category->title ?? 'Без категории' }}</td>
                    <td>
                        <x-form.form method="put" action="{{ route('post-trash.update', $post->id) }}">
                            <button type="submit">Restore</button>
                        </x-form.form>
                    </td>
                    <td>
                        <x-form.form method="delete" action="{{ route('post-trash.destroy', $post->id) }}">
                            <button type="submit">Delete</button>
                        </x-form.form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.main>