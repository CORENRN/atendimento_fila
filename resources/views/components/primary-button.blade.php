<button {{ $attributes->merge(['type' => 'submit', 'class' => 'flex items-center justify-center w-full px-4 py-5 bg-[#527cd1] rounded-md font-semibold text-xs text-white hover:text-[#213555] uppercase tracking-widest hover:bg-[#88a7e4] transition duration-300']) }}>
    {{ $slot }}
</button>
