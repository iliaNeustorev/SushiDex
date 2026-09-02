<label for="{{ $id }}" class="form-label">{{ $label }}</label>
<input
    type="{{ $type }}" 
    class="form-control @error($name) is-invalid @enderror" 
    id="{{ $id }}" 
    placeholder="{{ $placeholder }}" 
    name="{{ $name }}" 
    value="{{ old($name, $defaultValue) }}"
>
@error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror