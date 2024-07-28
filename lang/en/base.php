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

    'validation' => [
        'errors' => 'Validation errors occurred.',
    ],

    'messages' => [
        'created' => 'Media ":type" created successfully!',
        'attached' => 'Media attached successfully!',
        'detached' => 'Media detached successfully!',
    ],

    'exceptions' => [
        'model_media_contract_not_found' => 'Model ":model" not implements "JobMetric\Media\Contracts\MediaContract" interface!',
        'media_not_found' => 'Media with id ":media_id" not found!',
        'media_type_not_match' => 'Media with id ":media_id" type not match with ":type"!',
        'media_collection_not_match' => 'Media with id ":media_id" has the ":media_collection" collection, but you send the ":collection" collection',
        'collection_not_in_media_allow_collection_method' => 'The ":collection" collection is not in the "media_allow_collections" function!',
        'media_relation_not_found' => 'Media relation not found for ":mediaable_type" with id ":mediaable_id" and media id ":media_id"!',
        'media_folder_name_invalid' => 'The folder name ":name" is invalid!',
    ],

];
