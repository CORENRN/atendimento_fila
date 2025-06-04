@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-md text-[#213555]']) }}>
    {{ $value ?? $slot }}
</label>
