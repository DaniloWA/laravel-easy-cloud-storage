<?php

namespace Danilowa\LaravelEasyCloudStorage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|false upload(\Illuminate\Http\UploadedFile $file, string $path, ?string $newName = null)
 * @method static \Symfony\Component\HttpFoundation\BinaryFileResponse download(string $path, ?string $newName = null)
 * @method static string url(string $path)
 * @method static bool delete(string $path)
 * @method static bool exists(string $path)
 * @method static bool copy(string $from, string $to)
 * @method static bool move(string $from, string $to)
 * @method static int|false size(string $path)
 * @method static int|false lastModified(string $path)
 * @method static array|false getMetadata(string $path)
 * @method static bool setMetadata(string $path, array $metadata)
 * @method static array listFiles(string $directory)
 * @method static bool prepend(string $path, string $data)
 * @method static bool append(string $path, string $data)
 * @method static bool makeDirectory(string $path)
 * @method static bool deleteDirectory(string $path)
 * @method static self setDisk(string $disk)
 * @method static self useDisk(string $disk)
 * @method static self withLog(bool $log = true)
 * @method static self withError(bool $throw = true)
 * @method static mixed customMethod(string $method, array $parameters = [])
 */
class EasyStorage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'easy-storage';
    }
}
