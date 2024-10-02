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
 * @method static TL()
 * @method static TOP_LEFT()
 * @method static TC()
 * @method static TOP_CENTER()
 * @method static TR()
 * @method static TOP_RIGHT()
 * @method static ML()
 * @method static MIDDLE_LEFT()
 * @method static C()
 * @method static CENTER()
 * @method static MC()
 * @method static MIDDLE_CENTER()
 * @method static MR()
 * @method static MIDDLE_RIGHT()
 * @method static BL()
 * @method static BOTTOM_LEFT()
 * @method static BC()
 * @method static BOTTOM_CENTER()
 * @method static BR()
 * @method static BOTTOM_RIGHT()
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

    case TL = "tl";
    case TOP_LEFT = "top_left";
    /**
     * Aligns the image to the top left corner of the given dimensions.
     */

    case TC = "tc";
    case TOP_CENTER = "top_center";
    /**
     * Aligns the image to the top center of the given dimensions.
     */

    case TR = "tr";
    case TOP_RIGHT = "top_right";
    /**
     * Aligns the image to the top right corner of the given dimensions.
     */

    case ML = "ml";
    case MIDDLE_LEFT = "middle_left";
    /**
     * Aligns the image to the middle left of the given dimensions.
     */

    case C = "c";
    case CENTER = "center";
    case MC = "mc";
    case MIDDLE_CENTER = "middle_center";
    /**
     * Aligns the image to the middle center of the given dimensions.
     */

    case MR = "mr";
    case MIDDLE_RIGHT = "middle_right";
    /**
     * Aligns the image to the middle right of the given dimensions.
     */

    case BL = "bl";
    case BOTTOM_LEFT = "bottom_left";
    /**
     * Aligns the image to the bottom left corner of the given dimensions.
     */

    case BC = "bc";
    case BOTTOM_CENTER = "bottom_center";
    /**
     * Aligns the image to the bottom center of the given dimensions.
     */

    case BR = "br";
    case BOTTOM_RIGHT = "bottom_right";
    /**
     * Aligns the image to the bottom right corner of the given dimensions.
     */
}
