<?php

namespace Danilowa\LaravelEasyCloudStorage;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Danilowa\LaravelEasyCloudStorage\CustomMethod;
use Danilowa\LaravelEasyCloudStorage\Contracts\BaseStorage;
use Danilowa\LaravelEasyCloudStorage\Exceptions\StorageMethodNotSupportedException;

/**
 * Class EasyStorage
 *
 * Provides methods for cloud storage manipulation based on the BaseStorage contract.
 */
class EasyStorage implements BaseStorage
{
    protected string $disk;
    protected bool $logErrors;
    protected bool $throwErrors;

    /**
     * EasyStorage constructor.
     *
     * @param string|null $disk The storage disk to use. Defaults to the configured default disk.
     */
    public function __construct(?string $disk = null)
    {
        $this->disk = $disk ?: config('easycloudstorage.default');
        $this->logErrors = config('easycloudstorage.log_errors', true);
        $this->throwErrors = config('easycloudstorage.throw_errors', true);
    }

    /**
     * Get the storage disk instance.
     *
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function disk(?string $disk = null)
    {
        return Storage::disk($disk ?: $this->disk);
    }

    /**
     * Upload a file to the specified path on the storage disk.
     *
     * @param UploadedFile $file The file to upload.
     * @param string $path The path to upload the file to.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return string|false The file path if successful, false otherwise.
     */
    public function upload(UploadedFile $file, string $path, ?string $disk = null): string|false
    {
        return $this->executeMethod('putFileAs', [$path, $file, $file->getClientOriginalName()], $disk);
    }

    /**
     * Download a file from the specified path on the storage disk.
     *
     * @param string $path The path of the file to download.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(string $path, ?string $disk = null)
    {
        $fullPath = $this->disk($disk)->path($path);
        return response()->download($fullPath);
    }

    /**
     * Get the URL of a file in the storage disk.
     *
     * @param string $path The path of the file.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return string The URL of the file.
     */
    public function url(string $path, ?string $disk = null): string
    {
        return $this->executeMethod('url', [$path], $disk);
    }

    /**
     * Delete a file from the storage disk.
     *
     * @param string $path The path of the file to delete.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return bool True if deleted, false otherwise.
     */
    public function delete(string $path, ?string $disk = null): bool
    {
        return $this->executeMethod('delete', [$path], $disk);
    }

    /**
     * Check if a file exists on the storage disk.
     *
     * @param string $path The path of the file.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return bool True if the file exists, false otherwise.
     */
    public function exists(string $path, ?string $disk = null): bool
    {
        return $this->executeMethod('exists', [$path], $disk);
    }

    /**
     * Get metadata for a file.
     *
     * @param string $path The path of the file.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return array|false An array of metadata if successful, false otherwise.
     */
    public function getMetadata(string $path, ?string $disk = null): array|false
    {
        return $this->executeMethod('getMetadata', [$path], $disk);
    }

    /**
     * Set metadata for a file (currently a placeholder).
     *
     * @param string $path The path of the file.
     * @param array $metadata The metadata to set.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return bool Always returns false as it's not implemented.
     */
    public function setMetadata(string $path, array $metadata, ?string $disk = null): bool
    {
        return false; // Placeholder for metadata setting
    }

    /**
     * List files in a directory.
     *
     * @param string $directory The directory to list files from.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return array An array of file paths.
     */
    public function listFiles(string $directory, ?string $disk = null): array
    {
        return $this->executeMethod('files', [$directory], $disk);
    }

    /**
     * Move a file from one path to another.
     *
     * @param string $oldPath The current path of the file.
     * @param string $newPath The new path for the file.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return bool True if moved, false otherwise.
     */
    public function move(string $oldPath, string $newPath, ?string $disk = null): bool
    {
        return $this->executeMethod('move', [$oldPath, $newPath], $disk);
    }

    /**
     * Get the file type (MIME type) of a file.
     *
     * @param string $path The path of the file.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return string The MIME type of the file.
     */
    public function getFileType(string $path, ?string $disk = null): string
    {
        return $this->executeMethod('mimeType', [$path], $disk);
    }

