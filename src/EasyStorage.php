<?php

namespace Danilowa\LaravelEasyCloudStorage;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Danilowa\LaravelEasyCloudStorage\Contracts\BaseStorage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Danilowa\LaravelEasyCloudStorage\Exceptions\StorageMethodNotSupportedException;

class EasyStorage implements BaseStorage
{
    protected string $disk;
    protected bool $logErrors;
    protected bool $throwErrors;

    /**
     * EasyStorage constructor.
     * @param string|null $disk
     */
    public function __construct(?string $disk = null)
    {
        $this->disk = $disk ?: config('easycloudstorage.default');
        $this->logErrors = false;
        $this->throwErrors = false;
    }

    /**
     * Set the disk.
     *
     * @param string $disk
     * @return self
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * Enable or disable logging of errors.
     *
     * @param bool $log
     * @return self
     */
    public function withLog(bool $log = true): self
    {
        $this->logErrors = $log;
        return $this;
    }

    /**
     * Enable or disable throwing of errors.
     *
     * @param bool $throw
     * @return self
     */
    public function withError(bool $throw = true): self
    {
        $this->throwErrors = $throw;
        return $this;
    }

    /**
     * Upload a file to the storage.
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string|null $newName
     * @return string|false
     */
    public function upload(UploadedFile $file, string $path, ?string $newName = null): string|false
    {
        $fileName = $this->getUniqueFileName($file, $path, $newName);
        return $this->executeMethod('putFileAs', [$path, $file, $fileName]);
    }

    /**
     * Get the disk instance.
     *
     * @param string|null $disk
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function disk(?string $disk = null)
    {
        return Storage::disk($disk ?: $this->disk);
    }

    /**
     * Get a unique file name for the uploaded file.
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string|null $newName
     * @return string
     */
    protected function getUniqueFileName(UploadedFile $file, string $path, ?string $newName = null): string
    {
        $fileName = $newName ?? $file->getClientOriginalName();
        $fullPath = $path . '/' . $fileName;

        $i = 1;
        while ($this->exists($fullPath)) {
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . "_{$i}." . $file->getClientOriginalExtension();
            $fullPath = $path . '/' . $fileName;
            $i++;
        }

        return $fileName;
    }

    /**
     * Download a file from the storage.
     *
     * @param string $path
     * @param string|null $newName
     * @return BinaryFileResponse
     * @throws NotFoundHttpException
     */
    public function download(string $path, ?string $newName = null): BinaryFileResponse
    {
        $storageDisk = $this->disk();
        $fullPath = $storageDisk->path($path);

        if (!$storageDisk->exists($path)) {
            throw new NotFoundHttpException("File not found at path: {$path}");
        }

        $fileName = $newName ?? basename($path);
        return response()->download($fullPath, $fileName);
    }

    /**
     * Delete a file from the storage.
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        return $this->executeMethod('delete', [$path]);
    }

    /**
     * Check if a file exists in the storage.
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return $this->executeMethod('exists', [$path]);
    }

    /**
     * Get the URL of a file in the storage.
     *
     * @param string $path
     * @return string
     */
    public function url(string $path): string
    {
        return $this->executeMethod('url', [$path]);
    }

    /**
     * Copy a file to a new location.
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function copy(string $from, string $to): bool
    {
        return $this->executeMethod('copy', [$from, $to]);
    }

    /**
     * Move a file to a new location.
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function move(string $from, string $to): bool
    {
        return $this->executeMethod('move', [$from, $to]);
    }

    /**
     * Get the size of a file in the storage.
     *
     * @param string $path
     * @return int|false
     */
    public function size(string $path): int|false
    {
        return $this->executeMethod('size', [$path]);
    }

    /**
     * Get the last modified time of a file in the storage.
     *
     * @param string $path
     * @return int|false
     */
    public function lastModified(string $path): int|false
    {
        return $this->executeMethod('lastModified', [$path]);
    }

    /**
     * Get metadata of a file in the storage.
     *
     * @param string $path
     * @param string|null $disk
     * @return array|false
     */
    public function getMetadata(string $path, ?string $disk = null): array|false
    {
        return $this->executeMethod('getMetadata', [$path]);
    }

    /**
     * Set metadata of a file in the storage.
     *
     * @param string $path
     * @param array $metadata
     * @param string|null $disk
     * @return bool
     */
    public function setMetadata(string $path, array $metadata, ?string $disk = null): bool
    {
        return $this->executeMethod('setMetadata', [$path, $metadata]);
    }

    /**
     * List files in a directory.
     *
     * @param string $directory
     * @param string|null $disk
     * @return array
     */
    public function listFiles(string $directory, ?string $disk = null): array
    {
        return $this->executeMethod('files', [$directory]);
    }

    /**
     * Prepend data to a file.
     *
     * @param string $path
     * @param string $data
     * @param string|null $disk
     * @return bool
     */
    public function prepend(string $path, string $data, ?string $disk = null): bool
    {
        return $this->executeMethod('prepend', [$path, $data]);
    }

    /**
     * Append data to a file.
     *
     * @param string $path
     * @param string $data
     * @param string|null $disk
     * @return bool
     */
    public function append(string $path, string $data, ?string $disk = null): bool
    {
        return $this->executeMethod('append', [$path, $data]);
    }

    /**
     * Create a directory.
     *
     * @param string $path
     * @param string|null $disk
     * @return bool
     */
    public function makeDirectory(string $path, ?string $disk = null): bool
    {
        return $this->executeMethod('makeDirectory', [$path]);
    }

    /**
     * Delete a directory.
     *
     * @param string $path
     * @param string|null $disk
     * @return bool
     */
    public function deleteDirectory(string $path, ?string $disk = null): bool
    {
        return $this->executeMethod('deleteDirectory', [$path]);
    }

    /**
     * Execute a custom method on the storage disk.
     *
     * @param string $method
     * @param array $parameters
     * @param string|null $disk
     * @return mixed
     */
    public function customMethod(string $method, array $parameters = [], ?string $disk = null)
    {
        return $this->executeMethod($method, $parameters, $disk);
    }

    /**
     * Execute a method on the storage disk.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     *
     * @throws StorageMethodNotSupportedException
     */
    protected function executeMethod(string $method, array $parameters)
    {
        $diskInstance = $this->disk();

        if (!method_exists($diskInstance, $method)) {
            if ($this->logErrors) {
                Log::error("EasyStorage: Method {$method} not supported on disk {$this->disk}.");
            }
            return $this->throwErrors
                ? throw new StorageMethodNotSupportedException("Method {$method} not supported on the selected storage disk.")
                : false;
        }

        try {
            return $diskInstance->{$method}(...$parameters);
        } catch (Exception $e) {
            if ($this->logErrors) {
                Log::error("EasyStorage: Error executing method {$method} on disk {$this->disk}: {$e->getMessage()}");
            }
            return $this->throwErrors ? throw $e : false;
        }
    }
}
