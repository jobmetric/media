<?php

if (!function_exists('getMimeGroup')) {
    /**
     * Get the mime group
     *
     * @param string $mime_type
     *
     * @return string
     */
    function getMimeGroup(string $mime_type): string
    {
        $config_mime_types = config('media.mime_type');

        $mime_group = null;
        foreach ($config_mime_types as $group => $mime_types) {
            if (in_array($mime_type, $mime_types)) {
                $mime_group = $group;
                break;
            }
        }

        return $mime_group;
    }
}
