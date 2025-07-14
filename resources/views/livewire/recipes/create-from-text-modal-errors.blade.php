@if (!empty($errors))
    <div class="p-4 text-sm text-red-600">
        <ul class="space-y-1 list-disc pl-4">
            @foreach ($errors as $error)
                <li>
                    <strong>{{ __('Line :line', ['line' => $error['line']]) }}:</strong>
                    {{ $error['content'] }} - {{ $error['error'] }}
                </li>
            @endforeach
        </ul>
    </div>
@endif
