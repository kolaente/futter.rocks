<style>
    * {
        font-family: sans-serif;
        line-height: 1.5;
        text-align: center;
    }

    p {
        margin: .5rem 0 .5rem;
    }

    .shopping-tour-list {
        text-align: left;
    }

    ul, .shopping-tour-list {
        padding-left: 2rem;
    }

    ul li {
        text-align: left;
        font-size: .75rem;
        list-style-type: none;
    }

    .check {
        display: inline-block;
        border: 1px solid #000;
        width: .75em;
        height: .75em;
        vertical-align: baseline;
        margin-bottom: -1px;
    }
</style>

<div>
    @include('partials.event-pdf-header', ['event' => $event, 'subtitle' => __('Shopping List')])

    @include('partials.shopping-list', ['event' => $event])
</div>
