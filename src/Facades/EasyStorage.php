<?php

namespace Danilowa\LaravelEasyCloudStorage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|false upload(\Illuminate\Http\UploadedFile $file, string $path, ?string $disk = null)
 * @method static \Symfony\Component\HttpFoundation\BinaryFileResponse download(string $path, ?string $disk = null)
 * @method static string url(string $path, ?string $disk = null)
 * @method static bool delete(string $path, ?string $disk = null)
 * @method static bool exists(string $path, ?string $disk = null)
 * @method static array|false getMetadata(string $path, ?string $disk = null)
 * @method static bool setMetadata(string $path, array $metadata, ?string $disk = null)
 * @method static array listFiles(string $directory, ?string $disk = null)
 * @method static bool move(string $oldPath, string $newPath, ?string $disk = null)
 * @method static string getFileType(string $path, ?string $disk = null)
 * @method static bool copy(string $sourcePath, string $destinationPath, ?string $disk = null)
 * @method static bool prepend(string $path, string $data, ?string $disk = null)
 * @method static bool append(string $path, string $data, ?string $disk = null)
 * @method static bool makeDirectory(string $path, ?string $disk = null)
 * @method static bool deleteDirectory(string $path, ?string $disk = null)
 * @method static self withLog(bool $log = true)
 * @method static self withError(bool $throw = true)
 * @method static \Danilowa\LaravelEasyCloudStorage\CustomMethod customMethod(string $method, ?string $disk = null)
 */
class EasyStorage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'easy-storage';
    }
}
