<?php

namespace App\Services\Property;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PropertyImageThumbnailService
{
    private const THUMBNAIL_WIDTH = 400;

    private const THUMBNAIL_DIRECTORY = 'properties/thumbnails';

    private const OPTIMIZED_WIDTH = 1920;

    private const OPTIMIZED_QUALITY = 82;

    private const OPTIMIZED_DIRECTORY = 'properties/optimized';

    /**
     * Generate a card-sized thumbnail for an already-stored property image
     * and return its path relative to the public disk.
     */
    public function generate(string $originalPath): string
    {
        $manager = ImageManager::usingDriver(Driver::class);

        $image = $manager->decodePath(Storage::disk('public')->path($originalPath));
        $image->scaleDown(width: self::THUMBNAIL_WIDTH);

        $extension = pathinfo($originalPath, PATHINFO_EXTENSION) ?: 'jpg';
        $thumbnailPath = self::THUMBNAIL_DIRECTORY.'/'.Str::random(40).'.'.$extension;

        Storage::disk('public')->makeDirectory(self::THUMBNAIL_DIRECTORY);
        $image->save(Storage::disk('public')->path($thumbnailPath));

        return $thumbnailPath;
    }

    /**
     * Generate a compressed, capped-resolution variant of an already-stored
     * property image for gallery/detail display, and return its path
     * relative to the public disk. The original upload is left untouched.
     */
    public function optimize(string $originalPath): string
    {
        $manager = ImageManager::usingDriver(Driver::class);

        $image = $manager->decodePath(Storage::disk('public')->path($originalPath));
        $image->scaleDown(width: self::OPTIMIZED_WIDTH);

        $extension = pathinfo($originalPath, PATHINFO_EXTENSION) ?: 'jpg';
        $optimizedPath = self::OPTIMIZED_DIRECTORY.'/'.Str::random(40).'.'.$extension;

        Storage::disk('public')->makeDirectory(self::OPTIMIZED_DIRECTORY);
        $image->save(Storage::disk('public')->path($optimizedPath), quality: self::OPTIMIZED_QUALITY);

        return $optimizedPath;
    }
}
