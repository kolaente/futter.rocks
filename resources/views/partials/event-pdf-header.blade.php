<h1>
    {{ $event->title }}
    @if(isset($subtitle))
        - {{ $subtitle }}
    @endif
</h1>
<p>
    {{ $event->duration_string }}
</p>
