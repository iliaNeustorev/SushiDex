<label for="{{ $id }}" class="form-label">{{ $label }}</label>
<select name={{ $name }} class="form-select" aria-label="{{ $label }}">
    <option value=""></option>
    @foreach($options as $key => $value)
        <option value="{{ $key }}" @selected(old($name, $defaultValue) == $key)>
            {{ $value }}
        </option>
    @endforeach
</select>