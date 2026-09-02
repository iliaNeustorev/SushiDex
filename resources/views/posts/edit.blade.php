<x-layouts.main title="Редактировать пост {{ $post->title }}">
    <a href="{{ route('posts.index') }}">Все посты</a>
    <hr>
    <x-form.form method="put" action="{{ route('posts.update', $post->id) }}">
        <x-form.input name='url' label='URL' placeholder='Введите url' defaultValue="{{ $post->url }}"/>
        <x-form.input name='title' label='Заголовок' placeholder='Введите заголовок' defaultValue="{{ $post->title }}"/>
        <x-form.text-area name='content' label='Текст' placeholder='Введите текст' defaultValue="{{ $post->content }}"/>
        <x-form.select name="category_id" :options="$categories" label="Выберите категорию" defaultValue="{{ $post->category_id }}"/>
        <button class="btn btn-success">Изменить</button>
    </x-form.form>
</x-layouts.main>