<x-layouts.main :title="$category->title">
   <a href="{{ route('categories.index') }}">Back to list</a>
   <hr>
   @forelse ($category->posts as $post)
       <table>
        <tbody>
               <tr>
                  <td>{{ $post->id }}</td>
                  <td>{{ $post->title }}</td>
                  <td><a href="{{ route('posts.show', $post->id) }}">Show</a></td>
                  <td><a href="{{ route('posts.edit', $post->id) }}">Edit</a></td>
                  <td>
                     <x-form.form method="delete" action="{{ route('posts.destroy', $post->id) }}">
                           <button type="submit">Delete</button>
                     </x-form.form>
                  </td>
               </tr>
        </tbody>
    </table>
   @empty
       <p>Нет постов</p>
   @endforelse
</x-layouts.main>