<?php

namespace JobMetric\Media;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Blade;
use JobMetric\PackageCore\Enums\RegisterClassTypeEnum;
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
            ->hasComponent()
            ->registerClass('Media', Media::class)
            ->registerClass('event', MediaEventServiceProvider::class, RegisterClassTypeEnum::REGISTER());
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

        // add alias for components
        Blade::component('media::components.file-manager', 'file-manager');

        // add pagination information to resource collection for add api_links to meta
        ResourceCollection::macro('paginationInformation', function ($request, $paginated, $default) {
            $default['meta']['api_links'] = [];

            $current_page = $default['meta']['current_page'];
            $last_page = $default['meta']['last_page'];

            if ($current_page > $last_page) {
                $current_page = $default['meta']['current_page'] = $last_page;
            }

            if ($last_page === 1) {
                return $default;
            }

            if ($current_page > 1) {
                $default['meta']['api_links'][] = [
                    'active' => false,
                    'label' => 'previous',
                    'page' => $current_page - 1,
                ];
            }

            $arrayItems[] = $current_page - 2;
            $arrayItems[] = $current_page - 1;
            $arrayItems[] = $current_page;
            $arrayItems[] = $current_page + 1;
            $arrayItems[] = $current_page + 2;

            foreach ($arrayItems as $arrayItem) {
                if ($arrayItem > 0 && $arrayItem <= $last_page) {
                    $default['meta']['api_links'][] = [
                        'active' => $arrayItem === $current_page,
                        'label' => $arrayItem,
                        'page' => $arrayItem,
                    ];
                }
            }

            if ($current_page < $last_page) {
                $default['meta']['api_links'][] = [
                    'active' => false,
                    'label' => 'next',
                    'page' => $current_page + 1,
                ];
            }

            return $default;
        });
    }
}
