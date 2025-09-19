@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-3 pt-2 border-b-2 border-sky-700 dark:border-sky-700 text-base font-medium leading-6 text-sky-700 dark:text-sky-700 focus:outline-none focus:border-sky-600 transition duration-150 ease-in-out'
    : 'inline-flex items-center px-3 pt-2 border-b-2 border-transparent text-base font-medium leading-6 text-sky-500 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-400 hover:border-sky-300 dark:hover:border-sky-700 focus:outline-none focus:text-sky-700 dark:focus:text-sky-400 focus:border-sky-300 dark:focus:border-sky-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
