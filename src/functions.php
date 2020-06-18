<?php

use Monolith\Collections\Collection;

function d(...$targets): void
{
    echo '<pre>';
    var_dump(...$targets);
    echo '</pre>';
}

function dd(...$targets): void
{
    d(...$targets);
    exit;
}

function ds(...$targets): string
{
    return Collection::of($targets)
                     ->map(
                         function ($target) {
                             return var_export($target, true);
                         }
                     )->implode("\n");
}
