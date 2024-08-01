<?php

namespace Saade\LaravelCodeStandards\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Saade\LaravelCodeStandards\LaravelCodeStandards
 */
class LaravelCodeStandards extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Saade\LaravelCodeStandards\LaravelCodeStandards::class;
    }
}
