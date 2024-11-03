<div class="modal fade" tabindex="-1" id="modal-file-manager" style="z-index: 999999">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header py-3 fm-disable-selection">
                <div class="row w-100 ms-0">
                    <div class="col-lg-6 ps-0 order-1 order-lg-0">
                        <div class="fm-toolbox d-flex">
                            <button type="button" class="btn btn-icon btn-circle btn-secondary rounded-4 me-2" id="fm-btn-back" onclick="fm.page.actions.back()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ trans('media::base.file_manager.modal.view.toolbox.button.back') }}">
                                <i class="las la-arrow-right fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-circle btn-light-twitter rounded-4 me-2" id="fm-btn-refresh" onclick="fm.page.actions.refresh()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ trans('media::base.file_manager.modal.view.toolbox.button.refresh') }}">
                                <i class="las la-sync fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-circle btn-light-info rounded-4 me-2" id="fm-btn-new-folder" onclick="fm.modal.new_folder.show()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ trans('media::base.file_manager.modal.view.toolbox.button.new_folder') }}">
                                <i class="las la-plus fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-circle btn-light-danger rounded-4 me-2" id="fm-btn-remove" onclick="fm.page.items.delete.process()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ trans('media::base.file_manager.modal.view.toolbox.button.remove') }}">
                                <i class="las la-trash fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-circle btn-light-warning rounded-4 me-2 d-none" id="fm-btn-recycle" onclick="fm.page.items.recycle.process()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ trans('media::base.file_manager.modal.view.toolbox.button.recycle') }}">
                                <i class="las la-recycle fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon w-auto px-5 btn-light-facebook rounded-4 me-2" id="fm-btn-upload" onclick="fm.upload.select()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ trans('media::base.file_manager.modal.view.toolbox.button.upload') }}">
                                <i class="las la-cloud-upload-alt fs-1 me-3"></i>
                                <span>{{ trans('media::base.file_manager.modal.view.toolbox.button.upload_file') }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-6 pe-0 mb-3 mb-lg-0 order-0 order-lg-1">
                        <div class="d-flex justify-content-end pe-0">
                            <div class="form-check form-switch form-check-custom form-check-solid me-5 fm-disable-selection">
                                <label class="form-check-label me-2 text-gray-600" for="fm-switch-garbage">{{ trans('media::base.file_manager.modal.view.toolbox.garbage') }}</label>
                                <input class="form-check-input" type="checkbox" value="" id="fm-switch-garbage" onchange="fm.page.mode.change()"/>
                            </div>
                            <div class="position-relative me-5">
                                <input type="text" class="form-control form-control-solid rounded-4 w-lg-400px pe-12" id="fm-search" onkeydown="fm.page.search.keydown(event)" placeholder="{{ trans('media::base.file_manager.modal.view.toolbox.search') }}"/>
                                <div class="position-absolute translate-middle-y top-50 end-0 me-3">
                                    <i class="ki-duotone ki-magnifier fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            <button type="button" class="btn btn-icon btn-circle btn-light-primary rounded-4 me-2" id="fm-btn-help" onclick="fm.help.show()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ trans('media::base.file_manager.modal.view.toolbox.help') }}">
                                <i class="las la-question fs-1"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-circle btn-light-google rounded-4" data-bs-dismiss="modal" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ trans('media::base.file_manager.modal.view.toolbox.close') }}">
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
                                                    <option value="100" selected>{{ trans('media::base.file_manager.modal.view.toolbox.select.limit.number', ['number' => 100]) }}</option>
                                                    <option value="150">{{ trans('media::base.file_manager.modal.view.toolbox.select.limit.number', ['number' => 150]) }}</option>
                                                    <option value="200">{{ trans('media::base.file_manager.modal.view.toolbox.select.limit.number', ['number' => 200]) }}</option>
                                                    <option value="250">{{ trans('media::base.file_manager.modal.view.toolbox.select.limit.number', ['number' => 250]) }}</option>
                                                    <option value="-1">{{ trans('media::base.file_manager.modal.view.toolbox.select.limit.all') }}</option>
                                                </select>
                                            </div>
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" id="fm-select-all" onchange="fm.page.actions.select_all.change()"/>
                                                <label class="form-check-label text-gray-600" for="fm-select-all">{{ trans('media::base.file_manager.modal.view.toolbox.select_all') }}</label>
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
                                                            <span class="ms-3">{{ trans('media::base.file_manager.modal.view.toolbox.select.view.name') }}</span>
                                                        </span>
                                                    <div class="overflow-hidden flex-grow-1">
                                                        <select class="form-select rounded-start-0 h-35px fs-8 w-125px appearance-none" id="fm-select-view">
                                                            <option value="square" data-class="fas fa-th-large" selected>{{ trans('media::base.file_manager.modal.view.toolbox.select.view.option.square') }}</option>
                                                            <option value="list" data-class="fas fa-list">{{ trans('media::base.file_manager.modal.view.toolbox.select.view.option.list') }}</option>
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
                                                            <span class="ms-3">{{ trans('media::base.file_manager.modal.view.toolbox.select.sort.name') }}</span>
                                                        </span>
                                                    <div class="overflow-hidden flex-grow-1">
                                                        <select class="form-select rounded-start-0 h-35px fs-8 w-90px" id="fm-select-sort">
                                                            <option value="name" selected>{{ trans('media::base.file_manager.modal.view.toolbox.select.sort.option.name') }}</option>
                                                            <option value="created_at">{{ trans('media::base.file_manager.modal.view.toolbox.select.sort.option.date') }}</option>
                                                            <option value="size">{{ trans('media::base.file_manager.modal.view.toolbox.select.sort.option.size') }}</option>
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
                                                            <span class="ms-3">{{ trans('media::base.file_manager.modal.view.toolbox.select.order.name') }}</span>
                                                        </span>
                                                    <div class="overflow-hidden flex-grow-1">
                                                        <select class="form-select rounded-start-0 h-35px fs-8 w-125px" id="fm-select-order">
                                                            <option value="" data-class="fa fa-arrow-up-wide-short" selected>{{ trans('media::base.file_manager.modal.view.toolbox.select.order.option.asc') }}</option>
                                                            <option value="-" data-class="fa fa-arrow-down-wide-short">{{ trans('media::base.file_manager.modal.view.toolbox.select.order.option.desc') }}</option>
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
                                                <span>{{ trans('media::base.file_manager.modal.view.toolbox.details') }}</span>
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
                                            <span class="fs-6 fw-bold">{{ trans('media::base.file_manager.modal.view.upload_box.title') }}</span>
                                            <button type="button" class="btn btn-icon btn-circle btn-light-google w-30px h-30px" onclick="fm.upload.uploadBox.hide()" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('media::base.file_manager.modal.view.upload_box.close') }}">
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
                                <button type="button" class="btn btn-icon btn-circle btn-light-google w-30px h-30px" onclick="fm.page.details.hide()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ trans('media::base.file_manager.modal.view.details_box.close') }}">
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
                        <button class="btn btn-sm btn-light-info btn-active-light-info" id="fm-btn-upload-box-toggle">{{ trans('media::base.file_manager.modal.view.footer.uploads') }}</button>
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
                    <button type="button" class="btn btn-primary btn-sm" id="fm-selected" onclick="fm.selector.pick()">{{ trans('media::base.file_manager.modal.view.footer.selected') }}</button>
                </div>
            </div>

            <form class="d-none" id="fm-uploader-form">
                <input type="file" name="files" id="fm-uploader-files" accept=".jpg,.jpeg,.png,.gif,.bmp,.webp,.svg,.mp3,.mp4,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip" multiple/>
            </form>

            <div id="fm-uploader" class="d-none">
                <div>
                    <i class="fa fa-plus"></i>
                    <span>{{ trans('media::base.file_manager.modal.view.uploader') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-file-manager-new-folder" style="z-index: 999999">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow shadow-lg">
            <div class="modal-header py-5 fm-disable-selection">
                <h5 class="modal-title">{{ trans('media::base.file_manager.modal.view.new_folder.title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body fm-disable-selection">
                <input type="text" class="form-control form-control-solid rounded-4" id="fm-text-new-folder">
                <div class="fs-7 text-danger mt-2 fm-error-new-folder"></div>
            </div>

            <div class="modal-footer py-3 fm-disable-selection">
                <button type="button" class="btn btn-info btn-sm" id="fm-btn-save-new-folder" onclick="fm.page.folder.new.save()">{{ trans('media::base.file_manager.modal.view.new_folder.create') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-file-manager-rename" style="z-index: 999999">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow shadow-lg">
            <div class="modal-header py-5 fm-disable-selection">
                <h5 class="modal-title">{{ trans('media::base.file_manager.modal.view.rename.title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body fm-disable-selection">
                <input type="hidden" id="fm-rename-element-id" value="">
                <input type="text" class="form-control form-control-solid rounded-4" id="fm-text-rename">
                <div class="fs-7 text-danger mt-2 fm-error-rename"></div>
            </div>

            <div class="modal-footer py-3 fm-disable-selection">
                <button type="button" class="btn btn-info btn-sm" id="fm-btn-save-rename" onclick="fm.page.items.rename.save()">{{ trans('media::base.file_manager.modal.view.rename.save') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-file-manager-question" style="z-index: 999999">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow shadow-lg">
            <div class="modal-header py-5 fm-disable-selection">
                <h5 class="modal-title" id="fm-question-header">{{ trans('media::base.file_manager.modal.view.question.title') }}</h5>
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

<div class="modal fade" tabindex="-1" id="modal-file-manager-help" style="z-index: 999999">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow shadow-lg">
            <div class="modal-header py-5 fm-disable-selection">
                <h5 class="modal-title">{{ trans('media::base.file_manager.modal.view.help.title') }}</h5>
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
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.open_help') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>N</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.new_folder') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>U</code>
                            یا
                            <code>insert</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.upload') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>D</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.toggle_details') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>Q</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.toggle_upload_box') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>E</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.refresh') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>shift</code>
                            +
                            <code>S</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.search') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>Ctrl</code>
                            +
                            <code>A</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.select_all') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>Ctrl</code>
                            +
                            <code>Space</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.multiple_choice') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>Backspace</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.back') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>Delete</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.delete') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>Enter</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.select') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 rounded rounded-4 rounded-end-0">
                            <code>F2</code>
                        </td>
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.rename') }}</td>
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
                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">{{ trans('media::base.file_manager.modal.view.help.option.arrow') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="fm-loading" style="z-index: 999999">
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
    <div class="text-white prevent-select">{{ trans('media::base.file_manager.modal.view.loading') }}</div>
</div>
