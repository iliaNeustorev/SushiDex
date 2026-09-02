@php
    $notice = session('notice');
    $message = $notice ? config("notices.$notice") : null;
@endphp

@if($message)
<div class="alert alert-{{ $message['type'] }}">
    {{ $message['text'] }}
</div>
@endif