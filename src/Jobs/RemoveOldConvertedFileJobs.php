<?php

namespace JobMetric\Media\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use JobMetric\Media\Models\Media;

class RemoveOldConvertedFileJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Media $media;
    private string $old_extension;

    /**
     * Create a new job instance.
     */
    public function __construct(Media $media, string $old_extension)
    {
        $this->media = $media;
        $this->old_extension = $old_extension;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $old_file_path = $this->media->collection . '/' . substr($this->media->created_at, 0, 4) . '/' . substr($this->media->created_at, 5, 2) . '/' . $this->media->uuid . '.' . $this->old_extension;

        if (Storage::disk($this->media->disk)->exists($old_file_path)) {
            Storage::disk($this->media->disk)->delete($old_file_path);
        }
    }
}
