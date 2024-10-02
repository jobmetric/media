<?php

namespace JobMetric\Media\Enums;

use JobMetric\PackageCore\Enums\EnumToArray;

/**
 * @method static SCALE()
 * @method static CONTAIN()
 * @method static COVER()
 * @method static FIT()
 * @method static FILL()
 * @method static STRETCH()
 * @method static CENTER()
 */
enum MediaImageResponsiveModeEnum: string
{
    use EnumToArray;

    case SCALE = "scale";
    /**
     * Scales the image proportionally based on the width, maintaining the aspect ratio.
     */

    case CONTAIN = "contain";
    /**
     * Fits the image inside the given dimensions without cropping. Adds padding to maintain the aspect ratio.
     */

    case COVER = "cover";
    /**
     * Scales the image to cover the entire area of the given dimensions, potentially cropping the image.
     */

    case FIT = "fit";
    /**
     * Proportionally scales the image to fit within the given dimensions.
     */

    case FILL = "fill";
    /**
     * Stretches the image to fill the given dimensions without maintaining the aspect ratio.
     */

    case STRETCH = "stretch";
    /**
     * Stretches the image to fit exactly into the given dimensions, distorting the image if necessary.
     */

    case CENTER = "center";
    /**
     * Centers the image within the given dimensions, adding padding around the image as needed.
     */
}
