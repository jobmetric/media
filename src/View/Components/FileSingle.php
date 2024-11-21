<?php

namespace JobMetric\Media\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Throwable;

class FileSingle extends Component
{
    use FileInformation;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string      $name,
        public string      $collection,
        public string      $mimeTypes = 'image,svg',
        public string|null $value = null,
    )
    {
    }

    /**
     * Get the view / contents that represent the component.
     * @throws Throwable
     */
    public function render(): View|Closure|string
    {
        $data = $this->getFileInformation((int)$this->value);

        return view('media::components.file-single', $data);
    }

}
