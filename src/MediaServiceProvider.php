<?php

namespace JobMetric\Media;

use JobMetric\PackageCore\Exceptions\MigrationFolderNotFoundException;
use JobMetric\PackageCore\Exceptions\RegisterClassTypeNotFoundException;
use JobMetric\PackageCore\PackageCore;
use JobMetric\PackageCore\PackageCoreServiceProvider;

class MediaServiceProvider extends PackageCoreServiceProvider
{
    /**
     * package configuration
     *
     * @param PackageCore $package
     *
     * @return void
     * @throws MigrationFolderNotFoundException
     * @throws RegisterClassTypeNotFoundException
     */
    public function configuration(PackageCore $package): void
    {
        $package->name('media')
            ->hasConfig()
            ->hasTranslation()
            ->hasMigration()
            ->registerClass('Media', Media::class);
    }
}
