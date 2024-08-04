<?php

namespace JobMetric\Media\Tests;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Tests\BaseDatabaseTestCase as BaseTestCase;

class BaseMedia extends BaseTestCase
{
    /**
     * create a fake image
     *
     * @param string $name
     *
     * @return UploadedFile
     */
    public function create_image(string $name = 'test.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($name);
    }

    /**
     * create a fake product
     *
     * @return Product
     */
    public function create_product(): Product
    {
        return Product::factory()->create();
    }
}
