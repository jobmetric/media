<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class ModelMediaContractNotFoundException extends Exception
{
    public function __construct(string $model, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.model_media_contract_not_found', [
            'model' => $model
        ]), $code, $previous);
    }
}
