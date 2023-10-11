<?php

namespace Backendprogramer\RedirectPanel\Models;

use Backendprogramer\RedirectPanel\Traits\WriteToHtaccessFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedirectPanel extends Model
{
    use HasFactory;
    use WriteToHtaccessFile;

    protected $table = 'redirect_panels';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        // Deleting a redirect in the process of removal from the .htaccess file.
        static::deleted(function ($redirect) {
            self::writeNewLineToHtaccess($redirect->toArray(), 'delete');
        });
    }
}
