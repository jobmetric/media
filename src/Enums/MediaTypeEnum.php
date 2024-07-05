<?php

namespace JobMetric\Media\Enums;

use JobMetric\PackageCore\Enums\EnumToArray;

enum MediaTypeEnum: string
{
    use EnumToArray;

    case FOLDER = "c";
    case FILE = "f";
}
