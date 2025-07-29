<?php

namespace JobMetric\Media\Enums;

use JobMetric\PackageCore\Enums\EnumMacros;

/**
 * @method static FOLDER()
 * @method static FILE()
 */
enum MediaTypeEnum: string
{
    use EnumMacros;

    case FOLDER = "c";
    case FILE = "f";
}
