<?php

namespace Backendprogramer\RedirectPanel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Backendprogramer\RedirectPanel\RedirectPanel
 */
class RedirectPanel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Backendprogramer\RedirectPanel\RedirectPanel::class;
    }
}
