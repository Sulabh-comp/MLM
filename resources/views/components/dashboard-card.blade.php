@props(['title', 'count', 'color'])

<div class="shadow-lg rounded-lg p-4" style="background-color: {{ $color ?? '#f8f9fc' }};">
    <h3 class="text-lg font-semibold">{{ $title }}</h3>
    <p class="text-2xl font-bold">{{ $count }}</p>
</div>
