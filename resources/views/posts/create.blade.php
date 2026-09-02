<x-layouts.main title="Новый пост">
    <x-form.form action="{{ route('posts.store') }}">
        <x-form.input name='url' label='URL' placeholder='Введите url'/>
        <x-form.input name='title' label='Заголовок' placeholder='Введите заголовок'/>
        <x-form.text-area name='content' label='Текст' placeholder='Введите текст'/>
        <x-form.select name="category_id" :options="$categories" label="Выберите категорию"/>
        <button class="btn btn-success">Создать</button>
    </x-form.form>
</x-layouts.main>