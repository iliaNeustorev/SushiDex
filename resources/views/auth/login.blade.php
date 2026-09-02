<x-layouts.main title="Форма входа">
   <x-form.form action="{{ route('login') }}">
        <x-form.input name="email" type="email" placeholder="Введите почту" label="Email"/>
        <x-form.input name="password" type="password" placeholder="Введите пароль" label="Пароль"/>
      <button type="submit" class="button">Войти</button>
   </x-form.form>
   <a href="{{ route('register') }}"><button class="button is-info mt-2">Регистрация</button></a>
   <div class="mt-2"><a href="{{ route('password.request') }}">Забыли пароль?</a></div>
</x-layouts.main>