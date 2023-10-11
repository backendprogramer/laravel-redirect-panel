<?php

use Backendprogramer\RedirectPanel\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        Route::redirectPanel('redirect-panel');
    })
    ->in(__DIR__);
