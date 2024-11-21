<?php

namespace JobMetric\Media\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Throwable;

class FileMultiple extends Component
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
        $data['images'] = [];

        if ($this->value) {
            $ids = explode(',', $this->value);

            foreach ($ids as $id) {
                $image = $this->getFileInformation((int)trim($id));

                if ($image['image_value']) {
                    $data['images'][] = $image;
                }
            }
        }

        return view('media::components.file-multiple', $data);
    }

}
