<x-layouts.main title="Сброс пароля">
   <x-form.form action="{{ route('password.email') }}">
        <x-form.input name="email" type="email" placeholder="Введите почту"/>
      <button type="submit" class="button">Отправить</button>
   </x-form.form>
</x-layouts.main>