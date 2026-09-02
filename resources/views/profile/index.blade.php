<x-layouts.main title="Your profile">
    {{ $user->first_name }}
    <hr>
     <x-form.form method="POST" action="{{ route('logout') }}">
        <button class="button btn-danger">
            Logout
        </button>
    </x-form.form>
</x-layouts.main>