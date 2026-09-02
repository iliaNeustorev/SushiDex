<x-layouts.main title="Create category">
   <x-form.form action="{{ route('categories.index') }}">
      <x-form.input label="Url" name="url" class="mb-3" />
      <x-form.input label="Title" name="title" class="mb-3" />
      <button>Send</button>
   </x-form.form>
</x-layouts.main>