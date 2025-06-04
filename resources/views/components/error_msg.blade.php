@props(['field'])
@error($field)
    <div {{ $attributes->merge(['class' => 'invalid-feedback d-block text-start']) }} >
        <small {{ $attributes->merge(['class' => 'text-danger fw-bold']) }} >{{ $message }}</small>
    </div>
@enderror