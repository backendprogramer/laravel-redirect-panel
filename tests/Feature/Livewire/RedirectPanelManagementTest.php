<?php

use Backendprogramer\RedirectPanel\Livewire\RedirectPanelManagement;
use Backendprogramer\RedirectPanel\Models\RedirectPanel;

use function Pest\Livewire\livewire;

it('test_can_create_redirect', function () {
    $htaccessPath = public_path() . '/' . config('redirect-panel.htaccess', '.htaccess');
    if (!file_exists($htaccessPath)) {
        $beforeEnterLines = [];
    } else {
        $beforeEnterLines = file($htaccessPath);
    }

    livewire(RedirectPanelManagement::class)
        ->set('fromPath', '/old-page')
        ->set('toPath', '/new-page')
        ->set('type', '301')
        ->call('save');

    $afterEnteredLines = file($htaccessPath);
    $this->assertGreaterThan(count($beforeEnterLines), count($afterEnteredLines));

    $this->assertDatabaseHas('redirect_panels', [
        'from_path' => '/old-page',
        'to_path' => '/new-page',
        'type' => '301',
    ]);

});


it('test_can_update_redirect', function () {
    livewire(RedirectPanelManagement::class)
        ->set('fromPath', '/old-page')
        ->set('toPath', '/new-page')
        ->set('type', '301')
        ->call('save');
    $redirect = RedirectPanel::orderBy('id', 'DESC')->first();

    $htaccessPath = public_path() . '/' . config('redirect-panel.htaccess', '.htaccess');
    $beforeEnterLines = file($htaccessPath);

    livewire(RedirectPanelManagement::class)
        ->set('selectedId', $redirect->id)
        ->set('fromPath', '/updated-page')
        ->set('toPath', '/new-page')
        ->set('type', '302')
        ->call('update');

    $afterEnteredLines = file($htaccessPath);
    $this->assertEquals(count($beforeEnterLines), count($afterEnteredLines));

    $this->assertDatabaseHas('redirect_panels', [
        'from_path' => '/updated-page',
        'to_path' => '/new-page',
        'type' => '302',
    ]);
});

it('test_can_delete_redirect', function () {
    livewire(RedirectPanelManagement::class)
        ->set('fromPath', '/old-page')
        ->set('toPath', '/new-page')
        ->set('type', '301')
        ->call('save');
    $redirect = RedirectPanel::orderBy('id', 'DESC')->first();

    $htaccessPath = public_path() . '/' . config('redirect-panel.htaccess', '.htaccess');
    $beforeEnterLines = file($htaccessPath);

    livewire(RedirectPanelManagement::class)
        ->call('delete', $redirect->id);

    $afterEnteredLines = file($htaccessPath);
    $this->assertLessThan(count($beforeEnterLines), count($afterEnteredLines));

    $this->assertDatabaseMissing('redirect_panels', [
        'id' => $redirect->id,
    ]);
});
