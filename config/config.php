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
    | Table Name
    |--------------------------------------------------------------------------
    |
    | Table name in database
    */

    "tables" => [
        'media' => 'media',
        'media_path' => 'media_paths',
        'media_relation' => 'media_relations'
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Image Size
    |--------------------------------------------------------------------------
    |
    | Default image size for media
    */

    'default_image_size' => [
        'width' => env('MEDIA_DEFAULT_IMAGE_SIZE_WIDTH', 500),
        'height' => env('MEDIA_DEFAULT_IMAGE_SIZE_HEIGHT', 500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Mime Type
    |--------------------------------------------------------------------------
    |
    | Image mime type for media
    */

    'mime_type' => [
        'image' => [
            'image/jpg',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/webp'
        ],
        'svg' => [
            'image/svg+xml'
        ],
        'video' => [
            'video/mp4',
            'video/mpeg'
        ],
        'audio' => [
            'audio/mpeg'
        ],
        'pdf' => [
            'application/pdf',
        ],
        'word' => [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'excel' => [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ],
        'powerpoint' => [
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ],
        'archive' => [
            'application/zip',
            'application/vnd.rar'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Collections Type
    |--------------------------------------------------------------------------
    |
    | Collection type for media data, You can add more collection type
    |
    | - public: default collection
    | - private: collection for private user data
    | - avatar: collection for user avatar
    |
    | - disk: Select disk to storage data
    | - duplicate_content: If you want to avoid uploading duplicate files, disable this option
    */

    'collections' => [
        'public' => [
            'disk' => env("MEDIA_COLLECTION_PUBLIC_DISK", env('FILESYSTEM_DISK', 'local')),
            "duplicate_content" => env("MEDIA_COLLECTION_PUBLIC_DUPLICATE_CONTENT", false),
        ],
        'private' => [
            'disk' => env("MEDIA_COLLECTION_PRIVATE_DISK", env('FILESYSTEM_DISK', 'local')),
            "duplicate_content" => env("MEDIA_COLLECTION_PRIVATE_DUPLICATE_CONTENT", false),
        ],
        'avatar' => [
            'disk' => env("MEDIA_COLLECTION_AVATAR_DISK", env('FILESYSTEM_DISK', 'local')),
            "duplicate_content" => env("MEDIA_COLLECTION_AVATAR_DUPLICATE_CONTENT", true),
        ],
    ],

];
