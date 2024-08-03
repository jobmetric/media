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
            ->hasRoute()
            ->registerClass('Media', Media::class);
    }

    /**
     * After Boot Package
     *
     * @return void
     */
    public function afterBootPackage(): void
    {
        // load media disk configuration
        foreach (config('media.disks') as $disk => $config) {
            config()->set("filesystems.disks.{$disk}", $config);
        }
    }
}
