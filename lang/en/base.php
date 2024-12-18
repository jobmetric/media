<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base media Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during Media for
    | various messages that we need to display to the user.
    |
    */

    "validation" => [
        "errors" => "Validation errors occurred.",
        "rules" => [
            "media_exist" => "The media does not exist!",
            "media_most_folder" => "The media must be a folder!",
            "media_most_file" => "The media must be a file!",
            "media_most_file_mime_type" => "The media type must be one of the following: :mimeTypes",
            "media_collection_exist" => "The ':collection' collection is not in the media config!",
        ],
    ],

    "messages" => [
        "created" => "Media ':type' created successfully!",
        "attached" => "Media attached successfully!",
        "already_attached" => "Media already attached!",
        "detached" => "Media detached successfully!",
        "rename" => "Media ':type' renamed successfully!",
        "details" => "Media ':type' details retrieved successfully!",
        "zipped" => "Media zipped successfully!",
        "deleted" => "':count' media has been deleted successfully!",
        "restored" => "':count' media has been restored successfully!",
        "force_deleted" => "':count' media has been force deleted successfully!",
    ],

    "exceptions" => [
        "model_media_contract_not_found" => "Model ':model' not implements 'JobMetric\Media\Contracts\MediaContract' interface!",
        "media_id_not_found" => "Media with id ':media_id' not found!",
        "media_uuid_not_found" => "Media with uuid ':media_uuid' not found!",
        "media_type_not_match" => "Media with id ':media_id' type not match with ':type'!",
        "media_collection_not_match" => "Media with id ':media_id' has the ':media_collection' collection, but you send the ':collection' collection",
        "collection_not_in_media_allow_collection_method" => "The ':collection' collection is not in the 'media_allow_collections' function!",
        "media_relation_not_found" => "Media relation not found for ':mediaable_type' with id ':mediaable_id' and media id ':media_id'!",
        "media_name_invalid" => "The :type name ':name' is invalid!",
        "media_same_name" => "There is a media named ':name' in the branch you are in and you cannot use this name!",
        "file_not_send_in_request" => "File in field ':field' is not available in the sent request!",
        "media_collection_not_in_config" => "The ':collection' collection is not in the media config!",
        "disk_not_defined_exception" => "The ':disk' disk is not defined in the filesystem config!",
        "media_max_size" => "The file size must be less than :size kilobytes!",
        "media_mime_type" => "The file type ':mime_type' is not accepted, please use the allowed ones!",
        "media_mime_type_not_in_groups" => "The media with id ':id' and mime type ':mime_type' is not in the ':groups' group!",
        "media_must_in_same_folder" => "The selected media must be in the same folder!",
        "media_ids_not_in_parent_id" => "Media ids not in parent id!",
        "media_ids_already_in_trash" => "Media ids already in trash!",
        "media_ids_already_not_trash" => "Media ids already not in trash!",
        "media_ids_used_in_other_object" => "Media ids used in other object!",
    ],

    "media_type" => [
        "folder" => "Folder",
        "file" => "File",
    ],

    'file_manager' => [
        'selector' => [
            'single' => [
                'select' => 'Select File',
                'button' => [
                    'edit' => 'Edit',
                    'remove' => 'Remove',
                ]
            ],
            'multiple' => [
                'add' => 'Add Files',
            ],
        ],
        'modal' => [
            'view' => [
                'toolbox' => [
                    'button' => [
                        'back' => 'Back',
                        'refresh' => 'Refresh',
                        'new_folder' => 'New Folder',
                        'remove' => 'Remove',
                        'recycle' => 'Recycle',
                        'upload' => 'Upload',
                        'upload_file' => 'Upload File',
                    ],
                    'garbage' => 'Show Garbages',
                    'search' => 'Search File',
                    'help' => 'Help',
                    'close' => 'Close',
                    'select' => [
                        'limit' => [
                            'number' => ':number Number',
                            'all' => 'All',
                        ],
                        'view' => [
                            'name' => 'Layout',
                            'option' => [
                                'square' => 'Grid',
                                'list' => 'List',
                            ],
                        ],
                        'sort' => [
                            'name' => 'Sort',
                            'option' => [
                                'name' => 'Name',
                                'date' => 'Date',
                                'size' => 'Size',
                            ],
                        ],
                        'order' => [
                            'name' => 'Order',
                            'option' => [
                                'asc' => 'Ascending',
                                'desc' => 'Descending',
                            ],
                        ],
                    ],
                    'select_all' => 'Select All',
                    'details' => 'Details',
                ],
                'upload_box' => [
                    'title' => 'Upload List',
                    'close' => 'Close',
                ],
                'details_box' => [
                    'close' => 'Close',
                ],
                'footer' => [
                    'uploads' => 'Uploads',
                    'selected' => 'Select',
                ],
                'uploader' => 'Drop your file here',
                'new_folder' => [
                    'title' => 'New Folder',
                    'create' => 'Create',
                ],
                'rename' => [
                    'title' => 'Rename',
                    'save' => 'Save',
                ],
                'question' => [
                    'title' => 'Warning',
                ],
                'help' => [
                    'title' => 'Guide to shortcut keys',
                    'option' => [
                        'open_help' => 'Show help',
                        'new_folder' => 'New folder',
                        'upload' => 'Upload file',
                        'toggle_details' => 'Showing and not showing details',
                        'toggle_upload_box' => 'Showing and not showing uploads',
                        'refresh' => 'Update item list',
                        'search' => 'Search',
                        'select_all' => 'Select all',
                        'multiple_choice' => 'Multiple choice',
                        'back' => 'Back to the previous folder',
                        'delete' => 'Delete item',
                        'select' => 'Select file',
                        'rename' => 'Rename item',
                        'arrow' => 'Move between items',
                    ]
                ],
                'loading' => 'Loading',
            ],
            'code' => [
                'pagination' => 'Display {from} to {to} of {total} items',
                'garbage' => [
                    'error' => [
                        'dont_rename' => 'You cannot rename items in the trash'
                    ]
                ],
                'rename' => [
                    'error' => [
                        'enter_name' => 'Enter the name'
                    ]
                ],
            ],
        ],
    ],

];
