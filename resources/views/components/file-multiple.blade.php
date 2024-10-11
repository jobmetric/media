<div class="accordion accordion-icon-collapse fm-selector-multiple" data-collection="{{ $collection }}" data-mime="{{ $mimeTypes }}" id="fm-selector-collection-{{ $collection }}">
    <div>
        <div class="accordion-header mt-10 d-flex justify-content-between collapsed" data-bs-toggle="collapse" data-bs-target="#fm-selector-collection-{{ $collection }}-item">
            <h3 class="fs-5 fw-bold mb-0">{{ $name }}</h3>
            <span class="accordion-icon">
                <i class="ki-duotone ki-plus-square fs-3 accordion-icon-off"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <i class="ki-duotone ki-minus-square fs-3 accordion-icon-on"><span class="path1"></span><span class="path2"></span></i>
            </span>
        </div>
        <div id="fm-selector-collection-{{ $collection }}-item" class="fs-6 collapse mt-3" data-bs-parent="#fm-selector-collection-{{ $collection }}">
            <div class="fm-multiple-items row fm-draggable-images">
                @foreach($images as $image)
                    <div class="fm-multiple-item col-12" data-hash="1">
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <input type="hidden" name="media[{{ $collection }}][]" value="{{ $image['image_value'] }}">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-grip-vertical pe-3 fm-handle-drag"></i>
                                <div class="w-70px h-70px border border-3 border-body rounded-3 bg-light-dark d-flex align-items-center justify-content-center position-relative">
                                    <img src="{{ $image['image_url'] }}" alt="{{ $image['image_name'] }}" class="w-100 h-100">
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
                    </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="fm-multiple-add mt-3 cursor-pointer">
                        <div class="h-40px text-gray-600 border border-1 border-primary border-dashed rounded-3 bg-light-primary d-flex align-items-center justify-content-center">
                            <i class="fa fa-plus fs-5 me-2"></i>
                            <span class="fw-bold">افزودن</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
