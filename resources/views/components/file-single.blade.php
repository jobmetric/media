<div class="accordion accordion-icon-collapse fm-selector-single" data-collection="{{ $collection }}" data-mime="{{ $mimeTypes }}" id="fm-selector-collection-base">
    <input type="hidden" name="media[{{ $collection }}]" value="{{ $image_value }}">

    <div>
        <div class="accordion-header mt-10 d-flex justify-content-between" data-bs-toggle="collapse" data-bs-target="#fm-selector-collection-base-item">
            <h3 class="fs-5 fw-bold mb-0">{{ $name }}</h3>
            <span class="accordion-icon">
                <i class="ki-duotone ki-plus-square fs-3 accordion-icon-off"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <i class="ki-duotone ki-minus-square fs-3 accordion-icon-on"><span class="path1"></span><span class="path2"></span></i>
            </span>
        </div>
        <div id="fm-selector-collection-base-item" class="fs-6 collapse show mt-9" data-bs-parent="#fm-selector-collection-base">
            <div class="image-item w-100 border border-3 border-body rounded-3 bg-light-dark d-flex align-items-center justify-content-center position-relative m-auto">
                <div class="fm-single-selected position-absolute top-0 end-0 m-5 opacity-25 opacity-100-hover z-index-3 @if(is_null($image_value)) d-none @endif">
                    <button class="fm-single-btn-edit btn btn-icon btn-circle btn-light-primary w-25px h-25px bg-body shadow shadow-sm me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('media::base.file_manager.selector.single.button.edit') }}">
                        <i class="ki-duotone ki-pencil fs-7">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </button>
                    <button class="fm-single-btn-remove btn btn-icon btn-circle btn-light-google w-25px h-25px bg-body shadow shadow-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('media::base.file_manager.selector.single.button.remove') }}">
                        <i class="fa fa-trash fs-8"></i>
                    </button>
                </div>
                <div class="border border-1 rounded w-100 h-225px position-relative bg-hover-light-secondary animate"></div>
                <img src="{{ $image_url }}" alt="{{ $image_name }}" class="fm-single-selected w-100 h-100 @if(is_null($image_value)) d-none @endif">
                <div class="fm-single-empty-selector position-absolute text-gray-800 w-100 h-100 d-flex flex-column justify-content-center cursor-pointer @if(!is_null($image_value)) d-none @endif">
                    <i class="fa fa-plus fs-5x"></i>
                    <p class="fs-5 fw-bold mt-5">{{ trans('media::base.file_manager.selector.single.select') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
