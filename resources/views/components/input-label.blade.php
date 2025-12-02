@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-md text-lightW']) }}>
    {{ $value ?? $slot }}
</label>
