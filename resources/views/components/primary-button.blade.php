
<div class="w-full hover:p-2 bg-blackSecondary rounded duration-300">
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'flex items-center justify-center w-full px-4 py-5 bg-blackThirdy rounded-md font-semibold text-xs text-white uppercase tracking-widest ']) }}>
    {{ $slot }}
    </button>
</div>