<?php

namespace JobMetric\Media\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Throwable;

class FileManager extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the view / contents that represent the component.
     * @throws Throwable
     */
    public function render(): View|Closure|string
    {
        DomiPlugins('jquery.ui', 'jquery.contextmenu');

        DomiStyle('assets/vendor/media/css/file-manager.css');
        DomiScript('assets/vendor/media/js/file-manager.js');

        DomiLocalize('fm', [
            'mime_type' => config('media.mime_type'),
        ]);

        DomiFooterContent(view('media::components.modal')->render(), 'media_modal');

        return view('media::components.file-manager');
    }
}
