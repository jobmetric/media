<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Time
    |--------------------------------------------------------------------------
    |
    | Cache time for get data translation
    |
    | - set zero for remove cache
    | - set null for forever
    |
    | - unit: minutes
    */

    "cache_time" => env("MEDIA_CACHE_TIME", 0),

    /*
    |--------------------------------------------------------------------------
    | Collections
    |--------------------------------------------------------------------------
    |
    | Collection type
    */

    'collections' => [
        'public' => [
            // Select disk to storage data
            'disk'              => env("MEDIA_COLLECTION_PUBLIC_DISK", env('FILESYSTEM_DISK', 'local')),

            // If you want to avoid uploading duplicate files, disable this option
            "duplicate_content" => env("MEDIA_COLLECTION_PUBLIC_DUPLICATE_CONTENT", false),
        ],
        'avatar' => [
            // Select disk to storage data
            'disk'              => env("MEDIA_COLLECTION_AVATAR_DISK", env('FILESYSTEM_DISK', 'local')),

            // If you want to avoid uploading duplicate files, disable this option
            "duplicate_content" => env("MEDIA_COLLECTION_AVATAR_DUPLICATE_CONTENT", true),
        ],
    ],

];
