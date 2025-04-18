<style>
    * {
        font-family: sans-serif;
        line-height: 1.25;
    }

    .header * {
        text-align: center;
    }

    table,
    thead,
    tbody,
    tfoot,
    tr,
    th,
    td {
        width: auto;
        height: auto;
        margin: 0;
        padding: 0;
        border-collapse: inherit;
        border-spacing: 0;
        text-align: left;
        -webkit-border-horizontal-spacing: 0;
        -webkit-border-vertical-spacing: 0;
        font-size: .75rem;
    }

    table {
        border: 1px solid #ccc;
        border-left: none;
        page-break-before: always;
    }

    th, td {
        padding: .25rem .5rem;
        border-left: 1px solid #ccc;
        vertical-align: top;
    }

    thead th {
        border-bottom: 1px solid #ccc;
    }
</style>

<div>
    <div class="header">
        @include('partials.event-pdf-header', ['event' => $event, 'subtitle' => __('Meal Plan')])
    </div>

    @include('partials.meal-plan', ['mealsByDate' => $mealsByDate])
</div>
