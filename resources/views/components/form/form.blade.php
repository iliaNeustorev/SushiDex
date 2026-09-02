<form method="{{ strtolower($method) === 'get' ? 'get' : 'post' }}" action="{{ $action }}" class="{{ $class }}">
    @if(strtolower($method) !== 'get')
        @csrf
        @method($method)
    @endif
    {{ $slot }}
</form>