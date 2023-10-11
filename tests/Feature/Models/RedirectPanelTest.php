<?php

use Backendprogramer\RedirectPanel\Models\RedirectPanel;

it('can create RedirectPanel', function () {
    $redirectPanel = RedirectPanel::factory()->create();

    $this->assertModelExists($redirectPanel);
});
