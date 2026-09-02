<x-layouts.main title="Регистрация">
    <div>
        <p>
            Регистрация
        </p>
        <x-form.form method='post' action="{{ route('register') }}">
            <x-form.input name="first_name" type="text" placeholder="Введите имя" label="Имя"/>
            <x-form.input name="email" type="email" placeholder="Введите почту" label="Email"/>
            <x-form.input name="password" type="password" placeholder="Введите пароль" label="Пароль(не менее 8 символов)"/>
            <x-form.input name="password_confirmation" type="password" placeholder="Повторите пароль" label="Повтор пароля"/>
            <button type="submit" class="btn btn-success">Зарегистрироваться</button>
        </x-form.form>
        <a href="{{ route('login') }}"><button class="button btn-success">Войти</button></a>
    </div>
</x-layouts.main>