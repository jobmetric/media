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
        "errors" => "خطاهای اعتبار سنجی رخ داده است.",
        "rules" => [
            "media_exist" => "رسانه وجود ندارد!",
            "media_most_folder" => "رسانه باید یک پوشه باشد!",
            "media_most_file" => "رسانه باید فایل باشد!",
            "media_most_file_mime_type" => "نوع رسانه باید یکی از موارد زیر باشد: :mimeTypes",
            "media_collection_exist" => "مجموعه ':collection' در پیکربندی رسانه نیست!",
        ],
    ],

    "messages" => [
        "created" => "رسانه ':type' با موفقیت ایجاد شد!",
        "attached" => "رسانه با موفقیت ضمیمه شد!",
        "detached" => "رسانه با موفقیت جدا شد!",
        "rename" => "رسانه ':type' با موفقیت تغییر نام داده شد!",
        "details" => "جزئیات رسانه ':type' با موفقیت بازیابی شد!",
        "zipped" => "رسانه با موفقیت فشرده شد!",
        "deleted" => "':count' رسانه با موفقیت حذف شد!",
        "restored" => "':count' رسانه با موفقیت بازیابی شد!",
        "force_deleted" => "':count' رسانه با موفقیت حذف واقعی شد!",
    ],

    "exceptions" => [
        "model_media_contract_not_found" => "مدل ':model' از رابط 'JobMetric\Media\Contracts\MediaContract' پیروی نمی کند!",
        "media_id_not_found" => "رسانه با شناسه ':media_id' یافت نشد!",
        "media_uuid_not_found" => "رسانه با uuid ':media_uuid' یافت نشد!",
        "media_type_not_match" => "نوع رسانه با شناسه ':media_id' با ':type' مطابقت ندارد!",
        "media_collection_not_match" => "رسانه با شناسه ':media_id' دارای مجموعه ':media_collection' است، اما شما مجموعه ':collection' را ارسال کرده اید",
        "collection_not_in_media_allow_collection_method" => "مجموعه ':collection' در تابع 'media_allow_collections' نیست!",
        "media_relation_not_found" => "رابط رسانه برای ':mediaable_type' با شناسه ':mediaable_id' و شناسه رسانه ':media_id' یافت نشد!",
        "media_name_invalid" => "نام :type ':name' نامعتبر است!",
        "media_same_name" => "یک رسانه با نام ':name' در شاخه ای که در آن هستید وجود دارد و نمی توانید از این نام استفاده کنید!",
        "file_not_send_in_request" => "فایل در فیلد ':field' در درخواست ارسال نشده است!",
        "media_collection_not_in_config" => "مجموعه ':collection' در پیکربندی رسانه نیست!",
        "disk_not_defined_exception" => "دیسک ':disk' در پیکربندی سیستم فایل تعریف نشده است!",
        "media_max_size" => "اندازه فایل باید کمتر از :size کیلوبایت باشد!",
        "media_mime_type" => "نوع فایل ':mime_type' قابل قبول نیست، لطفا از آنهایی که مجاز هستند استفاده کنید!",
        "media_mime_type_not_in_groups" => "رسانه با شناسه ':id' و نوع ':mime_type' در گروه ':groups' نیست!",
        "media_must_in_same_folder" => "رسانه های انتخابی باید در یک پوشه باشند!",
        "media_ids_not_in_parent_id" => "شناسه های رسانه در شناسه والد نیستند!",
        "media_ids_already_in_trash" => "شناسه های رسانه ها از قبل در زباله دان وجود دارند!",
        "media_ids_already_not_trash" => "شناسه های رسانه ها از قبل در زباله دان نیستند!",
        "media_ids_used_in_other_object" => "شناسه های رسانه در سایر اشیاء استفاده شده است!",
    ],

    "media_type" => [
        "folder" => "پوشه",
        "file" => "فایل",
    ],

    'file_manager' => [
        'selector' => [
            'single' => [
                'select' => 'انتخاب فایل',
                'button' => [
                    'edit' => 'ویرایش',
                    'remove' => 'حذف',
                ]
            ],
            'multiple' => [
                'add' => 'افزودن فایل',
            ],
        ],
        'modal' => [
            'view' => [
                'toolbox' => [
                    'button' => [
                        'back' => 'برگشت',
                        'refresh' => 'بروزرسانی',
                        'new_folder' => 'پوشه جدید',
                        'remove' => 'حذف',
                        'recycle' => 'بازیابی',
                        'upload' => 'آپلود',
                        'upload_file' => 'آپلود فایل',
                    ],
                    'garbage' => 'نمایش زباله‌ها',
                    'search' => 'جستجو فایل',
                    'help' => 'راهنما',
                    'close' => 'بستن',
                    'select' => [
                        'limit' => [
                            'number' => ':number عدد',
                            'all' => 'همه',
                        ],
                        'view' => [
                            'name' => 'چیدمان',
                            'option' => [
                                'square' => 'شبکه‌ای',
                                'list' => 'فهرستی',
                            ],
                        ],
                        'sort' => [
                            'name' => 'مرتب سازی',
                            'option' => [
                                'name' => 'نام',
                                'date' => 'تاریخ',
                                'size' => 'اندازه',
                            ],
                        ],
                        'order' => [
                            'name' => 'ترتیب',
                            'option' => [
                                'asc' => 'صعودی',
                                'desc' => 'نزولی',
                            ],
                        ],
                    ],
                    'select_all' => 'انتخاب همه',
                    'details' => 'جزئیات',
                ],
                'upload_box' => [
                    'title' => 'لیست آپلودها',
                    'close' => 'بستن',
                ],
                'details_box' => [
                    'close' => 'بستن',
                ],
                'footer' => [
                    'uploads' => 'آپلودها',
                    'selected' => 'انتخاب',
                ],
                'uploader' => 'فایل خود را در اینجا رها کنید',
                'new_folder' => [
                    'title' => 'پوشه جدید',
                    'create' => 'ایجاد',
                ],
                'rename' => [
                    'title' => 'تغییر نام',
                    'save' => 'ذخیره',
                ],
                'question' => [
                    'title' => 'هشدار'
                ],
                'help' => [
                    'title' => 'راهنمای کلید های میانبر',
                    'option' => [
                        'open_help' => 'نمایش راهنما',
                        'new_folder' => 'پوشه جدید',
                        'upload' => 'آپلود فایل',
                        'toggle_details' => 'نمایش و عدم نمایش جزئیات',
                        'toggle_upload_box' => 'نمایش و عدم نمایش آپلودها',
                        'refresh' => 'بروزرسانی لیست آیتم‌ها',
                        'search' => 'جستجو',
                        'select_all' => 'انتخاب همه',
                        'multiple_choice' => 'انتخاب چندتایی',
                        'back' => 'برگشت به پوشه قبلی',
                        'delete' => 'حذف آیتم',
                        'select' => 'انتخاب فایل',
                        'rename' => 'تغییر نام',
                        'arrow' => 'حرکت بین آیتم‌ها',
                    ]
                ],
                'loading' => 'در حال بارگذاری',
            ],
            'code' => [
                'pagination' => 'نمایش {from} تا {to} از {total} مورد',
                'garbage' => [
                    'error' => [
                        'dont_rename' => 'در سطل زباله شما نمی‌توانید آیتم‌ها را تغییر نام دهید'
                    ]
                ],
                'rename' => [
                    'error' => [
                        'enter_name' => 'نام را وارد کنید'
                    ],
                    'loading' => 'در حال ذخیره سازی ...',
                    'save' => 'ذخیره',
                ],
            ],
        ],
    ],

];
