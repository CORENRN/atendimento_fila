@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-2 border-primary bg-lightW py-3 dark:text-gray-300 focus:border-[#3368d1]  focus:ring-[#3368d1] rounded-md shadow-sm']) }}>