    /**
     * Copy a file from a source path to a destination path.
     *
     * @param string $sourcePath The source path of the file.
     * @param string $destinationPath The destination path for the copied file.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return bool True if copied, false otherwise.
     */
    public function copy(string $sourcePath, string $destinationPath, ?string $disk = null): bool
    {
        return $this->executeMethod('copy', [$sourcePath, $destinationPath], $disk);
    }

    /**
     * Prepend data to a file.
     *
     * @param string $path The path of the file.
     * @param string $data The data to prepend.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return bool True if successful, false otherwise.
     */
    public function prepend(string $path, string $data, ?string $disk = null): bool
    {
        return $this->executeMethod('prepend', [$path, $data], $disk);
    }

    /**
     * Append data to a file.
     *
     * @param string $path The path of the file.
     * @param string $data The data to append.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return bool True if successful, false otherwise.
     */
    public function append(string $path, string $data, ?string $disk = null): bool
    {
        return $this->executeMethod('append', [$path, $data], $disk);
    }

    /**
     * Create a new directory.
     *
     * @param string $path The path of the directory to create.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return bool True if created, false otherwise.
     */
    public function makeDirectory(string $path, ?string $disk = null): bool
    {
        return $this->executeMethod('makeDirectory', [$path], $disk);
    }

    /**
     * Delete a directory.
     *
     * @param string $path The path of the directory to delete.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return bool True if deleted, false otherwise.
     */
    public function deleteDirectory(string $path, ?string $disk = null): bool
    {
        return $this->executeMethod('deleteDirectory', [$path], $disk);
    }

    /**
     * Enable or disable error logging.
     *
     * @param bool $log Whether to enable logging.
     * @return self Returns the current instance for method chaining.
     */
    public function withLog(bool $log = true): self
    {
        $this->logErrors = $log;
        return $this;
    }

    /**
     * Enable or disable throwing errors.
     *
     * @param bool $throw Whether to enable throwing exceptions on errors.
     * @return self Returns the current instance for method chaining.
     */
    public function withError(bool $throw = true): self
    {
        $this->throwErrors = $throw;
        return $this;
    }

    /**
     * Execute a custom storage method. WIP - Work in Progress!
     *
     * This method allows you to call any method on the storage disk that is not explicitly defined in the EasyStorage class.
     * If the method does not exist on the storage disk, a StorageMethodNotSupportedException is thrown.
     *
     * @param string $method The name of the method to execute.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return CustomMethod An instance of the custom method, which can be executed or modified with parameters.
     */
    public function customMethod(string $method, array $parameters = [], ?string $disk = null): CustomMethod
    {
        $customMethod = new CustomMethod($method, $disk);
        $customMethod->withParameters($parameters);
        return $customMethod;
    }

    /**
     * Executes a storage method with error handling.
     *
     * @param string $method The name of the method to execute.
     * @param array $parameters The parameters to pass to the method.
     * @param string|null $disk The storage disk name. If null, uses the default disk.
     * @return mixed The result of the executed method.
     * @throws StorageMethodNotSupportedException if the method is not supported.
     * @throws Exception if an error occurs and throwing is enabled.
     */
    protected function executeMethod(string $method, array $parameters, ?string $disk = null)
    {
        $diskInstance = $this->disk($disk);

        if (!method_exists($diskInstance, $method)) {
            if ($this->logErrors) {
                Log::error("EasyStorage: Method {$method} not supported on disk {$disk}.");
            }
            return $this->throwErrors
                ? throw new StorageMethodNotSupportedException("Method {$method} not supported on the selected storage disk.")
                : false;
        }

        try {
            return $diskInstance->{$method}(...$parameters);
        } catch (Exception $e) {
            if ($this->logErrors) {
                Log::error("EasyStorage: Error executing method {$method} on disk {$disk}: {$e->getMessage()}");
            }
            return $this->throwErrors ? throw $e : false;
        }
    }
}
