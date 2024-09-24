<?php

namespace Danilowa\LaravelEasyCloudStorage\Contracts;

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Interface BaseStorage
 *
 * Defines the essential methods for cloud storage manipulation.
 */
interface BaseStorage
{
    /**
     * Upload a file to the specified path on the storage disk.
     *
     * @param UploadedFile $file The file to upload.
     * @param string $path The destination path for the upload.
     * @param string|null $newName Optional. The new name for the file. If not provided, the original name is used.
     * @param string|null $disk Optional. The storage disk name. If null, uses the default disk.
     * @return string|false The file path if successful; false otherwise.
     */
    public function upload(UploadedFile $file, string $path, ?string $newName = null, ?string $disk = null): string|false;

    /**
        * Download a file from the specified path on the storage disk.
        *
        * @param string $path The path of the file to download.
        * @param string|null $newName Optional. The new name for the downloaded file. If not provided, the original name is used.
        * @param string|null $disk Optional. The storage disk name. If null, uses the default disk.
        * @return BinaryFileResponse
        * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If the file does not exist.
        */
    public function download(string $path, ?string $newName = null, ?string $disk = null): BinaryFileResponse;

    /**
     * Returns the URL of the stored file.
     *
     * @param string $path The path of the file.
     * @param string|null $disk The disk to use (optional).
     * @return string The URL of the file.
     */
    public function url(string $path, ?string $disk = null): string;

    /**
     * Deletes a file at the specified path.
     *
     * @param string $path The path of the file to be deleted.
     * @param string|null $disk The disk to use (optional).
     * @return bool Returns true on success, false otherwise.
     */
    public function delete(string $path, ?string $disk = null): bool;

    /**
     * Checks if a file exists at the specified path.
     *
     * @param string $path The path of the file.
     * @param string|null $disk The disk to use (optional).
     * @return bool Returns true if it exists, false otherwise.
     */
    public function exists(string $path, ?string $disk = null): bool;

    /**
     * Obtains the metadata of a file.
     *
     * @param string $path The path of the file.
     * @param string|null $disk The disk to use (optional).
     * @return array|false Returns an array with the metadata or false on failure.
     */
    public function getMetadata(string $path, ?string $disk = null): array|false;

    /**
     * Sets the metadata of a file.
     *
     * @param string $path The path of the file.
     * @param array $metadata The metadata to be set.
     * @param string|null $disk The disk to use (optional).
     * @return bool Returns true on success, false otherwise.
     */
    public function setMetadata(string $path, array $metadata, ?string $disk = null): bool;

    /**
     * Lists the files in a directory.
     *
     * @param string $directory The path of the directory.
     * @param string|null $disk The disk to use (optional).
     * @return array The list of files.
     */
    public function listFiles(string $directory, ?string $disk = null): array;

    /**
     * Moves or renames a file.
     *
     * @param string $oldPath The old path of the file.
     * @param string $newPath The new path of the file.
     * @param string|null $disk The disk to use (optional).
     * @return bool Returns true on success, false otherwise.
     */
    public function move(string $oldPath, string $newPath, ?string $disk = null): bool;

    /**
     * Gets the MIME type of a file.
     *
     * @param string $path The path of the file.
     * @param string|null $disk The disk to use (optional).
     * @return string The MIME type of the file.
     */
    public function getFileType(string $path, ?string $disk = null): string;

    /**
     * Copies a file from a source path to a destination path.
     *
     * @param string $sourcePath The path of the source file.
     * @param string $destinationPath The destination path.
     * @param string|null $disk The disk to use (optional).
     * @return bool Returns true on success, false otherwise.
     */
    public function copy(string $sourcePath, string $destinationPath, ?string $disk = null): bool;

    /**
     * Prepends data to a file.
     *
     * @param string $path The path of the file.
     * @param string $data The data to be prepended.
     * @param string|null $disk The disk to use (optional).
     * @return bool Returns true on success, false otherwise.
     */
    public function prepend(string $path, string $data, ?string $disk = null): bool;

    /**
     * Appends data to the end of a file.
     *
     * @param string $path The path of the file.
     * @param string $data The data to be appended.
     * @param string|null $disk The disk to use (optional).
     * @return bool Returns true on success, false otherwise.
     */
    public function append(string $path, string $data, ?string $disk = null): bool;

    /**
     * Creates a new directory.
     *
     * @param string $path The path of the directory to be created.
     * @param string|null $disk The disk to use (optional).
     * @return bool Returns true on success, false if the directory already exists or on failure.
     */
    public function makeDirectory(string $path, ?string $disk = null): bool;

    /**
     * Deletes a directory.
     *
     * @param string $path The path of the directory to be deleted.
     * @param string|null $disk The disk to use (optional).
     * @return bool Returns true on success, false if the directory doesn't exist or on failure.
     */
    public function deleteDirectory(string $path, ?string $disk = null): bool;

    /**
     * Sets whether to log errors during operations.
     *
     * @param bool $log Whether to log errors.
     * @return self
     */
    public function withLog(bool $log = true): self;

    /**
     * Sets whether to throw exceptions for errors during operations.
     *
     * @param bool $throw Whether to throw exceptions.
     * @return self
     */
    public function withError(bool $throw = true): self;

    /**
     * Executes a custom method with parameters.
     *
     * @param string $method The name of the custom method.
     * @param array $parameters The parameters for the method.
     * @param string|null $disk The disk to use (optional).
     * @return mixed The result of the custom method execution.
     */
    public function customMethod(string $method, array $parameters = [], ?string $disk = null);

}
