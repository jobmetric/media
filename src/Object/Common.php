<?php

namespace JobMetric\Media\Object;

class Common
{
    private static Common $instance;

    private ?string $name = null;
    private array $responsive = [];

    /**
     * get instance object
     *
     * @return Common
     */
    public static function getInstance(): Common
    {
        if(empty(Common::$instance)) {
            Common::$instance = new Common;
        }

        return Common::$instance;
    }

    /**
     * list common
     *
     * @return void
     */
    public function list(): void
    {

    }

    /**
     * compress common
     *
     * @return void
     */
    public function compress(): void
    {

    }

    /**
     * extract common
     *
     * @return void
     */
    public function extract(): void
    {

    }

    /**
     * share common
     *
     * @return void
     */
    public function share(): void
    {

    }

    /**
     * copy common
     *
     * @return void
     */
    public function copy(): void
    {

    }

    /**
     * cut common
     *
     * @return void
     */
    public function cut(): void
    {

    }

    /**
     * paste common
     *
     * @return void
     */
    public function paste(): void
    {

    }
}
