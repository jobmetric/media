<?php

namespace JobMetric\Media\Enums;

use JobMetric\PackageCore\Enums\EnumToArray;

/**
 * @method static FOLDER()
 * @method static FILE()
 */
enum MediaTypeEnum: string
{
    use EnumToArray;

    case FOLDER = "c";
    case FILE = "f";
}
