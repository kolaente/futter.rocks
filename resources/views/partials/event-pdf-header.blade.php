<h1>
    {{ $event->title }}
    @if(isset($subtitle))
        - {{ $subtitle }}
    @endif
</h1>
<p>
    {{ $event->date_from }} bis {{ $event->date_to }}
</p>
