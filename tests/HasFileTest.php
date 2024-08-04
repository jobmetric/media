<?php

namespace JobMetric\Media\Tests;

use App\Models\Product;
use Throwable;

class HasFileTest extends BaseMedia
{
    /**
     * @throws Throwable
     */
    public function test_files_trait_relationship()
    {
        $product = new Product();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphToMany::class, $product->files());
    }

    /**
     * @throws Throwable
     */
    public function test_attach(): void
    {
    }

    /**
     * @throws Throwable
     */
    public function test_detach(): void
    {
    }

    /**
     * @throws Throwable
     */
    public function test_get_media_by_collection(): void
    {
    }
}
