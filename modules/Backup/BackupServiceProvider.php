<?php

namespace Modules\Backup;

use Modules\Support\BaseServiceProvider;

class BackupServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadViewsFrom(__DIR__.'/views', 'backup');
    }
}
