<label for="{{ $name }}" class="form-label">{{ $label }}</label>
<textarea class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" rows="{{ $rows }}" name="{{ $name }}">{{ old($name, $defaultValue) }}</textarea>
@error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror