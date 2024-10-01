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
    | Thumb Image Size
    |--------------------------------------------------------------------------
    |
    | Thumb image size for media
    */

    'thumb_image_size' => [
        'width' => env('MEDIA_THUMB_IMAGE_SIZE_WIDTH', 100),
        'height' => env('MEDIA_THUMB_IMAGE_SIZE_HEIGHT', 100),
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
            'image/bmp',
            'image/webp'
        ],
        'gif' => [
            'image/gif'
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
            'application/zip'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Webp Convert
    |--------------------------------------------------------------------------
    |
    | Image Webp Convert for media
    */

    'webp_convert' => [
        'enable' => env('MEDIA_WEBP_CONVERT_ENABLE', false),
        'quality' => env('MEDIA_WEBP_CONVERT_QUALITY', 80),
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
            'disk' => env("MEDIA_COLLECTION_PUBLIC_DISK", 'media_public'),
            "duplicate_content" => env("MEDIA_COLLECTION_PUBLIC_DUPLICATE_CONTENT", false),
            "max_size" => env("MEDIA_COLLECTION_PUBLIC_MAX_SIZE", -1), // -1: unlimited
            "mime_type" => env("MEDIA_COLLECTION_PUBLIC_MIME_TYPE", 'any'), // any: all mime type
        ],
        'private' => [
            'disk' => env("MEDIA_COLLECTION_PRIVATE_DISK", 'media_private'),
            "duplicate_content" => env("MEDIA_COLLECTION_PRIVATE_DUPLICATE_CONTENT", false),
            "max_size" => env("MEDIA_COLLECTION_PRIVATE_MAX_SIZE", -1), // -1: unlimited
            "mime_type" => env("MEDIA_COLLECTION_PRIVATE_MIME_TYPE", 'any'), // any: all mime type
        ],
        'avatar' => [
            'disk' => env("MEDIA_COLLECTION_AVATAR_DISK", 'media_avatar'),
            "duplicate_content" => env("MEDIA_COLLECTION_AVATAR_DUPLICATE_CONTENT", true),
            "max_size" => env("MEDIA_COLLECTION_AVATAR_MAX_SIZE", 1024 * 1024), // 1MB
            "mime_type" => env("MEDIA_COLLECTION_AVATAR_MIME_TYPE", 'image'), // image: image mime type
        ],
        'archive' => [
            'disk' => env("MEDIA_COLLECTION_ARCHIVE_DISK", 'media_archive'),
            "duplicate_content" => env("MEDIA_COLLECTION_ARCHIVE_DUPLICATE_CONTENT", false),
            "max_size" => env("MEDIA_COLLECTION_ARCHIVE_MAX_SIZE", 1024 * 1024 * 1024 * 3), // 3GB
            "mime_type" => env("MEDIA_COLLECTION_ARCHIVE_MIME_TYPE", 'archive'), // archive: archive mime type
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks In Media
    |--------------------------------------------------------------------------
    |
    | There can be three disks here, of course, according to the number
    | of collection groups defined above, their number changes.
    |
    | - public: collection public disk
    | - private: collection private disk
    | - avatar: collection avatar disk
    |
    | - Supported Drivers: "local", "ftp", "sftp", "s3"
    */

    'disks' => [
        'media_public' => [
            'driver' => 'local',
            'root' => public_path('media_uploads'),
            'url' => env('APP_URL').'/media/uploads',
            'visibility' => 'public',
            'throw' => false,
        ],
        'media_private' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'throw' => false,
        ],
        'media_avatar' => [
            'driver' => 'local',
            'root' => public_path('media_uploads/avatar'),
            'url' => env('APP_URL').'/media/uploads/avatar',
            'visibility' => 'public',
            'throw' => false,
        ],
        'media_archive' => [
            'driver' => 'local',
            'root' => storage_path('app/archive'),
            'throw' => false,
        ],
    ],

];
