@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-xs text-blue-800 bg-blue-50 border border-blue-200 p-3 rounded-xl']) }}>
        {{ $status }}
    </div>
@endif
