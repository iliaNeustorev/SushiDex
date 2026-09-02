<x-layouts.main title="Categories list">
    <a href="{{ route('categories.create') }}">Create category</a>
    <hr>
    <table class="table table-bordered table-hover">
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->url }}</td>
                    <td>
                        <a href="{{ route('categories.show', [ $category->id ]) }}">Read more</a> |
                        <a href="{{ route('categories.edit', [ $category->id ]) }}">Edit</a>
                    </td>
                     <td>
                        <x-form.form method="delete" action="{{ route('categories.destroy', $category->id) }}">
                            <button type="submit">Delete</button>
                        </x-form.form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.main>