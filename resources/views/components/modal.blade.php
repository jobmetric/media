<div class="modal fade" tabindex="-1" id="modal-file-manager">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header py-3 fm-disable-selection">
                <div class="row w-100 ms-0">
                    <div class="col-lg-6 ps-0 order-1 order-lg-0">
                        <div class="fm-toolbox d-flex">
                            <button type="button" class="btn btn-icon btn-circle btn-secondary rounded-4 me-2" id="fm-btn-back" onclick="fm.page.actions.back()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="برگشت">
                                <i class="las la-arrow-right fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-circle btn-light-twitter rounded-4 me-2" id="fm-btn-refresh" onclick="fm.page.actions.refresh()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="بروزرسانی">
                                <i class="las la-sync fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-circle btn-light-info rounded-4 me-2" id="fm-btn-new-folder" onclick="fm.modal.new_folder.show()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="پوشه جدید">
                                <i class="las la-plus fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-circle btn-light-danger rounded-4 me-2" id="fm-btn-remove" onclick="fm.page.items.delete.process()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="حذف">
                                <i class="las la-trash fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-circle btn-light-warning rounded-4 me-2 d-none" id="fm-btn-recycle" onclick="fm.page.items.recycle.process()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="بازیابی">
                                <i class="las la-recycle fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon w-auto px-5 btn-light-facebook rounded-4 me-2" id="fm-btn-upload" onclick="fm.upload.select()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="آپلود فایل">
                                <i class="las la-cloud-upload-alt fs-1 me-3"></i>
                                <span>آپلود فایل</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-6 pe-0 mb-3 mb-lg-0 order-0 order-lg-1">
                        <div class="d-flex justify-content-end pe-0">
                            <div class="form-check form-switch form-check-custom form-check-solid me-5 fm-disable-selection">
                                <label class="form-check-label me-2 text-gray-600" for="fm-switch-garbage">نمایش زباله‌ها</label>
                                <input class="form-check-input" type="checkbox" value="" id="fm-switch-garbage" onchange="fm.page.mode.change()"/>
                            </div>
                            <div class="position-relative me-5">
                                <input type="text" class="form-control form-control-solid rounded-4 w-lg-400px pe-12" id="fm-search" onkeydown="fm.page.search.keydown(event)" placeholder="جستجو فایل"/>
                                <div class="position-absolute translate-middle-y top-50 end-0 me-3">
                                    <i class="ki-duotone ki-magnifier fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            <button type="button" class="btn btn-icon btn-circle btn-light-primary rounded-4 me-2" id="fm-btn-help" onclick="fm.help.show()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="راهنما">
                                <i class="las la-question fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-circle btn-light-google rounded-4" data-bs-dismiss="modal" data-bs-toggle="tooltip" data-bs-placement="bottom" title="بستن">
                                <i class="fa fa-close fs-3"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-body overflow-hidden p-0">
                <div class="row w-100 h-100 m-0">
                    <div class="col-12 h-100 border border-left-1 border-right-1 border-top-0 border-bottom-0" id="fm-box-files">
                        <div class="p-0 mx-n3 h-100">
                            <div class="fm-items-toolbox fm-disable-selection d-flex justify-content-between align-items-center py-3 px-7 border border-bottom-1 border-top-0 border-left-0 border-right-0">
                                <div class="row w-100 ms-0">
                                    <div class="col-lg-6 ps-0 order-1 order-lg-0">
                                        <div class="d-flex align-items-center">
                                            <div class="h-35px me-3">
                                                <select class="form-select rounded h-35px fs-8 w-100px" id="fm-select-limit">
                                                    <option value="100" selected>100 عدد</option>
                                                    <option value="150">150 عدد</option>
                                                    <option value="200">200 عدد</option>
                                                    <option value="250">250 عدد</option>
                                                    <option value="-1">همه</option>
                                                </select>
                                            </div>
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" id="fm-select-all" onchange="fm.page.actions.select_all.change()"/>
                                                <label class="form-check-label text-gray-600" for="fm-select-all">انتخاب همه</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 pe-0 mb-3 mb-lg-0 order-0 order-lg-1">
                                        <div class="d-flex justify-content-end pe-0">
                                            <div class="me-5 d-none d-lg-block">
                                                <div class="input-group input-group-sm flex-nowrap h-35px">
                                                        <span class="input-group-text text-gray-600">
                                                            <i class="ki-duotone ki-element-equal fs-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                                <span class="path5"></span>
                                                            </i>
                                                            <span class="ms-3">چیدمان</span>
                                                        </span>
                                                    <div class="overflow-hidden flex-grow-1">
                                                        <select class="form-select rounded-start-0 h-35px fs-8 w-125px appearance-none" id="fm-select-view">
                                                            <option value="square" data-class="fas fa-th-large" selected>شبکه‌ای</option>
                                                            <option value="list" data-class="fas fa-list">فهرستی</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="me-5 d-none d-lg-block">
                                                <div class="input-group input-group-sm flex-nowrap h-35px">
                                                        <span class="input-group-text text-gray-600">
                                                            <i class="ki-duotone ki-arrow-mix fs-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <span class="ms-3">مرتب سازی</span>
                                                        </span>
                                                    <div class="overflow-hidden flex-grow-1">
                                                        <select class="form-select rounded-start-0 h-35px fs-8 w-90px" id="fm-select-sort">
                                                            <option value="name" selected>نام</option>
                                                            <option value="created_at">تاریخ</option>
                                                            <option value="size">سایز</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-none d-lg-block">
                                                <div class="input-group input-group-sm flex-nowrap h-35px">
                                                        <span class="input-group-text text-gray-600">
                                                            <i class="ki-duotone ki-arrow-up-down fs-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <span class="ms-3">ترتیب</span>
                                                        </span>
                                                    <div class="overflow-hidden flex-grow-1">
                                                        <select class="form-select rounded-start-0 h-35px fs-8 w-125px" id="fm-select-order">
                                                            <option value="" data-class="fa fa-arrow-up-wide-short" selected>صعودی</option>
                                                            <option value="-" data-class="fa fa-arrow-down-wide-short">نزولی</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-light-info btn-active-light-info ms-6 w-100px" id="fm-btn-details" onclick="fm.page.details.toggle()">
                                                <i class="ki-duotone ki-book fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                </i>
                                                <span>جزئیات</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-7 h-100 fm-view-box position-relative" onclick="fm.page.items.item.click_outside(event)" data-view="square">
                                <div class="fm-items hover-scroll-y scroll-ps overflow-y-auto overflow-x-hidden fm-disable-selection"></div>
                                <div id="fm-upload-box" style="display: none">
                                    <div class="w-100 h-100 hover-scroll-y scroll-ps overflow-y-auto overflow-x-hidden fm-disable-selection position-relative">
                                        <div class="position-absolute w-100 mt-5 me-4 px-3 d-flex justify-content-between align-items-center">
                                            <span class="fs-6 fw-bold">لیست آپلودها</span>
                                            <button type="button" class="btn btn-icon btn-circle btn-light-google w-30px h-30px" onclick="fm.upload.uploadBox.hide()" data-bs-toggle="tooltip" data-bs-placement="top" title="بستن">
                                                <i class="fa fa-close fs-3"></i>
                                            </button>
                                        </div>
                                        <div id="fm-upload-box-items" class="mt-20 w-100 px-2"></div>
                                    </div>
                                </div>
                                <div id="fm-totals">
                                    <div id="fm-pagination">
                                        <ul class="pagination"></ul>
                                    </div>
                                    <div id="fm-total-show"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-3 h-100" id="fm-box-details" style="display: none">
                        <div class="h-100 hover-scroll-y scroll-ps overflow-y-auto overflow-x-hidden mx-n3 fm-disable-selection position-relative" dir="rtl">
                            <div class="position-absolute mt-5 ms-4">
                                <button type="button" class="btn btn-icon btn-circle btn-light-google w-30px h-30px" onclick="fm.page.details.hide()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="بستن">
                                    <i class="fa fa-close fs-3"></i>
                                </button>
                            </div>
                            <div class="p-7" id="fm-details-content"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer py-3 d-flex justify-content-between align-items-center fm-disable-selection">
                <div class="d-flex align-items-center">
                    <div>
                        <button class="btn btn-sm btn-light-info btn-active-light-info" id="fm-btn-upload-box-toggle">آپلودها</button>
                    </div>
                    <div class="fs-1 mx-5">|</div>
                    <ol class="breadcrumb text-gray-600 fs-6 fw-semibold" id="fm-breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0)" data-folder-id="">
                                <i class="fa fa-home"></i>
                            </a>
                        </li>
                    </ol>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" id="fm-selected" onclick="fm.selector.pick()">انتخاب</button>
                </div>
            </div>

            <form class="d-none" id="fm-uploader-form">
                <input type="file" name="files" id="fm-uploader-files" accept=".jpg,.jpeg,.png,.gif,.bmp,.webp,.svg,.mp3,.mp4,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip" multiple/>
            </form>

            <div id="fm-uploader" class="d-none">
                <div>
                    <i class="fa fa-plus"></i>
                    <span>فایل خود را در اینجا رها کنید</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-file-manager-new-folder">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow shadow-lg">
            <div class="modal-header py-5 fm-disable-selection">
                <h5 class="modal-title">پوشه جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body fm-disable-selection">
                <input type="text" class="form-control form-control-solid rounded-4" id="fm-text-new-folder">
                <div class="fs-7 text-danger mt-2 fm-error-new-folder"></div>
            </div>

            <div class="modal-footer py-3 fm-disable-selection">
                <button type="button" class="btn btn-info btn-sm" id="fm-btn-save-new-folder" onclick="fm.page.folder.new.save()">ایجاد</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-file-manager-rename">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow shadow-lg">
            <div class="modal-header py-5 fm-disable-selection">
                <h5 class="modal-title">تغییر نام</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body fm-disable-selection">
                <input type="hidden" id="fm-rename-element-id" value="">
                <input type="text" class="form-control form-control-solid rounded-4" id="fm-text-rename">
                <div class="fs-7 text-danger mt-2 fm-error-rename"></div>
            </div>

            <div class="modal-footer py-3 fm-disable-selection">
                <button type="button" class="btn btn-info btn-sm" id="fm-btn-save-rename" onclick="fm.page.items.rename.save()">ذخیره</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-file-manager-question">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow shadow-lg">
            <div class="modal-header py-5 fm-disable-selection">
                <h5 class="modal-title" id="fm-question-header">هشدار</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body fm-disable-selection">
                <div class="fs-6 text-gray-600" id="fm-question-text"></div>
            </div>

            <div class="modal-footer py-3 fm-disable-selection">
                <button type="button" class="btn btn-info btn-sm" id="fm-btn-question-confirm" onclick="fm.modal.question.confirm()"></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-file-manager-help">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow shadow-lg">
            <div class="modal-header py-5 fm-disable-selection">
                <h5 class="modal-title">راهنمای کلید های میانبر</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body fm-disable-selection">
                <table class="table table-hover">
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>H</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">باز کردن راهنما</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>N</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">پوشه جدید</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>U</code>
                            یا
                            <code>insert</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">آپلود فایل</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>D</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">نمایش و عدم نمایش جزئیات</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>Q</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">نمایش و عدم نمایش آپلودها</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>E</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">بروزرسانی لیست آیتم‌ها</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>S</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">جستجو</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>Ctrl</code>
                            +
                            <code>A</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">انتخاب همه</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>Ctrl</code>
                            +
                            <code>Space</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">انتخاب چندتایی</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>Backspace</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">برگشت به پوشه قبلی</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>Delete</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">حذف آیتم</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>Enter</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">انتخاب فایل</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>F2</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">تغییر نام</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code class="d-inline-block">
                                <i class="fa fa-arrow-up"></i>
                            </code>
                            <code class="d-inline-block">
                                <i class="fa fa-arrow-down"></i>
                            </code>
                            <code class="d-inline-block">
                                <i class="fa fa-arrow-left"></i>
                            </code>
                            <code class="d-inline-block">
                                <i class="fa fa-arrow-right"></i>
                            </code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">حرکت بین آیتم‌ها</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="fm-loading">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" width="200" height="200">
        <circle r="20" fill="#013ca6" cy="50" cx="30">
            <animate begin="-0.5s" values="30;70;30" keyTimes="0;0.5;1" dur="1s" repeatCount="indefinite" attributeName="cx"></animate>
        </circle>
        <circle r="20" fill="#fff" cy="50" cx="70">
            <animate begin="0s" values="30;70;30" keyTimes="0;0.5;1" dur="1s" repeatCount="indefinite" attributeName="cx"></animate>
        </circle>
        <circle r="20" fill="#013ca6" cy="50" cx="30">
            <animate begin="-0.5s" values="30;70;30" keyTimes="0;0.5;1" dur="1s" repeatCount="indefinite" attributeName="cx"></animate>
            <animate repeatCount="indefinite" dur="1s" keyTimes="0;0.499;0.5;1" calcMode="discrete" values="0;0;1;1" attributeName="fill-opacity"></animate>
        </circle>
    </svg>
    <div class="text-white prevent-select">در حال بارگذاری</div>
</div>