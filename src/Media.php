<?php

namespace JobMetric\Media;

use Illuminate\Contracts\Foundation\Application;
use JobMetric\Media\Object\File;
use JobMetric\Media\Object\Category;
use JobMetric\Media\Object\Common;

class Media
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * Create a new Translation instance.
     *
     * @param Application $app
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * get instance common object
     *
     * @return Common
     */
    public function common(): Common
    {
        return Common::getInstance();
    }

    /**
     * get instance category object
     *
     * @return Category
     */
    public function category(): Category
    {
        return Category::getInstance();
    }

    /**
     * get instance file object
     *
     * @return File
     */
    public function file(): File
    {
        return File::getInstance();
    }
}
