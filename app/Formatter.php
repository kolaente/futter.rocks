<?php

namespace App;

use Illuminate\Support\Facades\App;
use NumberFormatter;

class Formatter
{
    protected ?NumberFormatter $formatter = null;

    public function getFormatter(): NumberFormatter
    {
        if ($this->formatter === null) {
            $this->formatter = new NumberFormatter(App::getLocale(), NumberFormatter::DECIMAL);
        }

        return $this->formatter;
    }

    public function format(int|float $value): string|false
    {
        return $this->getFormatter()->format($value);
    }
}
