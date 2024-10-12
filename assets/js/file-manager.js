"use strict"

const fm = {
    init: {
        ready: function () {
            fm.modal.file_manager.init()
            fm.modal.new_folder.init()
            fm.modal.rename.init()
            fm.modal.question.init()
            fm.modal.help.init()

            fm.select2.view.init()
            fm.select2.sort.init()
            fm.select2.order.init()
            fm.select2.limit.init()

            fm.contextmenu.items()
            fm.contextmenu.file()
            fm.contextmenu.folder()
        }
    },
    page: {
        mode: {
            is: {
                normal: function () {
                    return !!!$('#fm-switch-garbage').is(':checked')
                },
                garbage: function () {
                    return !!$('#fm-switch-garbage').is(':checked')
                },
            },
            in: {
                normal: function () {
                    $('#fm-btn-back').removeClass('d-none')
                    $('#fm-btn-new-folder').removeClass('d-none')
                    $('#fm-btn-recycle').addClass('d-none')
                    $('#fm-btn-upload').removeClass('d-none')
                    $('#modal-file-manager .modal-footer').removeClass('d-none')
                    $('#fm-search').val('')
                },
                garbage: function () {
                    $('#fm-btn-back').addClass('d-none')
                    $('#fm-btn-new-folder').addClass('d-none')
                    $('#fm-btn-recycle').removeClass('d-none')
                    $('#fm-btn-upload').addClass('d-none')
                    $('#modal-file-manager .modal-footer').addClass('d-none')
                    $('#fm-search').val('')
                },
            },
            change: function(){
                if (fm.page.mode.is.garbage()) {
                    fm.page.mode.in.garbage()
                } else {
                    fm.page.mode.in.normal()
                }
                fm.page.items.get()
            }
        },
        parent: {
            id: '',
            change: function(id) {
                this.id = id
                fm.page.details.hide()
            },
            get: function() {
                return this.id
            },
        },
        search: {
            get: function () {
                return $('#fm-search').val()
            },
            keydown: function(e){
                // enter
                if (e.which === 13) {
                    e.preventDefault()
                    fm.page.items.get()
                }
                // ctrl + A
                if (e.ctrlKey && e.which === 65) {
                    e.preventDefault()
                    $(e.target).select()
                }
            }
        },
        sort: {
            get: function () {
                return $('#fm-select-sort').val()
            }
        },
        order: {
            get: function () {
                return $('#fm-select-order').val()
            }
        },
        limit: {
            get: function () {
                return $('#fm-select-limit').val()
            }
        },
        actions: {
            back: function () {
                let last_breadcrumb = fm.page.breadcrumb.pop()
                let last = fm.page.breadcrumb.last()
                let folder_id = last?.id

                fm.page.parent.change(folder_id)

                let status_open = fm.page.items.get()

                if (!status_open) {
                    fm.page.breadcrumb.push(last_breadcrumb.id, last_breadcrumb.name)
                    fm.page.parent.change(last_breadcrumb.id)
                }
            },
            refresh: function () {
                fm.page.items.get()
            },
            select_all: {
                change: function() {
                    if ($('#fm-select-all').is(':checked')) {
                        $('.fm-item').addClass('active')
                    } else {
                        $('.fm-item').removeClass('active')
                    }
                },
                observer: function(){
                    let flag = true
                    $('.fm-item').each(function () {
                        if (!$(this).hasClass('active')) {
                            flag = false
                            return false
                        }
                    })

                    if (flag) {
                        $('#fm-select-all').prop('checked', true)
                    } else {
                        $('#fm-select-all').prop('checked', false)
                    }
                }
            }
        },
        items: {
            render: {
                items: function (items) {
                    if (items.length === 0) {
                        $('.fm-items').html('')
                    } else {
                        let theme = ''
                        $.each(items, function (i, item) {
                            let id = item.id
                            let type = item.type
                            let name = item.name
                            let uuid = item.uuid
                            let deleted_at = item.deleted_at
                            let created_at = item.created_at
                            let updated_at = item.updated_at

                            if (type === 'c') {
                                theme += `<div class="fm-item fm-item-folder" onclick="fm.page.items.item.click(this, event)" ondblclick="fm.page.items.item.folder.dblclick(this, event)" data-id="${id}" data-name="${name}" data-size="10002" data-uuid="${uuid}" data-children-count="1" data-created-at="${created_at}" data-updated-at="${updated_at}" data-deleted-at="${deleted_at}">
                                            <div>
                                                <i class="fa fa-folder fs-5x text-warning"></i>
                                            </div>
                                            <div>${name}</div>
                                        </div>`
                            } else {
                                let size = item.size
                                let mime_type = item.mime_type
                                let mime_group = item.mime_group
                                let content_id = item.content_id
                                let disk = item.disk
                                let collection = item.collection
                                let extension = item.extension
                                let src = item.src

                                theme += `<div class="fm-item fm-item-file" onclick="fm.page.items.item.click(this, event)" ondblclick="fm.page.items.item.file.dblclick(this, event)" data-id="${id}" data-name="${name}" data-size="${size}" data-uuid="${uuid}" data-mime="${mime_type}" data-content="${content_id}" data-disk="${disk}" data-collection="${collection}" data-extension="${extension}" data-src="${src}" data-created-at="${created_at}" data-updated-at="${updated_at}" data-deleted-at="${deleted_at}">
                                        <div class="fm-item-file-extension" data-type="${mime_group}"></div>
                                        <div class="fm-item-file-content">`

                                if ($.inArray(mime_group, ['image', 'gif', 'svg']) !== -1) {
                                    theme += `<img src="${src}" alt="${name}">`
                                } else {
                                    theme += fm.icon.get(mime_group)
                                }
                                theme += `</div>
                                        <div class="fm-item-file-name">${name}</div>
                                    </div>`
                            }
                        })

                        $('.fm-items').html(theme)
                    }
                },
                pagination: function (meta) {
                    if (meta === undefined) {
                        $('#fm-totals').addClass('d-none')
                    } else {
                        $('#fm-totals').removeClass('d-none')

                        if (meta.from !== null && meta.to !== null && meta.total !== null) {
                            let text_template = localize?.fm?.trans?.pagination
                            text_template = text_template.replace('{from}', meta.from).replace('{to}', meta.to).replace('{total}', meta.total)
                            $('#fm-total-show').text(text_template)
                        } else {
                            $('#fm-total-show').text('')
                        }

                        if (meta.last_page === 1) {
                            $('#fm-pagination').addClass('d-none')
                        } else {
                            $('#fm-pagination').removeClass('d-none')

                            let pagination = ''
                            if (meta.api_links.length > 0) {
                                $.each(meta.api_links, function (i, item) {
                                    switch (item.label) {
                                        case 'previous':
                                            pagination += `<li class="page-item previous">
                                                    <a href="javascript:void(0)" class="page-link" onclick="fm.page.items.get(${item.page})">
                                                        <i class="previous"></i>
                                                    </a>
                                                </li>`
                                            break
                                        case 'next':
                                            pagination += `<li class="page-item next">
                                                    <a href="javascript:void(0)" class="page-link" onclick="fm.page.items.get(${item.page})">
                                                        <i class="next"></i>
                                                    </a>
                                                </li>`
                                            break
                                        default:
                                            let active = item.active ? ' active' : ''
                                            pagination += `<li class="page-item${active}">
                                                    <a href="javascript:void(0)" class="page-link" onclick="fm.page.items.get(${item.page})">${item.label}</a>
                                                </li>`
                                    }
                                })
                            }

                            $('#fm-pagination > ul').html(pagination)
                        }
                    }
                }
            },
            get: function(page = 1) {
                let mode = null
                if (fm.page.mode.is.garbage()) {
                    mode = 'onlyTrashed'
                }

                let parent_id = fm.page.parent.get()
                let search = fm.page.search.get()
                let sort = fm.page.sort.get()
                let order = fm.page.order.get()
                let limit = fm.page.limit.get()

                let status = false

                $.ajax({
                    url: 'media',
                    method: 'get',
                    dataType: 'json',
                    async: false,
                    data: $.param({
                        mode: mode,
                        filter: {
                            parent_id: parent_id,
                            name: search
                        },
                        with: [
                            'mediaRelations',
                            'paths'
                        ],
                        sort: 'type,' + order + sort,
                        page_limit: limit,
                        page: page
                    }),
                    beforeSend: function () {
                        fm.page.loading.show()
                    },
                    complete: function () {
                        fm.page.loading.hide()
                    },
                    success: function (json) {
                        fm.page.items.render.items(json.data)
                        fm.page.items.render.pagination(json.meta)

                        status = true
                        return true
                    },
                    error: function () {
                        status = false
                    }
                })

                return status
            },
            rename: {
                show: function (element) {
                    $('#fm-rename-element-id').val($(element).data('id'))
                    $('#fm-text-rename').val($(element).data('name'))

                    $('#modal-file-manager-rename').modal('show')
                    setTimeout(function () {
                        $('#fm-text-rename').focus()
                    }, 500)
                },
                save: function () {
                    if (fm.page.mode.is.garbage()) {
                        fm.helper.alert.error(localize?.fm?.trans?.garbage?.error?.dont_rename)
                        return
                    }

                    let element_id = $('#fm-rename-element-id').val()
                    let name = $('#fm-text-rename').val()

                    if (name.length === 0) {
                        fm.helper.alert.error(localize?.fm?.trans?.garbage?.error?.dont_rename)
                        $('#fm-text-rename').focus()
                        return
                    }

                    $.ajax({
                        url: 'media/rename/' + element_id,
                        method: 'post',
                        dataType: 'json',
                        data: {
                            name: name
                        },
                        beforeSend: function () {
                            $('.fm-error-rename').text('')
                            $('#fm-btn-save-rename').text(localize?.fm?.trans?.rename?.loading).attr('disabled', true)
                        },
                        complete: function () {
                            $('#fm-btn-save-rename').text(localize?.fm?.trans?.rename?.save).attr('disabled', false)
                        },
                        success: function (json) {
                            fm.helper.alert.success(json.message)
                            $('#modal-file-manager-rename').modal('hide')
                            fm.page.items.get()
                        },
                        error: function (dataErrors) {
                            if (dataErrors.responseJSON?.message) {
                                fm.helper.alert.error(dataErrors.responseJSON.message)
                            }

                            if (dataErrors.responseJSON?.errors?.name) {
                                $('#fm-text-rename').next('div.fm-error-rename').text(dataErrors.responseJSON?.errors?.name.join('<br>'))
                            }
                        },
                    })
                },
            },
            arrow: {
                move: function (direction, hasCtrl) {
                    if (!$('.fm-item').hasClass('selector')) {
                        $('.fm-item:first').addClass('selector active')
                        return
                    }

                    let remInPixels = parseFloat($("html").css("font-size"))

                    let view = $('.fm-view-box').data('view')
                    let items = $('.fm-item')

                    let currentIndex = $('.fm-item.selector').index()
                    let countItems = items.length

                    let itemsWidth = parseFloat($('.fm-items').css('width'))
                    // view square
                    if (view === 'square') {
                        let itemWidth = parseFloat($('.fm-item:first').css('width')) + remInPixels

                        if (direction === 'up') {
                            let row = Math.floor(itemsWidth / itemWidth)
                            if (currentIndex - row < 0) {
                                return
                            } else {
                                if (hasCtrl) {
                                    $('.fm-item').removeClass('selector')
                                    items.eq(currentIndex - row).addClass('selector')
                                } else {
                                    $('.fm-item').removeClass('selector active')
                                    items.eq(currentIndex - row).addClass('selector active')
                                }
                            }
                        }
                        if (direction === 'down') {
                            let row = Math.floor(itemsWidth / itemWidth)
                            if (currentIndex + row >= countItems - 1) {
                                let rowCount = Math.ceil(countItems / row)
                                if (((rowCount - 1) * row) - 1 < currentIndex) {
                                    return
                                } else {
                                    if (hasCtrl) {
                                        $('.fm-item').removeClass('selector')
                                        items.eq(countItems - 1).addClass('selector')
                                    } else {
                                        $('.fm-item').removeClass('selector active')
                                        items.eq(countItems - 1).addClass('selector active')
                                    }
                                }
                            } else {
                                if (hasCtrl) {
                                    $('.fm-item').removeClass('selector')
                                    items.eq(currentIndex + row).addClass('selector')
                                } else {
                                    $('.fm-item').removeClass('selector active')
                                    items.eq(currentIndex + row).addClass('selector active')
                                }
                            }
                        }
                        if (direction === 'left') {
                            if (currentIndex === countItems - 1) {
                                return
                            } else {
                                if (hasCtrl) {
                                    $('.fm-item').removeClass('selector')
                                    items.eq(currentIndex + 1).addClass('selector')
                                } else {
                                    $('.fm-item').removeClass('selector active')
                                    items.eq(currentIndex + 1).addClass('selector active')
                                }
                            }
                        }
                        if (direction === 'right') {
                            if (currentIndex === 0) {
                                return
                            } else {
                                if (hasCtrl) {
                                    $('.fm-item').removeClass('selector')
                                    items.eq(currentIndex - 1).addClass('selector')
                                } else {
                                    $('.fm-item').removeClass('selector active')
                                    items.eq(currentIndex - 1).addClass('selector active')
                                }
                            }
                        }
                    }

                    // view list
                    if (view === 'list') {

                    }
                }
            },
            multiselect: function () {
                // ctrl + space
                if (!$('.fm-item').hasClass('selector')) {
                    $('.fm-item:first').addClass('selector active')
                    return
                }

                $('.fm-item.selector').toggleClass('active')
            },
            item: {
                click: function(element, e){
                    $('.fm-item').removeClass('selector')
                    if (e.ctrlKey) {
                        $(element).toggleClass('active')
                    } else {
                        $('.fm-item').removeClass('active')
                        $(element).addClass('active')
                    }

                    $(element).addClass('selector')
                },
                click_outside: function(e){
                    if (!$(e.target).closest('.fm-item').length) {
                        $('.fm-item').removeClass('active')
                    }
                },
                select: function (element, hasActive, hasSelector) {
                    let active = hasActive ? 'active' : ''
                    let selector = hasSelector ? 'selector' : ''

                    $('.fm-item').removeClass(`${active} ${selector}`)
                    $(element).addClass(`${active} ${selector}`)
                },
                file: {
                    dblclick: function(element){
                        if (fm.page.mode.is.normal()) {
                            fm.selector.pick(element)
                        }
                    }
                },
                folder: {
                    dblclick: function(element){
                        if (fm.page.mode.is.normal()) {
                            fm.page.folder.open(element)
                        }
                    }
                },
            },
            delete: {
                process: function(element) {
                    if (element === undefined) {
                        let items = $('.fm-item.active')
                        if (items.length === 0) {
                            fm.helper.alert.error('هیچ آیتمی انتخاب نشده است')
                            return
                        }

                        let ids = []
                        items.each(function (i, item) {
                            ids.push($(item).data('id'))
                        })

                        fm.modal.question.fire({
                            text: 'آیا از حذف این آیتم‌ها اطمینان دارید؟',
                            callback: function () {
                                fm.page.items.delete.send(ids)
                            }
                        })
                    } else if (element === 'all') {
                        let items = $('.fm-item')
                        if (items.length === 0) {
                            fm.helper.alert.error('هیچ آیتمی انتخاب نشده است')
                            return
                        }

                        let ids = []
                        items.each(function (i, item) {
                            ids.push($(item).data('id'))
                        })

                        fm.modal.question.fire({
                            text: 'آیا از حذف همه آیتم‌ها اطمینان دارید؟',
                            callback: function () {
                                fm.page.items.delete.send(ids)
                            }
                        })
                    } else {
                        let id = $(element).data('id')

                        let ids = []
                        ids.push(id)

                        fm.modal.question.fire({
                            text: `آیا از حذف آیتم‌ شماره ${id} اطمینان دارید؟`,
                            callback: function () {
                                fm.page.items.delete.send(ids)
                            }
                        })
                    }
                },
                send: function(ids) {
                    $.ajax({
                        url: 'media/delete',
                        method: 'post',
                        dataType: 'json',
                        data: {
                            ids: ids,
                            parent_id: fm.page.parent.get(),
                            mode: fm.page.mode.is.garbage() ? 'trash' : 'normal'
                        },
                        beforeSend: function () {
                            fm.page.loading.show()
                        },
                        complete: function () {
                            fm.page.loading.hide()
                        },
                        success: function (json) {
                            fm.helper.alert.success(json.message)
                            fm.page.items.get()
                        },
                        error: function (dataErrors) {
                            if (dataErrors.responseJSON?.message) {
                                fm.helper.alert.error(dataErrors.responseJSON.message)
                            }
                        },
                    })
                },
            },
            recycle: {
                process: function(element) {
                    if (element === undefined) {
                        let items = $('.fm-item.active')
                        if (items.length === 0) {
                            fm.helper.alert.error('هیچ آیتمی انتخاب نشده است')
                            return
                        }

                        let ids = []
                        items.each(function (i, item) {
                            ids.push($(item).data('id'))
                        })

                        fm.modal.question.fire({
                            text: 'آیا از بازیابی این آیتم‌ها اطمینان دارید؟',
                            callback: function () {
                                fm.page.items.recycle.send(ids)
                            }
                        })
                    } else if (element === 'all') {
                        let items = $('.fm-item')
                        if (items.length === 0) {
                            fm.helper.alert.error('هیچ آیتمی انتخاب نشده است')
                            return
                        }

                        let ids = []
                        items.each(function (i, item) {
                            ids.push($(item).data('id'))
                        })

                        fm.modal.question.fire({
                            text: 'آیا از بازیابی همه آیتم‌ها اطمینان دارید؟',
                            callback: function () {
                                fm.page.items.recycle.send(ids)
                            }
                        })
                    } else {
                        let id = $(element).data('id')

                        let ids = []
                        ids.push(id)

                        fm.modal.question.fire({
                            text: `آیا از بازیابی آیتم‌ شماره ${id} اطمینان دارید؟`,
                            callback: function () {
                                fm.page.items.recycle.send(ids)
                            }
                        })
                    }
                },
                send: function(ids) {
                    $.ajax({
                        url: 'media/restore',
                        method: 'post',
                        dataType: 'json',
                        data: {
                            ids: ids
                        },
                        beforeSend: function () {
                            fm.page.loading.show()
                        },
                        complete: function () {
                            fm.page.loading.hide()
                        },
                        success: function (json) {
                            fm.helper.alert.success(json.message)
                            fm.page.items.get()
                        },
                        error: function (dataErrors) {
                            if (dataErrors.responseJSON?.message) {
                                fm.helper.alert.error(dataErrors.responseJSON.message)
                            }
                        },
                    })
                },
            }
        },
        folder: {
            new: {
                save: function () {
                    if (fm.page.mode.is.garbage()) {
                        fm.helper.alert.error('پوشه در سطل زباله ایجاد نمی شود')
                        return
                    }

                    let name = $('#fm-text-new-folder').val()

                    if (name.length === 0) {
                        fm.helper.alert.error('نام پوشه را وارد کنید')
                        $('#fm-text-new-folder').focus()
                        return
                    }

                    $.ajax({
                        url: 'media/new-folder',
                        method: 'post',
                        dataType: 'json',
                        data: {
                            name: name,
                            parent_id: fm.page.parent.get()
                        },
                        beforeSend: function () {
                            $('.fm-error-new-folder').text('')
                            $('#fm-btn-save-new-folder').text('در حال ایجاد ...').attr('disabled', true)
                        },
                        complete: function () {
                            $('#fm-btn-save-new-folder').text('ایجاد').attr('disabled', false)
                        },
                        success: function (json) {
                            fm.helper.alert.success(json.message)
                            $('#modal-file-manager-new-folder').modal('hide')
                            fm.page.items.get()
                        },
                        error: function (dataErrors) {
                            if (dataErrors.responseJSON?.message) {
                                fm.helper.alert.error(dataErrors.responseJSON.message)
                            }

                            if (dataErrors.responseJSON?.errors?.name) {
                                $('#fm-text-new-folder').next('div.fm-error-new-folder').text(dataErrors.responseJSON?.errors?.name.join('<br>'))
                            }
                        },
                    })
                }
            },
            open: function (element) {
                let folder_id = $(element).data('id')
                let folder_name = $(element).data('name')
                let current_folder_id = fm.page.parent.get()

                fm.page.parent.change(folder_id)

                let status_open = fm.page.items.get()

                if (status_open) {
                    fm.page.breadcrumb.push(folder_id, folder_name)
                } else {
                    fm.page.parent.change(current_folder_id)
                }
            }
        },
        details: {
            show: function(){
                let flag = false
                $('.fm-item').each(function () {
                    if ($(this).hasClass('selector')) {
                        flag = true
                        return false
                    }
                })

                if (!flag) {
                    $('.fm-item:first').addClass('selector')
                }

                $('#fm-btn-details').removeClass('btn-active-light-info btn-light-info').addClass('btn-info')
                $('#fm-box-details').fadeIn()
                $('#fm-box-files').removeClass('col-12').addClass('col-9')
            },
            hide: function(){
                $('#fm-btn-details').removeClass('btn-info').addClass('btn-active-light-info btn-light-info')
                $('#fm-box-details').fadeOut()
                $('#fm-box-files').removeClass('col-9').addClass('col-12')
            },
            toggle: function () {
                if ($('#fm-btn-details').hasClass('btn-light-info')) {
                    this.show()
                } else if ($('#fm-btn-details').hasClass('btn-info')) {
                    this.hide()
                }
            },
            observer: function(){
                let selector = $('.fm-item.selector')

                let type = null
                if (selector.hasClass('fm-item-folder')) {
                    type = 'folder'
                } else if (selector.hasClass('fm-item-file')) {
                    type = 'file'
                }

                let id = selector.data('id')
                let name = selector.data('name')
                let size = selector.data('size')
                let uuid = selector.data('uuid')

                let created_at = selector.data('created-at')
                let updated_at = selector.data('updated-at')
                let deleted_at = selector.data('deleted-at')

                const dateOptions = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    timeZoneName: 'short'
                }

                const date_created_at = new Date(created_at)
                const local_date_created_at = date_created_at.toLocaleString(undefined, dateOptions)

                const date_updated_at = new Date(updated_at)
                const local_date_updated_at = date_updated_at.toLocaleString(undefined, dateOptions)

                let local_date_deleted_at = null
                if (deleted_at !== null) {
                    const date_deleted_at = new Date(deleted_at)
                    local_date_deleted_at = date_deleted_at.toLocaleString(undefined, dateOptions)
                }

                let theme = ''

                if (type === 'folder') {
                    let children_count = selector.data('children-count')

                    theme = `<div class="d-flex justify-content-center mb-5 mt-20">
                            <i class="fa fa-folder fs-15rem text-warning"></i>
                        </div>
                        <div class="text-center mt-10"></div>
                        <div class="separator separator-dashed border-2 my-5"></div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center px-3 py-5">
                                    <div class="fs-4 fw-bold text-gray-700">مشخصات</div>
                                </div>
                                <table class="table table-hover">
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">نام:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${name}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">نوع:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">folder</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">سایز:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${size}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">تعداد زیر مجموعه:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${children_count}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">شناسه:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${uuid}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">تاریخ افزودن:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${local_date_created_at}</td>
                                    </tr>`

                    if (created_at !== updated_at) {
                        theme += `<tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">تاریخ بروزرسانی:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${local_date_updated_at}</td>
                                    </tr>`
                    }

                    if (deleted_at !== null) {
                        theme += `<tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">تاریخ حذف:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${local_date_deleted_at}</td>
                                    </tr>`
                    }

                    theme += `</table>
                            </div>
                        </div>`
                }

                if (type === 'file') {
                    let content = selector.data('content')
                    let mime = selector.data('mime')
                    let extension = selector.children('.fm-item-file-extension').data('type')
                    let src = null
                    if ($.inArray(extension, ['image', 'gif', 'svg', 'audio', 'video']) !== -1) {
                        src = selector.data('src')
                    }

                    let disk = selector.data('disk')
                    let collection = selector.data('collection')
                    let ext = selector.data('extension')

                    if ($.inArray(extension, ['audio', 'pdf', 'word', 'excel', 'powerpoint', 'archive']) !== -1) {
                        theme += `<div class="d-flex justify-content-center mb-5">${fm.icon.get(extension, 'w-200px')}</div>`
                    }

                    theme += `<div class="text-center mt-10">`
                    if ($.inArray(extension, ['image', 'gif', 'svg']) !== -1) {
                        theme += `<img src="${src}" alt="${name}">`
                    }

                    if (extension === 'audio') {
                        theme += `<audio controlslist="noplaybackrate nodownload" controls>
                                    <source src="${src}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>`
                    }

                    if (extension === 'video') {
                        theme += `<video width="90%" controlslist="noplaybackrate nodownload" controls disablePictureInPicture>
                                    <source src="${src}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>`
                    }

                    theme += `</div>
                        <div class="separator separator-dashed border-2 my-5"></div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center px-3 py-5">
                                    <div class="fs-4 fw-bold text-gray-700">مشخصات</div>
                                    <button class="btn btn-icon btn-light-facebook w-auto px-5" onclick="fm.download.get(${id})">
                                        <i class="las la-cloud-download-alt fs-1 me-3"></i>
                                        <span>دانلود</span>
                                    </button>
                                </div>
                                <table class="table table-hover">
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">نام:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${name}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">نوع:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${extension}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">جنس فایل:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${mime}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">سایز:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${size}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">کلید محتوا:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${content}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">شناسه:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${uuid}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">دیسک:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${disk}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">کالکشن:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${collection}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">پسوند فایل:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${ext}</td>
                                    </tr>
                                    <tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">تاریخ افزودن:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${local_date_created_at}</td>
                                    </tr>`

                    if (created_at !== updated_at) {
                        theme += `<tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">تاریخ بروزرسانی:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${local_date_updated_at}</td>
                                    </tr>`
                    }

                    if (deleted_at !== null) {
                        theme += `<tr class="text-gray-600">
                                        <td class="ps-3 rounded rounded-4 rounded-end-0">تاریخ حذف:</td>
                                        <td class="pe-3 rounded rounded-4 rounded-start-0 text-end">${local_date_deleted_at}</td>
                                    </tr>`
                    }

                    theme += `</table>
                            </div>
                        </div>`
                }

                $('#fm-details-content').html(theme)
            }
        },
        breadcrumb: {
            data: [],
            push: function (id, name) {
                this.data.push({
                    id: id,
                    name: name
                })

                this.render()
                return this.data
            },
            pop: function () {
                let item = this.data.pop()
                this.render()
                return item
            },
            last: function () {
                return this.data[this.data.length - 1]
            },
            isLast: function (obj) {
                return this.last() === obj
            },
            backTo: function (id) {
                if (id === '') {
                    this.data = []
                    this.render()
                    return true
                } else {
                    let index = this.data.findIndex(x => x.id === id)
                    if (index !== -1) {
                        this.data = this.data.slice(0, index + 1)
                        this.render()
                        return true
                    }
                    return false
                }
            },
            render: function () {
                let theme = `<li class="breadcrumb-item">
                                <a href="javascript:void(0)" onclick="fm.page.breadcrumb.move('')">
                                    <i class="fa fa-home"></i>
                                </a>
                            </li>`

                $.each(this.data, function (i, item) {
                    if (fm.page.breadcrumb.isLast(item)) {
                        theme += `<li class="breadcrumb-item text-gray-600">
                                <span>${item.name}</span>
                            </li>`
                    } else {
                        theme += `<li class="breadcrumb-item">
                                <a href="javascript:void(0)" onclick="fm.page.breadcrumb.move(${item.id})">
                                    <span>${item.name}</span>
                                </a>
                            </li>`
                    }
                })

                $('#fm-breadcrumb').html(theme)
            },
            move: function(folder_id){
                fm.page.parent.change(folder_id)
                let status_open = fm.page.items.get()
                if (status_open) {
                    this.backTo(folder_id)
                }
            }
        },
        loading: {
            show: function () {
                $('#fm-loading').addClass('active')
            },
            hide: function () {
                $('#fm-loading').removeClass('active')
            },
        }
    },
    selector: {
        config: {
            ui_type: '',
            collection: '',
            mime_type: [],
            multiple: false,
            hash: '',
        },
        open: function(ui_type, collection, mime_type = '', multiple = false, hash = '') {
            this.config.ui_type = ui_type
            this.config.collection = collection
            this.config.mime_type = mime_type.split(',')
            this.config.multiple = multiple
            this.config.hash = hash

            $('#modal-file-manager').modal('show')
        },
        pick: function (element) {
            if (element === undefined || element === 'enter') {
                // btn pick or keydown enter
                if (this.config.multiple) {
                    let items = $('.fm-item-file.active')
                    if (items.length === 0) {
                        fm.helper.alert.error('هیچ آیتمی انتخاب نشده است')
                        return
                    }

                    let objects = []
                    let type_error = ''
                    items.each(function (i, item) {
                        let mime_group = fm.upload.mimeType.getGroup($(item).data('mime'))

                        if ($.inArray(mime_group, fm.selector.config.mime_type) === -1) {
                            type_error = 'mime'
                            return false
                        }

                        let image_url = ''
                        if (mime_group === 'image') {
                            image_url = 'media/image/responsive?uuid=' + $(item).data('uuid') + '&w=400&h=400&m=cover'
                        } else if (mime_group === 'svg') {
                            image_url = $(item).data('src')
                        } else {
                            const base64SVG = btoa(fm.icon.get(mime_group))
                            image_url = `data:image/svg+xml;base64,${base64SVG}`
                        }

                        objects.push({
                            id: $(item).data('id'),
                            name: $(item).data('name'),
                            url: image_url,
                        })
                    })

                    if(type_error === 'mime') {
                        fm.helper.alert.error('نوع فایل انتخاب شده برای انتخاب مجاز نیست')
                        return
                    }

                    this.send(objects)
                } else {
                    let item = $('.fm-item-file.selector')
                    if (item.length === 0) {
                        fm.helper.alert.error('هیچ آیتمی انتخاب نشده است')
                        return
                    }

                    if (fm.selector.config.ui_type === 'multiple') {
                        let current_ids = []
                        $('.fm-selector-multiple[data-collection="' + fm.selector.config.collection + '"] .fm-multiple-item input').each(function(i, element){
                            current_ids.push({
                                id: parseInt($(element).val()),
                                hash: $(element).closest('.fm-multiple-item').data('hash')
                            })
                        })

                        let flag = false
                        $.each(current_ids, function(i, current_id){
                            if (current_id.hash !== fm.selector.config.hash) {
                                if (current_id.id === item.data('id')) {
                                    flag = true
                                    return false
                                }
                            }
                        })

                        if (flag) {
                            fm.helper.alert.error('این آیتم قبلا انتخاب شده است')
                            return
                        }
                    }

                    this.send([{
                        id: item.data('id'),
                        name: item.data('name'),
                        url: 'media/image/responsive?uuid=' + item.data('uuid') + '&w=400&h=400&m=cover',
                    }])
                }
            } else {
                // dblclick
                let item = $('.fm-item-file.selector')
                if (item.length === 0) {
                    fm.helper.alert.error('هیچ آیتمی انتخاب نشده است')
                    return
                }

                if (fm.selector.config.ui_type === 'multiple') {
                    let current_ids = []
                    $('.fm-selector-multiple[data-collection="' + fm.selector.config.collection + '"] .fm-multiple-item input').each(function(i, element){
                        current_ids.push({
                            id: parseInt($(element).val()),
                            hash: $(element).closest('.fm-multiple-item').data('hash')
                        })
                    })

                    let flag = false
                    $.each(current_ids, function(i, current_id){
                        if (current_id.hash !== fm.selector.config.hash) {
                            if (current_id.id === item.data('id')) {
                                flag = true
                                return false
                            }
                        }
                    })

                    if (flag) {
                        fm.helper.alert.error('این آیتم قبلا انتخاب شده است')
                        return
                    }
                }

                this.send([{
                    id: item.data('id'),
                    name: item.data('name'),
                    url: 'media/image/responsive?uuid=' + item.data('uuid') + '&w=400&h=400&m=cover',
                }])
            }
        },
        send: function (objects) {
            if (this.config.ui_type === 'single') {
                $('.fm-selector-single input[name="media[' + this.config.collection + ']"]').val(objects[0].id)
                $('.fm-selector-single[data-collection="' + this.config.collection + '"] .fm-single-empty-selector').addClass('d-none')
                $('.fm-selector-single[data-collection="' + this.config.collection + '"] .fm-single-selected').removeClass('d-none')
                $('.fm-selector-single[data-collection="' + this.config.collection + '"] img').attr('src', objects[0].url)
            }

            if (this.config.ui_type === 'multiple') {
                let current_data = []
                $('.fm-selector-multiple[data-collection="' + this.config.collection + '"] .fm-multiple-item input').each(function(i, element){
                    current_data.push(parseInt($(element).val()))
                })

                if (this.config.multiple) {
                    $.each(objects, function(i, object){
                        if ($.inArray(object.id, current_data) === -1) {
                            $('.fm-selector-multiple[data-collection="' + fm.selector.config.collection + '"] .fm-multiple-items')
                                .append(`<div class="col-12 fm-multiple-item" data-hash="${fm.helper.randomString(10)}">
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <input type="hidden" name="media[${fm.selector.config.collection}][]" value="${object.id}">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-grip-vertical pe-3 fm-handle-drag"></i>
                                                    <div class="w-70px h-70px border border-3 border-body rounded-3 bg-light-dark d-flex align-items-center justify-content-center position-relative">
                                                        <img src="${object.url}" alt="1" class="w-100 h-100">
                                                    </div>
                                                </div>
                                                <div class="image-input-button">
                                                    <button class="fm-multiple-btn-edit btn btn-icon btn-circle btn-light-primary w-25px h-25px bg-body shadow shadow-sm me-2">
                                                        <i class="ki-duotone ki-pencil fs-7">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </button>
                                                    <button class="fm-multiple-btn-remove btn btn-icon btn-circle btn-light-google w-25px h-25px bg-body shadow shadow-sm">
                                                        <i class="fa fa-trash fs-8"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>`)
                        }
                    })
                } else {
                    $('.fm-selector-multiple[data-collection="' + this.config.collection + '"] .fm-multiple-item[data-hash="' + this.config.hash + '"] input').val(objects[0].id)
                    $('.fm-selector-multiple[data-collection="' + this.config.collection + '"] .fm-multiple-item[data-hash="' + this.config.hash + '"] img').attr('src', objects[0].url)
                }
            }

            $('#modal-file-manager').modal('hide')
        }
    },
    upload: {
        isDragging: false,
        files: {
            data: [],
            push: function (file) {
                if (this.exist(file)) {
                    fm.helper.alert.error(this.replaceMessage('فایل {name} تکراری است', file))
                } else {
                    if (fm.upload.mimeType.check(file.type)) {
                        this.data.push({
                            file: file,
                            status: 'waiting',
                            hash: fm.helper.randomString()
                        })

                        return true
                    } else {
                        fm.helper.alert.error(this.replaceMessage('فایل {name} نوع مجاز نیست', file))
                    }
                }

                return false
            },
            find: function (hash) {
                let index = this.data.findIndex(x => x.hash === hash)
                if (index !== -1) {
                    return this.data[index]
                }

                return undefined
            },
            remove: function (hash) {
                let index = this.data.findIndex(x => x.hash === hash)
                if (index !== -1) {
                    this.data.splice(index, 1)
                }
            },
            exist: function (file) {
                let flag = false
                $.each(this.data, function (i, item) {
                    if (fm.upload.files.compare(item.file, file)) {
                        flag = true
                        return false
                    }
                })

                return flag
            },
            compare: function (obj1, obj2) {
                return obj1.name === obj2.name &&
                    obj1.size === obj2.size &&
                    obj1.type === obj2.type &&
                    obj1.lastModified === obj2.lastModified
            },
            replaceMessage: function (message, file) {
                return message.replace('{name}', file.name).replace('{size}', file.size).replace('{type}', file.type)
            },
        },
        mimeType: {
            data: [],
            makeData: function () {
                $.each(localize?.fm?.mime_type, function (mime_group, item) {
                    $.each(item, function (i, mime) {
                        fm.upload.mimeType.data.push(mime)
                    })
                })
            },
            check: function (mime) {
                this.makeData()

                return this.data.includes(mime)
            },
            getGroup: function (mime) {
                let foundGroup = undefined
                $.each(localize?.fm?.mime_type, function (mime_group, item) {
                    if ($.inArray(mime, item) !== -1) {
                        foundGroup = mime_group
                        return false
                    }
                })

                return foundGroup
            }
        },
        select: function () {
            $('#fm-uploader-files').trigger('click')
        },
        uploader: {
            show: function () {
                $('#fm-uploader').removeClass('d-none')
                fm.upload.isDragging = true
            },
            hide: function () {
                $('#fm-uploader').addClass('d-none')
                fm.upload.isDragging = false
            },
        },
        uploadBox: {
            show: function () {
                $('#fm-btn-upload-box-toggle').removeClass('btn-active-light-info btn-light-info').addClass('btn-info')
                $('#fm-upload-box').slideDown(650)
            },
            hide: function () {
                $('#fm-btn-upload-box-toggle').removeClass('btn-info').addClass('btn-active-light-info btn-light-info')
                $('#fm-upload-box').slideUp(350)
            },
            toggle: function(){
                if ($('#fm-btn-upload-box-toggle').hasClass('btn-light-info')) {
                    fm.upload.uploadBox.show()
                } else if ($('#fm-btn-upload-box-toggle').hasClass('btn-info')) {
                    fm.upload.uploadBox.hide()
                }
            },
            element: {
                getImageOrIcon: function (file) {
                    return new Promise((resolve, reject) => {
                        if (file && file.type.startsWith('image/')) {
                            const reader = new FileReader()

                            reader.onload = function (e) {
                                resolve(e.target.result)
                            }

                            reader.onerror = function () {
                                reject("محتوای فایل در دسترس نیست")
                            }

                            reader.readAsDataURL(file)
                        } else {
                            let mimeGroup = fm.upload.mimeType.getGroup(file.type)

                            if (mimeGroup === undefined || !mimeGroup) {
                                reject("نوع فایل مجاز نیست")
                            }

                            const base64SVG = btoa(fm.icon.get(mimeGroup))
                            resolve(`data:image/svg+xml;base64,${base64SVG}`)
                        }
                    })
                },
                add: async function (fileData) {
                    try {
                        let content = await this.getImageOrIcon(fileData.file)

                        let element = $('<div>').addClass('w-100 mb-5 fm-upload-box-item').attr('data-hash', fileData.hash)
                            .html(`<div class="fm-upload-box-data w-100 bg-gray-100 rounded rounded-3 border border-2 border-dashed border-gray-300 d-flex justify-content-between align-items-center position-relative">
                                <img class="image w-75px h-75px rounded rounded-3" src="${content}" alt="${fileData.file.name}">
                                <span class="px-3">${fileData.file.name}</span>
                            </div>
                            <div class="d-flex-center mt-2 upload-box-item-progress">
                                <div class="fs-7 fw-bold upload-box-item-progress-percent">0%</div>
                                <div class="h-10px mx-3 w-100 bg-gray-200 rounded">
                                    <div class="bg-gray-500 rounded h-10px" role="progressbar" style="width: 0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <button type="button" class="btn btn-icon w-30px h-30px upload-box-item-cancel" onclick="fm.upload.uploadBox.progressBar.cancel(this)">
                                    <i class="fa fa-close fs-3"></i>
                                </button>
                            </div>`)

                        $('#fm-upload-box-items').prepend(element)

                        return element
                    } catch (error) {
                        fm.helper.alert.error(error)
                    }
                },
                remove: function (hash) {
                    $('.fm-upload-box-item[data-hash="' + hash + '"]').remove()
                }
            },
            progressBar: {
                remove: function (element) {
                    $(element).children('.upload-box-item-progress').remove()
                },
                change: function (element, percent) {
                    $(element).find('.upload-box-item-progress-percent').text(percent + '%')
                    $(element).find('div[role="progressbar"]').css('width', percent + '%').attr('aria-valuenow', percent)
                },
                cancel: function (cancelElement) {
                    let hash = $(cancelElement).closest('.fm-upload-box-item').data('hash')

                    fm.upload.uploadManager.request.cancel(hash)
                    fm.upload.uploadBox.element.remove(hash)
                    fm.upload.files.remove(hash)
                }
            },
            setStatus: {
                success: function (element) {
                    fm.upload.uploadBox.progressBar.remove(element)
                    $(element).find('.fm-upload-box-data').append(`<i class="fa fa-check text-primary"></i>`)
                },
                error: function (element) {
                    fm.upload.uploadBox.progressBar.remove(element)
                    $(element).find('.fm-upload-box-data').append(`<i class="fa fa-warning text-warning"></i>`)
                }
            }
        },
        push(files) {
            let flag = false
            $.each(files, function (i, file) {
                if (fm.upload.files.push(file)) {
                    flag = true
                }
            })

            return flag
        },
        dragFiles: function (files) {
            if (this.push(files)) {
                this.uploadBox.show()
                this.uploadManager.upload()
            }
        },
        changed: function () {
            let current_files = $('#fm-uploader-files')[0].files

            if (this.push(current_files)) {
                this.uploadBox.show()
                this.uploadManager.upload()
            }
        },
        uploadManager: {
            request: {
                data: [],
                push: function (fileData, element, xhr) {
                    this.data.push({
                        fileData: fileData,
                        element: element,
                        xhr: xhr
                    })
                },
                find: function (hash) {
                    let index = this.data.findIndex(x => x.fileData.hash === hash)
                    if (index !== -1) {
                        return this.data[index]
                    }

                    return undefined
                },
                remove: function (hash) {
                    let index = this.data.findIndex(x => x.fileData.hash === hash)
                    if (index !== -1) {
                        this.data.splice(index, 1)
                    }
                },
                cancel: function (hash) {
                    let request = this.find(hash)

                    if (request !== undefined) {
                        request.xhr.abort()

                        this.remove(hash)
                    }
                }
            },
            upload: function () {
                $.each(fm.upload.files.data, async function (i, fileData) {
                    if (fileData.status === 'waiting') {
                        let element = await fm.upload.uploadBox.element.add(fileData)

                        let formData = new FormData()
                        formData.append('file', fileData.file)
                        formData.append('parent_id', fm.page.parent.get())

                        let xhr = new XMLHttpRequest()
                        xhr.open('POST', 'media/upload', true)

                        fm.upload.uploadManager.request.push(fileData, element, xhr)

                        // progress bar
                        xhr.upload.onprogress = function (e) {
                            if (e.lengthComputable) {
                                let percentComplete = Math.floor((e.loaded / e.total) * 100)
                                fm.upload.uploadBox.progressBar.change(element, percentComplete)
                            }
                        }

                        // complete upload
                        xhr.onload = function () {
                            let response = JSON.parse(xhr.responseText)

                            if (xhr.status === 201) {
                                fileData.status = 'completed'
                                fm.upload.uploadBox.setStatus.success(element)
                                fm.page.items.get()
                            } else {
                                fileData.status = 'error'
                                fm.upload.uploadBox.setStatus.error(element)
                                fm.helper.alert.error(response.message)
                            }
                        }

                        xhr.send(formData)
                        fileData.status = 'uploading'
                    }
                })
            },
        }
    },
    download: {
        get: function (id) {
            const a = document.createElement('a')
            a.href = 'media/download/' + id
            a.download = ''
            document.body.appendChild(a)
            a.click()
            document.body.removeChild(a)
        },
    },
    help: {
        show: function () {
            $('#modal-file-manager-help').modal('show')
            setTimeout(function () {
                $('#modal-file-manager-help').focus()
            }, 500)
        },
    },
    modal: {
        file_manager: {
            init: function () {
                $('#modal-file-manager').on('show.bs.modal', function () {
                    fm.page.parent.change('')
                    $('#fm-search').val('')
                    $('#fm-switch-garbage').prop('checked', false)
                    fm.page.breadcrumb.data = []
                    fm.page.breadcrumb.render()
                    fm.page.items.get()
                }).on('shown.bs.modal', function () {
                    fm.modal.file_manager.shortcut.add()
                }).on('hide.bs.modal', function (e) {
                    if ($('#fm-search').is(':focus')) {
                        e.preventDefault()
                        $('#modal-file-manager').focus()
                    }
                }).on('hidden.bs.modal', function () {
                    fm.modal.file_manager.shortcut.remove()
                })
            },
            shortcut: {
                add: function () {
                    $(document)
                        // keydown in file manager
                        .on('keydown.modal-file-manager-keydown', function (e) {
                            // shift + n
                            if (e.shiftKey && e.which === 78) {
                                e.preventDefault()
                                if (fm.page.mode.is.normal()) {
                                    fm.modal.new_folder.show()
                                }
                            }
                            // shift + h
                            if (e.shiftKey && e.which === 72) {
                                e.preventDefault()
                                fm.help.show()
                            }
                            // shift + u or insert = upload
                            if ((e.shiftKey && e.which === 85) || e.which === 45) {
                                e.preventDefault()
                                if (fm.page.mode.is.normal()) {
                                    fm.upload.select()
                                }
                            }
                            // shift + d = details
                            if (e.shiftKey && e.which === 68) {
                                e.preventDefault()
                                $('#fm-btn-details').trigger('click')
                            }
                            // shift + q = upload box
                            if (e.shiftKey && e.which === 81) {
                                e.preventDefault()
                                if (fm.page.mode.is.normal()) {
                                    $('#fm-btn-upload-box-toggle').trigger('click')
                                }
                            }
                            // if search is focused
                            if ($(e.target).is('#fm-search')) {
                                return
                            }
                            // f2 = open rename
                            if (e.which === 113) {
                                e.preventDefault()
                                if (fm.page.mode.is.normal()) {
                                    let selector = $('.fm-item.selector')
                                    if (selector.length > 0) {
                                        fm.page.items.rename.show(selector)
                                    }
                                }
                            }
                            // backspace
                            if (e.which === 8) {
                                e.preventDefault()
                                if (fm.page.mode.is.normal()) {
                                    fm.page.actions.back()
                                }
                            }
                            // delete
                            if (e.which === 46) {
                                e.preventDefault()
                                fm.page.items.delete.process()
                            }
                            // shift + e = refresh
                            if (e.shiftKey && e.which === 69) {
                                e.preventDefault()
                                fm.page.actions.refresh()
                            }
                            // shift + s = focus search
                            if (e.shiftKey && e.which === 83) {
                                e.preventDefault()
                                $('#fm-search').focus()
                            }
                            // up
                            if (e.which === 38) {
                                e.preventDefault()
                                if (e.ctrlKey) {
                                    e.preventDefault()
                                    fm.page.items.arrow.move('up', true)
                                } else {
                                    fm.page.items.arrow.move('up', false)
                                }
                            }
                            // down
                            if (e.which === 40) {
                                e.preventDefault()
                                if (e.ctrlKey) {
                                    e.preventDefault()
                                    fm.page.items.arrow.move('down', true)
                                } else {
                                    fm.page.items.arrow.move('down', false)
                                }
                            }
                            // right
                            if (e.which === 39) {
                                e.preventDefault()
                                if (e.ctrlKey) {
                                    e.preventDefault()
                                    fm.page.items.arrow.move('right', true)
                                } else {
                                    fm.page.items.arrow.move('right', false)
                                }
                            }
                            // left
                            if (e.which === 37) {
                                e.preventDefault()
                                if (e.ctrlKey) {
                                    e.preventDefault()
                                    fm.page.items.arrow.move('left', true)
                                } else {
                                    fm.page.items.arrow.move('left', false)
                                }
                            }
                            // ctrl + space = multiselect
                            if (e.ctrlKey && e.which === 32) {
                                e.preventDefault()
                                fm.page.items.multiselect()
                            }
                            // enter
                            if (e.which === 13) {
                                e.preventDefault()
                                if (fm.page.mode.is.normal()) {
                                    if ($('.fm-item.selector').hasClass('fm-item-folder')) {
                                        fm.page.folder.open($('.fm-item.selector'))
                                    } else {
                                        fm.selector.pick('enter')
                                    }
                                }
                            }
                            // ctrl + a = select all
                            if (e.ctrlKey && e.which === 65) {
                                e.preventDefault()
                                $('#fm-select-all').trigger('click')
                            }
                        })

                        // toggle upload box
                        .on('click.modal-file-manager-toggle-upload-box', '#fm-btn-upload-box-toggle', fm.helper.debounce(function () {
                            if ($(this).hasClass('btn-light-info')) {
                                fm.upload.uploadBox.show()
                            } else if ($(this).hasClass('btn-info')) {
                                fm.upload.uploadBox.hide()
                            }
                        }, 250))

                        // mouseup on file manager for hide upload box
                        .on('mouseup.modal-file-manager', function (e) {
                            let box = $("#fm-upload-box")
                            let btn = $('#fm-btn-upload-box-toggle')
                            if (!(btn.is(e.target) || btn.has(e.target).length)) {
                                if (!box.is(e.target) && box.has(e.target).length === 0) {
                                    fm.upload.uploadBox.hide()
                                }
                            }
                        })

                        // upload
                        .on('dragover.modal-file-manager', '#modal-file-manager', function (e) {
                            e.preventDefault()
                            e.stopPropagation()

                            if (!fm.upload.isDragging) {
                                fm.upload.uploader.show()
                            }
                        })
                        .on('dragleave.modal-file-manager', '#modal-file-manager', function (e) {
                            e.preventDefault()
                            e.stopPropagation()

                            if (e.relatedTarget === null || !$(e.relatedTarget).closest('#modal-file-manager').length) {
                                fm.upload.uploader.hide()
                            }
                        })
                        .on('drop.modal-file-manager', '#modal-file-manager', function (e) {
                            e.preventDefault()
                            e.stopPropagation()

                            fm.upload.uploader.hide()
                            fm.upload.dragFiles(e.originalEvent.dataTransfer.files)
                        })
                        .on('change.modal-file-manager-uploader-file', '#fm-uploader-files', function () {
                            fm.upload.changed()
                        })
                },
                remove: function () {
                    $(document)
                        .off('keydown.modal-file-manager-keydown')
                        .off('click.modal-file-manager-toggle-upload-box')
                        .off('mouseup.modal-file-manager')
                        .off('dragover.modal-file-manager')
                        .off('dragleave.modal-file-manager')
                        .off('drop.modal-file-manager')
                        .off('change.modal-file-manager-uploader-file')
                }
            }
        },
        new_folder: {
            init: function () {
                $('#modal-file-manager-new-folder').on('show.bs.modal', function () {
                    $('#fm-text-new-folder').val('')
                    fm.modal.file_manager.shortcut.remove()
                }).on('shown.bs.modal', function () {
                    fm.modal.new_folder.shortcut.add()
                }).on('hide.bs.modal', function (e) {
                }).on('hidden.bs.modal', function () {
                    $('#modal-file-manager').focus()
                    fm.modal.new_folder.shortcut.remove()
                    fm.modal.file_manager.shortcut.add()
                })
            },
            show: function () {
                $('#modal-file-manager-new-folder').modal('show')
                setTimeout(function () {
                    $('#fm-text-new-folder').focus()
                }, 500)
            },
            shortcut: {
                add: function (){
                    $(document).on('keydown.modal-new-folder-keydown', function (e) {
                        // enter = save folder
                        if (e.which === 13) {
                            e.preventDefault()
                            $('#fm-btn-save-new-folder').trigger('click')
                        }
                        // ctrl + a = select text
                        if (e.ctrlKey && e.which === 65) {
                            e.preventDefault()
                            $('#fm-text-new-folder').select()
                        }
                    })
                },
                remove: function (){
                    $(document).off('keydown.modal-new-folder-keydown')
                },
            }
        },
        rename: {
            init: function () {
                $('#modal-file-manager-rename').on('show.bs.modal', function () {
                    fm.modal.file_manager.shortcut.remove()
                }).on('shown.bs.modal', function () {
                    fm.modal.rename.shortcut.add()
                }).on('hide.bs.modal', function (e) {
                }).on('hidden.bs.modal', function () {
                    $('#modal-file-manager').focus()
                    fm.modal.rename.shortcut.remove()
                    fm.modal.file_manager.shortcut.add()
                })
            },
            shortcut: {
                add: function () {
                    $(document).on('keydown.modal-rename-keydown', function (e) {
                        // enter = save folder
                        if (e.which === 13) {
                            e.preventDefault()
                            $('#fm-btn-save-rename').trigger('click')
                        }
                        // ctrl + a = select text
                        if (e.ctrlKey && e.which === 65) {
                            e.preventDefault()
                            $('#fm-text-rename').select()
                        }
                    })
                },
                remove: function () {
                    $(document).off('keydown.modal-rename-keydown')
                },
            },
        },
        question: {
            callable: null,
            header_text: 'هشدار',
            button_ok: 'کاملا موافقم',
            init: function () {
                $('#modal-file-manager-question').on('show.bs.modal', function () {
                    fm.modal.file_manager.shortcut.remove()
                }).on('shown.bs.modal', function () {
                    fm.modal.question.shortcut.add()
                }).on('hide.bs.modal', function (e) {
                }).on('hidden.bs.modal', function () {
                    $('#modal-file-manager').focus()
                    fm.modal.question.shortcut.remove()
                    fm.modal.file_manager.shortcut.add()
                })
            },
            fire: function(object){
                if (object.text === undefined) {
                    fm.helper.alert.error('متن سوال نامعتبر است')
                } else {
                    $('#modal-file-manager-question').modal('show')
                    $('#fm-question-text').text(object.text)

                    if (object.callback !== undefined) {
                        this.callable = object.callback
                    } else {
                        this.callable = null
                    }

                    if (object.header !== undefined) {
                        $('#fm-question-header').text(object.header)
                    } else {
                        $('#fm-question-header').text(this.header_text)
                    }

                    if (object.button_ok !== undefined) {
                        $('#fm-btn-question-confirm').text(object.button_ok)
                    } else {
                        $('#fm-btn-question-confirm').text(this.button_ok)
                    }

                    setTimeout(function () {
                        $('#fm-btn-question-confirm').focus()
                    }, 500)
                }
            },
            hide: function () {
                $('#modal-file-manager-question').modal('hide')
            },
            confirm: function(){
                if (this.callable !== null) {
                    this.callable()
                }
                this.hide()
            },
            shortcut: {
                add: function () {
                    $(document).on('keydown.modal-question-keydown', function (e) {
                        // enter = save folder
                        if (e.which === 13) {
                            e.preventDefault()
                            $('#fm-btn-question-confirm').trigger('click')
                        }
                    })
                },
                remove: function () {
                    $(document).off('keydown.modal-question-keydown')
                }
            }
        },
        help: {
            init: function () {
                $('#modal-file-manager-help').on('show.bs.modal', function () {
                    fm.modal.file_manager.shortcut.remove()
                }).on('shown.bs.modal', function () {
                }).on('hide.bs.modal', function () {
                    fm.modal.file_manager.shortcut.add()
                }).on('hidden.bs.modal', function () {
                    $('#modal-file-manager').focus()
                })
            },
        },
    },
    select2: {
        view: {
            init: function () {
                $('#fm-select-view').select2({
                    minimumResultsForSearch: -1,
                    dropdownCssClass: 'select2-dropdown-view',
                    templateSelection: function (item) {
                        return $('<span class="d-flex align-items-center"><i class="' + item?.element?.getAttribute('data-class') + '"></i><span class="text-gray-600 ms-3">' + item.text + '</span></span>')
                    },
                    templateResult: function (item) {
                        return $('<span class="d-flex align-items-center"><i class="' + item?.element?.getAttribute('data-class') + '"></i><span class="text-gray-600 ms-3">' + item.text + '</span></span>')
                    }
                }).on('select2:open', function () {
                    $('.select2-container--open').css('z-index', 9999)
                }).on('select2:select', function (e) {
                    $('.fm-view-box').attr('data-view', e.params.data.id)
                })
                $('#select2-fm-select-view-container').addClass('px-0')
            },
        },
        sort: {
            init: function () {
                $('#fm-select-sort').select2({
                    minimumResultsForSearch: -1,
                    dropdownCssClass: 'select2-dropdown-view',
                    templateSelection: function (item) {
                        return $('<span class="text-gray-600">' + item.text + '</span>')
                    },
                    templateResult: function (item) {
                        return $('<span class="text-gray-600">' + item.text + '</span>')
                    }
                }).on('select2:open', function () {
                    $('.select2-container--open').css('z-index', 9999)
                }).on('select2:select', function () {
                    fm.page.items.get()
                })
                $('#select2-fm-select-sort-container').addClass('px-0')
            },
        },
        order: {
            init: function () {
                $('#fm-select-order').select2({
                    minimumResultsForSearch: -1,
                    dropdownCssClass: 'select2-dropdown-view',
                    templateSelection: function (item) {
                        return $('<span class="d-flex align-items-center"><i class="' + item?.element?.getAttribute('data-class') + '"></i><span class="text-gray-600 ms-3">' + item.text + '</span></span>')
                    },
                    templateResult: function (item) {
                        return $('<span class="d-flex align-items-center"><i class="' + item?.element?.getAttribute('data-class') + '"></i><span class="text-gray-600 ms-3">' + item.text + '</span></span>')
                    }
                }).on('select2:open', function () {
                    $('.select2-container--open').css('z-index', 9999)
                }).on('select2:select', function () {
                    fm.page.items.get()
                })
            },
        },
        limit: {
            init: function () {
                $('#fm-select-limit').select2({
                    minimumResultsForSearch: -1,
                    dropdownCssClass: 'select2-dropdown-view',
                    templateSelection: function (item) {
                        return $('<span class="text-gray-600">' + item.text + '</span>')
                    },
                    templateResult: function (item) {
                        return $('<span class="text-gray-600">' + item.text + '</span>')
                    }
                }).on('select2:open', function () {
                    $('.select2-container--open').css('z-index', 9999)
                }).on('select2:select', function () {
                    fm.page.items.get()
                })
                $('#select2-fm-select-limit-container').addClass('px-0')
            },
        },
    },
    contextmenu: {
        items: function () {
            $.contextMenu({
                selector: '.fm-items',
                build: function ($triggerElement) {
                    if (!$($triggerElement).hasClass('active')) {
                        $('.fm-item').removeClass('active')
                    }
                },
                callback: function (key) {
                    switch (key) {
                        case 'select_all':
                            $('#fm-select-all').trigger('click')
                            break
                        case 'back':
                            fm.page.actions.back()
                            break
                        case 'refresh':
                            fm.page.actions.refresh()
                            break
                        case 'new_folder':
                            fm.modal.new_folder.show()
                            break
                        case 'upload':
                            fm.upload.select()
                            break
                        case 'recycle_all':
                            fm.page.items.recycle.process('all')
                            break
                        case 'delete_all':
                            fm.page.items.delete.process('all')
                            break
                    }
                },
                items: {
                    select_all: {
                        name: 'انتخاب همه',
                        disabled: function () {
                            let flag = true
                            $('.fm-item').each(function () {
                                if (!$(this).hasClass('active')) {
                                    flag = false
                                    return false
                                }
                            })
                            return flag
                        },
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-check-square text-danger" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    sep1: '-',
                    back: {
                        name: 'برگشت',
                        visible: function () {
                            return fm.page.mode.is.normal()
                        },
                        disabled: function () {
                            if (fm.page.parent.get() === '') {
                                return true
                            }
                        },
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-level-down la-rotate-270 text-info" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    refresh: {
                        name: 'بروزرسانی',
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-refresh text-info" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    sep2: '-',
                    new_folder: {
                        name: 'پوشه جدید',
                        visible: function () {
                            return fm.page.mode.is.normal()
                        },
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-plus text-success" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    upload: {
                        name: 'آپلود فایل',
                        visible: function () {
                            return fm.page.mode.is.normal()
                        },
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-cloud-upload text-success" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    recycle_all: {
                        name: 'بازیابی همه',
                        visible: function () {
                            return !fm.page.mode.is.normal()
                        },
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-recycle text-warning" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    delete_all: {
                        name: 'حذف همه',
                        visible: function () {
                            return !fm.page.mode.is.normal()
                        },
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-trash text-danger" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                }
            })
        },
        file: function () {
            $.contextMenu({
                selector: '.fm-item-file',
                build: function ($triggerElement) {
                    if (!$($triggerElement).hasClass('active')) {
                        $('.fm-item').removeClass('active')
                    }

                    fm.page.items.item.select($triggerElement, false, true)
                },
                callback: function (key) {
                    switch (key) {
                        case 'select':
                            fm.page.items.item.select(this, true, true)
                            break
                        case 'preview':
                            fm.page.items.item.select(this, true, true)
                            fm.page.details.show()
                            break
                        case 'download':
                            fm.page.items.item.select(this, true, true)
                            fm.download.get($(this).data('id'))
                            break
                        case 'rename':
                            fm.page.items.item.select(this, true, true)
                            fm.page.items.rename.show(this)
                            break
                        case 'delete':
                            fm.page.items.delete.process(this)
                            break
                        case 'recycle':
                            fm.page.items.recycle.process(this)
                            break
                    }
                },
                items: {
                    select: {
                        name: 'انتخاب',
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-hand-pointer-o text-info" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    preview: {
                        name: 'نمایش جزئیات',
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-eye text-info" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    download: {
                        name: 'دانلود',
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-download text-info" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    sep1: '-',
                    rename: {
                        name: 'تغییر نام',
                        visible: function () {
                            return !fm.page.mode.is.garbage()
                        },
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-i-cursor text-dark" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    recycle: {
                        name: 'بازیابی',
                        visible: function () {
                            return !fm.page.mode.is.normal()
                        },
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-recycle text-warning" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    sep2: '-',
                    delete: {
                        name: 'حذف',
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-trash text-danger" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                }
            })
        },
        folder: function () {
            $.contextMenu({
                selector: '.fm-item-folder',
                build: function ($triggerElement) {
                    if (!$($triggerElement).hasClass('active')) {
                        $('.fm-item').removeClass('active')
                    }

                    fm.page.items.item.select($triggerElement, false, true)
                },
                callback: function (key) {
                    switch (key) {
                        case 'select':
                            fm.page.items.item.select(this, true, true)
                            break
                        case 'open':
                            fm.page.folder.open(this)
                            break
                        case 'rename':
                            fm.page.items.rename.show(this)
                            break
                        case 'delete':
                            fm.page.items.delete.process(this)
                            break
                        case 'recycle':
                            fm.page.items.recycle.process(this)
                            break
                    }
                },
                items: {
                    select: {
                        name: 'انتخاب',
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-hand-pointer-o text-info" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    open: {
                        name: 'باز کردن',
                        visible: function () {
                            return !fm.page.mode.is.garbage()
                        },
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-folder-open text-info" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    sep1: '-',
                    rename: {
                        name: 'تغییر نام',
                        visible: function () {
                            return !fm.page.mode.is.garbage()
                        },
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-i-cursor text-dark" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    recycle: {
                        name: 'بازیابی',
                        visible: function () {
                            return !fm.page.mode.is.normal()
                        },
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-recycle text-warning" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                    sep2: '-',
                    delete: {
                        name: 'حذف',
                        icon: function (opt, $itemElement, itemKey, item) {
                            $itemElement.html('<i class="la la-trash text-danger" aria-hidden="true"></i><span>' + item.name + '</span>')
                            return 'context-menu-icon-updated'
                        }
                    },
                }
            })
        },
    },
    icon: {
        get: function (type, className) {
            let icon = ''
            switch (type) {
                case 'video':
                    icon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 461.001 461.001" class="${className}"><path fill="#F61C0D" d="M365.257,67.393H95.744C42.866,67.393,0,110.259,0,163.137v134.728 c0,52.878,42.866,95.744,95.744,95.744h269.513c52.878,0,95.744-42.866,95.744-95.744V163.137 C461.001,110.259,418.135,67.393,365.257,67.393z M300.506,237.056l-126.06,60.123c-3.359,1.602-7.239-0.847-7.239-4.568V168.607 c0-3.774,3.982-6.22,7.348-4.514l126.06,63.881C304.363,229.873,304.298,235.248,300.506,237.056z"/></svg>`
                    break
                case 'audio':
                    icon = `<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="${className}"><path class="svg-audio-c1" d="M12.75 12.508L21.25 9.108V14.7609C20.7449 14.4375 20.1443 14.25 19.5 14.25C17.7051 14.25 16.25 15.7051 16.25 17.5C16.25 19.2949 17.7051 20.75 19.5 20.75C21.2949 20.75 22.75 19.2949 22.75 17.5C22.75 17.5 22.75 17.5 22.75 17.5L22.75 7.94625C22.75 6.80342 22.75 5.84496 22.6696 5.08131C22.6582 4.97339 22.6448 4.86609 22.63 4.76597C22.5525 4.24426 22.4156 3.75757 22.1514 3.35115C22.0193 3.14794 21.8553 2.96481 21.6511 2.80739C21.6128 2.77788 21.573 2.74927 21.5319 2.7216L21.5236 2.71608C20.8164 2.2454 20.0213 2.27906 19.2023 2.48777C18.4102 2.68961 17.4282 3.10065 16.224 3.60469L14.13 4.48115C13.5655 4.71737 13.0873 4.91751 12.712 5.1248C12.3126 5.34535 11.9686 5.60548 11.7106 5.99311C11.4527 6.38075 11.3455 6.7985 11.2963 7.25204C11.25 7.67831 11.25 8.19671 11.25 8.80858V16.7609C10.7448 16.4375 10.1443 16.25 9.5 16.25C7.70507 16.25 6.25 17.7051 6.25 19.5C6.25 21.2949 7.70507 22.75 9.5 22.75C11.2949 22.75 12.75 21.2949 12.75 19.5C12.75 19.5 12.75 19.5 12.75 19.5L12.75 12.508Z"/><path class="svg-audio-c2" opacity="0.5" d="M7.75 2C7.75 1.58579 7.41421 1.25 7 1.25C6.58579 1.25 6.25 1.58579 6.25 2V7.76091C5.74485 7.4375 5.14432 7.25 4.5 7.25C2.70507 7.25 1.25 8.70507 1.25 10.5C1.25 12.2949 2.70507 13.75 4.5 13.75C6.29493 13.75 7.75 12.2949 7.75 10.5V5.0045C8.44852 5.50913 9.27955 5.75 10 5.75C10.4142 5.75 10.75 5.41421 10.75 5C10.75 4.58579 10.4142 4.25 10 4.25C9.54565 4.25 8.9663 4.07389 8.51159 3.69837C8.0784 3.34061 7.75 2.79785 7.75 2Z"/></svg>`
                    break
                case 'pdf':
                    icon = `<svg viewBox="0 0 24 24" role="img" xmlns="http://www.w3.org/2000/svg" class="${className}"><path fill="#EB5757" d="M23.63 15.3c-.71-.745-2.166-1.17-4.224-1.17-1.1 0-2.377.106-3.761.354a19.443 19.443 0 0 1-2.307-2.661c-.532-.71-.994-1.49-1.42-2.236.817-2.484 1.207-4.507 1.207-5.962 0-1.632-.603-3.336-2.342-3.336-.532 0-1.065.32-1.349.781-.78 1.384-.425 4.4.923 7.381a60.277 60.277 0 0 1-1.703 4.507c-.568 1.349-1.207 2.733-1.917 4.01C2.834 18.53.314 20.34.03 21.758c-.106.533.071 1.03.462 1.42.142.107.639.533 1.49.533 2.59 0 5.323-4.188 6.707-6.707 1.065-.355 2.13-.71 3.194-.994a34.963 34.963 0 0 1 3.407-.745c2.732 2.448 5.145 2.839 6.352 2.839 1.49 0 2.023-.604 2.2-1.1.32-.64.106-1.349-.213-1.704zm-1.42 1.03c-.107.532-.64.887-1.384.887-.213 0-.39-.036-.604-.071-1.348-.32-2.626-.994-3.903-2.059a17.717 17.717 0 0 1 2.98-.248c.746 0 1.385.035 1.81.142.497.106 1.278.426 1.1 1.348zm-7.524-1.668a38.01 38.01 0 0 0-2.945.674 39.68 39.68 0 0 0-2.52.745 40.05 40.05 0 0 0 1.207-2.555c.426-.994.78-2.023 1.136-2.981.354.603.745 1.207 1.135 1.739a50.127 50.127 0 0 0 1.987 2.378zM10.038 1.46a.768.768 0 0 1 .674-.425c.745 0 .887.851.887 1.526 0 1.135-.355 2.874-.958 4.861-1.03-2.768-1.1-5.074-.603-5.962zM6.134 17.997c-1.81 2.981-3.549 4.826-4.613 4.826a.872.872 0 0 1-.532-.177c-.213-.213-.32-.461-.249-.745.213-1.065 2.271-2.555 5.394-3.904Z"/></svg>`
                    break
                case 'word':
                    icon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="${className}"><path fill="#41A5EE" d="M490.17,19.2H140.9c-12.05,0-21.83,9.72-21.83,21.7l0,0v96.7l202.42,59.2L512,137.6V40.9 C512,28.91,502.23,19.2,490.17,19.2L490.17,19.2z"/><path fill="#2B7CD3" d="M512,137.6H119.07V256l202.42,35.52L512,256V137.6z"/><path fill="#185ABD" d="M119.07,256v118.4l190.51,23.68L512,374.4V256H119.07z"/><path fill="#103F91" d="M140.9,492.8h349.28c12.05,0,21.83-9.72,21.83-21.7l0,0v-96.7H119.07v96.7 C119.07,483.09,128.84,492.8,140.9,492.8L140.9,492.8z"/><path opacity="0.1" d="M263.94,113.92H119.07v296h144.87c12.04-0.04,21.79-9.73,21.83-21.7v-252.6 C285.73,123.65,275.98,113.96,263.94,113.92z"/><g opacity="0.2"><path d="M252.04,125.76H119.07v296h132.97c12.04-0.04,21.79-9.73,21.83-21.7v-252.6 C273.82,135.49,264.07,125.8,252.04,125.76z"/><path d="M252.04,125.76H119.07v272.32h132.97c12.04-0.04,21.79-9.73,21.83-21.7V147.46 C273.82,135.49,264.07,125.8,252.04,125.76z"/><path d="M240.13,125.76H119.07v272.32h121.06c12.04-0.04,21.79-9.73,21.83-21.7V147.46 C261.91,135.49,252.17,125.8,240.13,125.76z"/></g><path fill="url(#SVG_ID_WORD)" d="M21.83,125.76h218.3c12.05,0,21.83,9.72,21.83,21.7v217.08c0,11.99-9.77,21.7-21.83,21.7H21.83\tC9.77,386.24,0,376.52,0,364.54V147.46C0,135.48,9.77,125.76,21.83,125.76z"/><path fill="#FFFFFF" d="M89.56,292.21c0.43,3.35,0.71,6.26,0.85,8.76h0.5c0.19-2.37,0.59-5.22,1.19-8.56c0.6-3.34,1.15-6.16,1.63-8.47\tl22.96-98.49h29.68l23.81,97.01c1.38,6.03,2.37,12.15,2.96,18.3h0.39c0.44-5.97,1.27-11.9,2.48-17.76l18.99-97.6h27.02\tl-33.36,141.13H157.1l-22.62-93.47c-0.65-2.69-1.4-6.2-2.23-10.53s-1.33-7.48-1.54-9.47h-0.39c-0.26,2.3-0.77,5.71-1.54,10.23\tc-0.76,4.52-1.37,7.87-1.83,10.04l-21.27,93.17h-32.1L40.04,185.46h27.5l20.68,98.69C88.7,286.17,89.14,288.87,89.56,292.21z"/><linearGradient id="SVG_ID_WORD" gradientUnits="userSpaceOnUse" x1="45.8183" y1="-1083.4916" x2="216.1361" y2="-788.5082" gradientTransform="matrix(1 0 0 1 0 1192)"><stop offset="0" style="stop-color:#2368C4"/><stop offset="0.5" style="stop-color:#1A5DBE"/><stop offset="1" style="stop-color:#1146AC"/></linearGradient></svg>`
                    break
                case 'powerpoint':
                    icon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="${className}"><path fill="#ED6C47" d="M309.58,279.81L273.86,17.86h-2.67C141.3,18.24,36.1,123.44,35.72,253.33V256L309.58,279.81z"/><path fill="#FF8F6B" d="M276.53,17.86h-2.67V256l119.07,47.63L512,256v-2.67C511.62,123.44,406.42,18.24,276.53,17.86z"/><path fill="#D35230" d="M512,256v2.62c-0.37,129.92-105.6,235.15-235.52,235.52h-5.24c-129.92-0.37-235.15-105.6-235.52-235.52V256H512 z"/><path opacity="0.1" d="M285.77,134.91V389c-0.06,8.83-5.41,16.76-13.57,20.12c-2.6,1.1-5.39,1.67-8.22,1.67H91.92 c-3.33-3.81-6.55-7.86-9.53-11.91c-30.34-40.47-46.71-89.69-46.68-140.26v-5.24c-0.07-45.62,13.26-90.25,38.34-128.36 c2.62-4.05,5.36-8.1,8.33-11.91h181.58C275.97,113.21,285.68,122.91,285.77,134.91z"/><g opacity="0.2"><path d="M273.86,146.81v254.09c0,2.82-0.57,5.62-1.67,8.22c-3.36,8.16-11.29,13.51-20.12,13.57H102.76\tc-3.77-3.82-7.38-7.8-10.84-11.91c-3.33-3.81-6.55-7.86-9.53-11.91c-30.34-40.47-46.71-89.69-46.68-140.26v-5.24 c-0.07-45.62,13.26-90.25,38.34-128.36h178.01C264.07,125.11,273.77,134.82,273.86,146.81z"/><path d="M273.86,146.81v230.28c-0.09,12-9.79,21.7-21.79,21.79H82.4c-30.34-40.47-46.71-89.69-46.68-140.26v-5.24 c-0.07-45.62,13.26-90.25,38.34-128.36h178.01C264.07,125.11,273.77,134.82,273.86,146.81z"/><path d="M261.95,146.81v230.28c-0.09,12-9.79,21.7-21.79,21.79H82.4c-30.34-40.47-46.71-89.69-46.68-140.26v-5.24 c-0.07-45.62,13.26-90.25,38.34-128.36h166.1C252.16,125.11,261.86,134.82,261.95,146.81z"/></g><path fill="url(#SVG_ID_POWERPOINT)" d="M21.83,125.02h218.3c12.05,0,21.83,9.77,21.83,21.83v218.3c0,12.05-9.77,21.83-21.83,21.83H21.83 C9.77,386.98,0,377.2,0,365.15v-218.3C0,134.8,9.77,125.02,21.83,125.02z"/><path fill="#FFFFFF" d="M133.36,183.24c14.21-0.96,28.3,3.17,39.75,11.65c9.55,8.52,14.65,20.96,13.84,33.73 c0.16,8.88-2.21,17.62-6.82,25.21c-4.67,7.46-11.4,13.4-19.37,17.12c-9.12,4.24-19.08,6.33-29.14,6.12H104v51.32H75.72V183.24\tH133.36z M103.97,254.89h24.34c7.72,0.57,15.37-1.72,21.52-6.42c5.08-4.88,7.75-11.75,7.28-18.78c0-16-9.3-23.99-27.89-23.99h-25.24\tL103.97,254.89L103.97,254.89z"/><linearGradient id="SVG_ID_POWERPOINT" gradientUnits="userSpaceOnUse" x1="45.5067" y1="-1120.0306" x2="216.4468" y2="-823.9694" gradientTransform="matrix(1 0 0 1 0 1228)"><stop offset="0" style="stop-color:#CA4C28"/><stop offset="0.5" style="stop-color:#C5401E"/><stop offset="1" style="stop-color:#B62F14"/></linearGradient></svg>`
                    break
                case 'excel':
                    icon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="${className}"><path fill="#185C37" d="M321.49,244.09l-202.42-35.72v263.94c0,12.05,9.77,21.83,21.83,21.83l0,0h349.28 c12.05,0,21.83-9.77,21.83-21.83l0,0v-97.24L321.49,244.09z"/><path fill="#21A366" d="M321.49,17.86H140.9c-12.05,0-21.83,9.77-21.83,21.83l0,0v97.24L321.49,256l107.16,35.72L512,256V136.93 L321.49,17.86z"/><path fill="#107C41" d="M119.07,136.93h202.42V256H119.07V136.93z"/><path opacity="0.1" d="M263.94,113.12H119.07v297.67h144.87c12.04-0.04,21.79-9.79,21.83-21.83V134.94 C285.73,122.9,275.98,113.16,263.94,113.12z"/><g opacity="0.2"><path d="M252.04,125.02H119.07V422.7h132.97c12.04-0.04,21.79-9.79,21.83-21.83V146.85 C273.82,134.81,264.07,125.06,252.04,125.02z"/><path d="M252.04,125.02H119.07v273.86h132.97c12.04-0.04,21.79-9.79,21.83-21.83V146.85 C273.82,134.81,264.07,125.06,252.04,125.02z"/><path d="M240.13,125.02H119.07v273.86h121.06c12.04-0.04,21.79-9.79,21.83-21.83V146.85 C261.91,134.81,252.17,125.06,240.13,125.02z"/></g><path fill="url(#SVG_ID_EXCEL)" d="M21.83,125.02h218.3c12.05,0,21.83,9.77,21.83,21.83v218.3c0,12.05-9.77,21.83-21.83,21.83H21.83 C9.77,386.98,0,377.21,0,365.15v-218.3C0,134.79,9.77,125.02,21.83,125.02z"/><path fill="#FFFFFF" d="M67.6,326.94l45.91-71.14l-42.07-70.75h33.84l22.96,45.25c2.12,4.3,3.57,7.49,4.36,9.6h0.3 c1.51-3.43,3.1-6.76,4.76-9.99l24.54-44.83h31.07l-43.14,70.33l44.23,71.54H161.3l-26.52-49.66c-1.25-2.11-2.31-4.33-3.17-6.63\th-0.39c-0.78,2.25-1.81,4.41-3.07,6.43l-27.3,49.87L67.6,326.94L67.6,326.94z"/><path fill="#33C481" d="M490.17,17.86H321.49v119.07H512V39.69C512,27.63,502.23,17.86,490.17,17.86L490.17,17.86z"/><path fill="#107C41" d="M321.49,256H512v119.07H321.49V256z"/><linearGradient id="SVG_ID_EXCEL" gradientUnits="userSpaceOnUse" x1="45.5065" y1="-1464.0308" x2="216.4467" y2="-1167.9695" gradientTransform="matrix(1 0 0 1 0 1572)"><stop offset="0" style="stop-color:#18884F"/><stop offset="0.5" style="stop-color:#117E43"/><stop offset="1" style="stop-color:#0B6631"/></linearGradient></svg>`
                    break
                case 'archive':
                    icon = `<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="${className}"><g class="svg-archive-c"><g opacity="0.3"><path d="M4.66602 9C3.73413 9 3.26819 9 2.90065 8.84776C2.41059 8.64477 2.02124 8.25542 1.81826 7.76537C1.66602 7.39782 1.66602 6.93188 1.66602 6C1.66602 5.06812 1.66602 4.60217 1.81826 4.23463C2.02124 3.74458 2.41059 3.35523 2.90065 3.15224C3.26819 3 3.73413 3 4.66602 3H11.934C11.8905 3.07519 11.8518 3.15353 11.8183 3.23463C11.666 3.60217 11.666 4.06812 11.666 5L11.666 9H4.66602Z"/><path d="M21.666 6C21.666 6.93188 21.666 7.39782 21.5138 7.76537C21.3108 8.25542 20.9214 8.64477 20.4314 8.84776C20.0638 9 19.5979 9 18.666 9H17.666V5C17.666 4.06812 17.666 3.60217 17.5138 3.23463C17.4802 3.15353 17.4415 3.07519 17.3981 3H18.666C19.5979 3 20.0638 3 20.4314 3.15224C20.9214 3.35523 21.3108 3.74458 21.5138 4.23463C21.666 4.60217 21.666 5.06812 21.666 6Z"/></g><g opacity="0.7"><path d="M17.5138 20.7654C17.666 20.3978 17.666 19.9319 17.666 19V15H18.666C19.5979 15 20.0638 15 20.4314 15.1522C20.9214 15.3552 21.3108 15.7446 21.5138 16.2346C21.666 16.6022 21.666 17.0681 21.666 18C21.666 18.9319 21.666 19.3978 21.5138 19.7654C21.3108 20.2554 20.9214 20.6448 20.4314 20.8478C20.0638 21 19.5979 21 18.666 21H17.3981C17.4415 20.9248 17.4802 20.8465 17.5138 20.7654Z"/><path d="M11.934 21H4.66602C3.73413 21 3.26819 21 2.90065 20.8478C2.41059 20.6448 2.02124 20.2554 1.81826 19.7654C1.66602 19.3978 1.66602 18.9319 1.66602 18C1.66602 17.0681 1.66602 16.6022 1.81826 16.2346C2.02124 15.7446 2.41059 15.3552 2.90065 15.1522C3.26819 15 3.73413 15 4.66602 15H11.666V19C11.666 19.9319 11.666 20.3978 11.8183 20.7654C11.8518 20.8465 11.8905 20.9248 11.934 21Z"/></g><g opacity="0.5"><path d="M17.666 9H18.666C19.5979 9 20.0638 9 20.4314 9.15224C20.9214 9.35523 21.3108 9.74458 21.5138 10.2346C21.666 10.6022 21.666 11.0681 21.666 12C21.666 12.9319 21.666 13.3978 21.5138 13.7654C21.3108 14.2554 20.9214 14.6448 20.4314 14.8478C20.0638 15 19.5979 15 18.666 15H17.666V9Z"/><path d="M11.666 9V15H4.66602C3.73413 15 3.26819 15 2.90065 14.8478C2.41059 14.6448 2.02124 14.2554 1.81826 13.7654C1.66602 13.3978 1.66602 12.9319 1.66602 12C1.66602 11.0681 1.66602 10.6022 1.81826 10.2346C2.02124 9.74458 2.41059 9.35523 2.90065 9.15224C3.26819 9 3.73413 9 4.66602 9H11.666Z"/></g><path fill-rule="evenodd" clip-rule="evenodd" d="M17.5138 3.23463C17.666 3.60218 17.666 4.06812 17.666 5L17.666 19C17.666 19.9319 17.666 20.3978 17.5138 20.7654C17.4802 20.8465 17.4415 20.9248 17.3981 21C17.1792 21.3792 16.8403 21.6784 16.4314 21.8478C16.0638 22 15.5979 22 14.666 22C13.7341 22 13.2682 22 12.9006 21.8478C12.4917 21.6784 12.1529 21.3792 11.934 21C11.8905 20.9248 11.8518 20.8465 11.8183 20.7654C11.666 20.3978 11.666 19.9319 11.666 19V5C11.666 4.06812 11.666 3.60218 11.8183 3.23463C11.8518 3.15353 11.8905 3.07519 11.934 3C12.1529 2.62082 12.4917 2.32164 12.9006 2.15224C13.2682 2 13.7341 2 14.666 2C15.5979 2 16.0638 2 16.4314 2.15224C16.8403 2.32164 17.1792 2.62082 17.3981 3C17.4415 3.07519 17.4802 3.15353 17.5138 3.23463ZM15.416 11C15.416 10.5858 15.0802 10.25 14.666 10.25C14.2518 10.25 13.916 10.5858 13.916 11L13.916 13C13.916 13.4142 14.2518 13.75 14.666 13.75C15.0802 13.75 15.416 13.4142 15.416 13L15.416 11Z"/></g></svg>`
                    break
            }

            return icon
        }
    },
    helper: {
        randomString: function (length = 25) {
            const characters = 'abcdefghijklmnopqrstuvwxyz'
            let result = ''

            const randomIndexes = Array.from({length: length}, (_, i) => i)

            $.each(randomIndexes, function () {
                const randomIndex = Math.floor(Math.random() * characters.length)
                result += characters[randomIndex]
            })

            return result
        },
        debounce: function (func, wait) {
            let timeout
            return function (...args) {
                const later = () => {
                    clearTimeout(timeout)
                    func.apply(this, args)
                }
                clearTimeout(timeout)
                timeout = setTimeout(later, wait)
            }
        },
        alert: {
            toast: null,
            init: function () {
                toastr.options = {
                    "closeButton": false,
                    "progressBar": true,
                    "positionClass": "toastr-bottom-right",
                    "preventDuplicates": true,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }

                this.toast = toastr
            },
            success: function (message) {
                this.init()
                this.toast.success(message)
            },
            error: function (message) {
                this.init()
                this.toast.error(message)
            },
            warning: function (message) {
                this.init()
                this.toast.warning(message)
            },
            info: function (message) {
                this.init()
                this.toast.info(message)
            }
        },
    }
}

$(document).ready(function () {
    fm.init.ready()

    $('.fm-disable-selection').disableSelection()

    // begin: draggable images
    $(".fm-draggable-images").sortable({
        handle: ".fm-handle-drag",
        axis: "y",
        placeholder: "fm-sortable-placeholder",
        update: function (event, ui) {
        }
    }).disableSelection()
    // end: draggable images

    // begin: observer fm-item
    const observer = (new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                fm.page.actions.select_all.observer()
                fm.page.details.observer()
            }
        }
    }))

    observer.observe(document.querySelector('.fm-items'), {
        attributes: true,
        attributeFilter: ['class'],
        subtree: true
    })
    // end: observer fm-item

    // begin: media selector
    $(document)
        .on('click', '.fm-selector-single .fm-single-empty-selector', function () {
            let collection = $(this).closest('.fm-selector-single').data('collection')
            let mime = $(this).closest('.fm-selector-single').data('mime')

            fm.selector.open('single', collection, mime)
        })
        .on('click', '.fm-selector-single .fm-single-btn-edit', function (e) {
            e.preventDefault()
            e.stopPropagation()

            let collection = $(this).closest('.fm-selector-single').data('collection')
            let mime = $(this).closest('.fm-selector-single').data('mime')

            fm.selector.open('single', collection, mime)
        })
        .on('click', '.fm-selector-single .fm-single-btn-remove', function (e) {
            e.preventDefault()
            e.stopPropagation()

            let item_target = $(this).closest('.fm-selector-single')

            item_target.find('input').val('')
            item_target.find('.fm-single-empty-selector').removeClass('d-none')
            item_target.find('.fm-single-selected').addClass('d-none')
            item_target.find('img').attr('src', '')
        })
        // multiple selector
        .on('click', '.fm-selector-multiple .fm-multiple-add', function(){
            let collection = $(this).closest('.fm-selector-multiple').data('collection')
            let mime = $(this).closest('.fm-selector-multiple').data('mime')

            fm.selector.open('multiple', collection, mime, true)
        })
        .on('click', '.fm-selector-multiple .fm-multiple-btn-edit', function(e){
            e.preventDefault()
            e.stopPropagation()

            let collection = $(this).closest('.fm-selector-multiple').data('collection')
            let mime = $(this).closest('.fm-selector-multiple').data('mime')
            let hash = $(this).closest('.fm-multiple-item').data('hash')

            fm.selector.open('multiple', collection, mime, false, hash)
        })
        .on('click', '.fm-selector-multiple .fm-multiple-btn-remove', function(e){
            e.preventDefault()
            e.stopPropagation()

            $(this).closest('.fm-multiple-item').remove()
        })
})
