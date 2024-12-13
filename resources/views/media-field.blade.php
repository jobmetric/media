@php
    /**
     * @var \JobMetric\Media\ServiceType\Media $media
     */
    $collection = $media->getCollection();
    $name = str_replace('{collection}', $collection, $name);
@endphp
@if($media->getMultiple())
    <x-file-multiple name="{{ trans($name) }}" collection="{{ $collection }}" mime-types="{{ implode(',', $media->getMimeTypes()) }}" value="{{ $value }}" />
@else
    <x-file-single name="{{ trans($name) }}" collection="{{ $collection }}" mime-types="{{ implode(',', $media->getMimeTypes()) }}" value="{{ $value }}" />
@endif
