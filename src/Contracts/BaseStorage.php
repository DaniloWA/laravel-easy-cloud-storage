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
     * @return string|false The file path if successful; false otherwise.
     */
    public function upload(UploadedFile $file, string $path, ?string $newName = null): string|false;

    /**
     * Download a file from the specified path on the storage disk.
     *
     * @param string $path The path of the file to download.
     * @param string|null $newName Optional. The new name for the downloaded file. If not provided, the original name is used.
     * @return BinaryFileResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If the file does not exist.
     */
    public function download(string $path, ?string $newName = null): BinaryFileResponse;

    /**
     * Returns the URL of the stored file.
     *
     * @param string $path The path of the file.
     * @return string The URL of the file.
     */
    public function url(string $path): string;

    /**
     * Deletes a file at the specified path.
     *
     * @param string $path The path of the file to be deleted.
     * @return bool Returns true on success, false otherwise.
     */
    public function delete(string $path): bool;

    /**
     * Checks if a file exists at the specified path.
     *
     * @param string $path The path of the file.
     * @return bool Returns true if it exists, false otherwise.
     */
    public function exists(string $path): bool;

    /**
     * Copies a file from a source path to a destination path.
     *
     * @param string $from The path of the source file.
     * @param string $to The destination path.
     * @return bool Returns true on success, false otherwise.
     */
    public function copy(string $from, string $to): bool;

    /**
     * Moves or renames a file.
     *
     * @param string $from The old path of the file.
     * @param string $to The new path of the file.
     * @return bool Returns true on success, false otherwise.
     */
    public function move(string $from, string $to): bool;

    /**
     * Gets the size of a file.
     *
     * @param string $path The path of the file.
     * @return int|false The size of the file in bytes, or false on failure.
     */
    public function size(string $path): int|false;

    /**
     * Gets the last modified time of a file.
     *
     * @param string $path The path of the file.
     * @return int|false The last modified timestamp, or false on failure.
     */
    public function lastModified(string $path): int|false;

    /**
     * Gets the metadata of a file.
     *
     * @param string $path The path of the file.
     * @return array|false An array of metadata if successful; false otherwise.
     */
    public function getMetadata(string $path): array|false;

    /**
     * Sets metadata for a file.
     *
     * @param string $path The path of the file.
     * @param array $metadata The metadata to set.
     * @return bool Returns true on success, false otherwise.
     */
    public function setMetadata(string $path, array $metadata): bool;

    /**
     * Lists all files in the specified directory.
     *
     * @param string $directory The directory path.
     * @return array An array of file paths.
     */
    public function listFiles(string $directory): array;

    /**
     * Prepends data to a file.
     *
     * @param string $path The path of the file.
     * @param string $data The data to prepend.
     * @return bool Returns true on success, false otherwise.
     */
    public function prepend(string $path, string $data): bool;

    /**
     * Appends data to a file.
     *
     * @param string $path The path of the file.
     * @param string $data The data to append.
     * @return bool Returns true on success, false otherwise.
     */
    public function append(string $path, string $data): bool;

    /**
     * Creates a new directory.
     *
     * @param string $path The path of the directory to create.
     * @return bool Returns true on success, false otherwise.
     */
    public function makeDirectory(string $path): bool;

    /**
     * Deletes a directory.
     *
     * @param string $path The path of the directory to delete.
     * @return bool Returns true on success, false otherwise.
     */
    public function deleteDirectory(string $path): bool;

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
}
