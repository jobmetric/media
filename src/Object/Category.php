<?php

namespace JobMetric\Media\Object;

use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Models\Media;

class Category
{
    private static Category $instance;

    private ?string $name = null;
    private array $responsive = [];

    /**
     * get instance object
     *
     * @return Category
     */
    public static function getInstance(): Category
    {
        if(empty(Category::$instance)) {
            Category::$instance = new Category;

            //event(new InitDocument);
        }

        return Category::$instance;
    }

    public function exist(int $folder = null, string $collection = 'public'): bool
    {
        if($folder == null) {
            return true;
        }

        if(Media::query()->where([
            'id'         => $folder,
            'collection' => $collection,
            'type'       => MediaTypeEnum::FOLDER->value
        ])->exists()) {
            return true;
        }

        return false;
    }

    /**
     * store category
     *
     * @return void
     */
    public function store(): void
    {

    }

    /**
     * delete category
     *
     * @return void
     */
    public function delete(): void
    {

    }

    /**
     * rename category
     *
     * @return void
     */
    public function rename(): void
    {

    }
}
