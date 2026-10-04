<?php

namespace App\Jobs;

use App\Models\PropertyImage;
use App\Services\Property\PropertyImageThumbnailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPropertyImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(public PropertyImage $propertyImage) {}

    public function handle(): void
    {
        $propertyImage = $this->propertyImage->fresh();

        if ($propertyImage === null) {
            return;
        }

        $thumbnails = new PropertyImageThumbnailService;

        $propertyImage->update([
            'thumbnail_path' => $thumbnails->generate($propertyImage->path),
            'optimized_path' => $thumbnails->optimize($propertyImage->path),
        ]);
    }
}
