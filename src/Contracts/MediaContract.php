<?php

namespace JobMetric\Media\Contracts;

interface MediaContract
{
    /**
     * media allow collections.
     *
     * @return array
     */
    public function mediaAllowCollections(): array;
}
