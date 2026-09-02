<x-layouts.main title="Edit category">
   <x-form.form method="put" action="{{ route('categories.update', $category->id) }}">
      <x-form.input label="Url" name="url" class="mb-3" :defaultValue="$category->url" />
      <x-form.input label="Title" name="title" class="mb-3" :defaultValue="$category->title" />
      <button>Save</button>
   </x-form.form>
</x-layouts.main>