<x-layouts.main title="Сброс пароля">
    <div>
        <p>
            Сброс пароля
        </p>
        <x-form.form action="{{ route('password.update') }}">
            <x-form.input name="token" value="{{ $token }}" type="hidden"/>
            <x-form.input name="email" type="email" placeholder="Введите почту" label="Email"/>
            <x-form.input name="password" type="password" placeholder="Введите пароль" label="Пароль"/>
            <x-form.select name="password_confirmation" type="password" placeholder="Повторите пароль" label="Повтор пароля"/>
            <button type="submit" class="button">Сбросить пароль</button>
        </x-form.form>
    </div>
</x-layouts.main>