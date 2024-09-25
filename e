[1mdiff --git a/src/EasyStorage.php b/src/EasyStorage.php[m
[1mindex 7a3caf8..3ddea1d 100644[m
[1m--- a/src/EasyStorage.php[m
[1m+++ b/src/EasyStorage.php[m
[36m@@ -6,17 +6,11 @@[m [muse Exception;[m
 use Illuminate\Http\UploadedFile;[m
 use Illuminate\Support\Facades\Log;[m
 use Illuminate\Support\Facades\Storage;[m
[31m-use Danilowa\LaravelEasyCloudStorage\CustomMethod;[m
 use Symfony\Component\HttpFoundation\BinaryFileResponse;[m
 use Danilowa\LaravelEasyCloudStorage\Contracts\BaseStorage;[m
 use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;[m
 use Danilowa\LaravelEasyCloudStorage\Exceptions\StorageMethodNotSupportedException;[m
 [m
[31m-/**[m
[31m- * Class EasyStorage[m
[31m- *[m
[31m- * Provides methods for cloud storage manipulation based on the BaseStorage contract.[m
[31m- */[m
 class EasyStorage implements BaseStorage[m
 {[m
     protected string $disk;[m
[36m@@ -25,58 +19,91 @@[m [mclass EasyStorage implements BaseStorage[m
 [m
     /**[m
      * EasyStorage constructor.[m
[31m-     *[m
[31m-     * @param string|null $disk The storage disk to use. Defaults to the configured default disk.[m
[32m+[m[32m     * @param string|null $disk[m
      */[m
     public function __construct(?string $disk = null)[m
     {[m
         $this->disk = $disk ?: config('easycloudstorage.default');[m
[31m-        $this->logErrors = config('easycloudstorage.log_errors', true);[m
[31m-        $this->throwErrors = config('easycloudstorage.throw_errors', true);[m
[32m+[m[32m        $this->logErrors = false;[m
[32m+[m[32m        $this->throwErrors = false;[m
     }[m
 [m
     /**[m
[31m-     * Get the storage disk instance.[m
[32m+[m[32m     * Set the disk.[m
      *[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return \Illuminate\Contracts\Filesystem\Filesystem[m
[32m+[m[32m     * @param string $disk[m
[32m+[m[32m     * @return self[m
      */[m
[31m-    protected function disk(?string $disk = null)[m
[32m+[m[32m    public function setDisk(string $disk): self[m
     {[m
[31m-        return Storage::disk($disk ?: $this->disk);[m
[32m+[m[32m        $this->disk = $disk;[m
[32m+[m[32m        return $this;[m
     }[m
 [m
     /**[m
[31m-     * Upload a file to the specified path on the storage disk.[m
[32m+[m[32m     * Enable or disable logging of errors.[m
      *[m
[31m-     * @param UploadedFile $file The file to upload.[m
[31m-     * @param string $path The destination path for the upload.[m
[31m-     * @param string|null $newName Optional. The new name for the file. If not provided, the original name is used.[m
[31m-     * @param string|null $disk Optional. The storage disk name. If null, uses the default disk.[m
[31m-     * @return string|false The file path if successful; false otherwise.[m
[32m+[m[32m     * @param bool $log[m
[32m+[m[32m     * @return self[m
      */[m
[31m-    public function upload(UploadedFile $file, string $path, ?string $newName = null, ?string $disk = null): string|false[m
[32m+[m[32m    public function withLog(bool $log = true): self[m
     {[m
[31m-        $fileName = $this->getUniqueFileName($file, $path, $newName, $disk);[m
[31m-        return $this->executeMethod('putFileAs', [$path, $file, $fileName], $disk);[m
[32m+[m[32m        $this->logErrors = $log;[m
[32m+[m[32m        return $this;[m
     }[m
 [m
     /**[m
[31m-     * Generate a unique file name to avoid conflicts in the storage.[m
[32m+[m[32m     * Enable or disable throwing of errors.[m
      *[m
[31m-     * @param UploadedFile $file The file being uploaded.[m
[31m-     * @param string $path The destination path.[m
[31m-     * @param string|null $newName Optional. The new name for the file.[m
[31m-     * @param string|null $disk Optional. The storage disk name.[m
[31m-     * @return string The unique file name.[m
[32m+[m[32m     * @param bool $throw[m
[32m+[m[32m     * @return self[m
      */[m
[31m-    private function getUniqueFileName(UploadedFile $file, string $path, ?string $newName = null, ?string $disk = null): string[m
[32m+[m[32m    public function withError(bool $throw = true): self[m
[32m+[m[32m    {[m
[32m+[m[32m        $this->throwErrors = $throw;[m
[32m+[m[32m        return $this;[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Upload a file to the storage.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @param UploadedFile $file[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @param string|null $newName[m
[32m+[m[32m     * @return string|false[m
[32m+[m[32m     */[m
[32m+[m[32m    public function upload(UploadedFile $file, string $path, ?string $newName = null): string|false[m
[32m+[m[32m    {[m
[32m+[m[32m        $fileName = $this->getUniqueFileName($file, $path, $newName);[m
[32m+[m[32m        return $this->executeMethod('putFileAs', [$path, $file, $fileName]);[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Get the disk instance.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @param string|null $disk[m
[32m+[m[32m     * @return \Illuminate\Contracts\Filesystem\Filesystem[m
[32m+[m[32m     */[m
[32m+[m[32m    protected function disk(?string $disk = null)[m
[32m+[m[32m    {[m
[32m+[m[32m        return Storage::disk($disk ?: $this->disk);[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Get a unique file name for the uploaded file.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @param UploadedFile $file[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @param string|null $newName[m
[32m+[m[32m     * @return string[m
[32m+[m[32m     */[m
[32m+[m[32m    protected function getUniqueFileName(UploadedFile $file, string $path, ?string $newName = null): string[m
     {[m
         $fileName = $newName ?? $file->getClientOriginalName();[m
         $fullPath = $path . '/' . $fileName;[m
 [m
         $i = 1;[m
[31m-        while ($this->exists($fullPath, $disk)) {[m
[32m+[m[32m        while ($this->exists($fullPath)) {[m
             $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . "_{$i}." . $file->getClientOriginalExtension();[m
             $fullPath = $path . '/' . $fileName;[m
             $i++;[m
[36m@@ -86,17 +113,16 @@[m [mclass EasyStorage implements BaseStorage[m
     }[m
 [m
     /**[m
[31m-     * Download a file from the specified path on the storage disk.[m
[32m+[m[32m     * Download a file from the storage.[m
      *[m
[31m-     * @param string $path The path of the file to download.[m
[31m-     * @param string|null $newName Optional. The new name for the downloaded file. If not provided, the original name is used.[m
[31m-     * @param string|null $disk Optional. The storage disk name. If null, uses the default disk.[m
[31m-     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse[m
[31m-     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If the file does not exist.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @param string|null $newName[m
[32m+[m[32m     * @return BinaryFileResponse[m
[32m+[m[32m     * @throws NotFoundHttpException[m
      */[m
[31m-    public function download(string $path, ?string $newName = null, ?string $disk = null): BinaryFileResponse[m
[32m+[m[32m    public function download(string $path, ?string $newName = null): BinaryFileResponse[m
     {[m
[31m-        $storageDisk = $this->disk($disk);[m
[32m+[m[32m        $storageDisk = $this->disk();[m
         $fullPath = $storageDisk->path($path);[m
 [m
         if (!$storageDisk->exists($path)) {[m
[36m@@ -104,274 +130,204 @@[m [mclass EasyStorage implements BaseStorage[m
         }[m
 [m
         $fileName = $newName ?? basename($path);[m
[31m-[m
         return response()->download($fullPath, $fileName);[m
     }[m
 [m
[31m-[m
     /**[m
[31m-     * Get the URL of a file in the storage disk.[m
[32m+[m[32m     * Delete a file from the storage.[m
      *[m
[31m-     * @param string $path The path of the file.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return string The URL of the file.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @return bool[m
      */[m
[31m-    public function url(string $path, ?string $disk = null): string[m
[32m+[m[32m    public function delete(string $path): bool[m
     {[m
[31m-        return $this->executeMethod('url', [$path], $disk);[m
[32m+[m[32m        return $this->executeMethod('delete', [$path]);[m
     }[m
 [m
     /**[m
[31m-     * Delete a file from the storage disk.[m
[32m+[m[32m     * Check if a file exists in the storage.[m
      *[m
[31m-     * @param string $path The path of the file to delete.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return bool True if deleted, false otherwise.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @return bool[m
      */[m
[31m-    public function delete(string $path, ?string $disk = null): bool[m
[32m+[m[32m    public function exists(string $path): bool[m
     {[m
[31m-        return $this->executeMethod('delete', [$path], $disk);[m
[32m+[m[32m        return $this->executeMethod('exists', [$path]);[m
     }[m
 [m
     /**[m
[31m-     * Check if a file exists on the storage disk.[m
[32m+[m[32m     * Get the URL of a file in the storage.[m
      *[m
[31m-     * @param string $path The path of the file.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return bool True if the file exists, false otherwise.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @return string[m
      */[m
[31m-    public function exists(string $path, ?string $disk = null): bool[m
[32m+[m[32m    public function url(string $path): string[m
     {[m
[31m-        return $this->executeMethod('exists', [$path], $disk);[m
[32m+[m[32m        return $this->executeMethod('url', [$path]);[m
     }[m
 [m
     /**[m
[31m-     * Get metadata for a file.[m
[32m+[m[32m     * Copy a file to a new location.[m
      *[m
[31m-     * @param string $path The path of the file.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return array|false An array of metadata if successful, false otherwise.[m
[32m+[m[32m     * @param string $from[m
[32m+[m[32m     * @param string $to[m
[32m+[m[32m     * @return bool[m
      */[m
[31m-    public function getMetadata(string $path, ?string $disk = null): array|false[m
[32m+[m[32m    public function copy(string $from, string $to): bool[m
     {[m
[31m-        return $this->executeMethod('getMetadata', [$path], $disk);[m
[32m+[m[32m        return $this->executeMethod('copy', [$from, $to]);[m
     }[m
 [m
     /**[m
[31m-     * Set metadata for a file (currently a placeholder).[m
[32m+[m[32m     * Move a file to a new location.[m
      *[m
[31m-     * @param string $path The path of the file.[m
[31m-     * @param array $metadata The metadata to set.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return bool Always returns false as it's not implemented.[m
[32m+[m[32m     * @param string $from[m
[32m+[m[32m     * @param string $to[m
[32m+[m[32m     * @return bool[m
      */[m
[31m-    public function setMetadata(string $path, array $metadata, ?string $disk = null): bool[m
[32m+[m[32m    public function move(string $from, string $to): bool[m
     {[m
[31m-        return false; // Placeholder for metadata setting[m
[32m+[m[32m        return $this->executeMethod('move', [$from, $to]);[m
     }[m
 [m
     /**[m
[31m-     * List files in a directory.[m
[32m+[m[32m     * Get the size of a file in the storage.[m
      *[m
[31m-     * @param string $directory The directory to list files from.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return array An array of file paths.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @return int|false[m
      */[m
[31m-    public function listFiles(string $directory, ?string $disk = null): array[m
[32m+[m[32m    public function size(string $path): int|false[m
     {[m
[31m-        return $this->executeMethod('files', [$directory], $disk);[m
[32m+[m[32m        return $this->executeMethod('size', [$path]);[m
     }[m
 [m
     /**[m
[31m-     * Move a file from one path to another.[m
[32m+[m[32m     * Get the last modified time of a file in the storage.[m
      *[m
[31m-     * @param string $oldPath The current path of the file.[m
[31m-     * @param string $newPath The new path for the file.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return bool True if moved, false otherwise.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @return int|false[m
      */[m
[31m-    public function move(string $oldPath, string $newPath, ?string $disk = null): bool[m
[32m+[m[32m    public function lastModified(string $path): int|false[m
     {[m
[31m-        $storageDisk = $this->disk($disk);[m
[31m-    [m
[31m-        if ($storageDisk->exists($newPath)) {[m
[31m-            $pathInfo = pathinfo($newPath);[m
[31m-            $baseName = $pathInfo['filename'];[m
[31m-            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';[m
[31m-            $i = 1;[m
[31m-[m
[31m-            do {[m
[31m-                $newPath = $pathInfo['dirname'] . '/' . $baseName . "_{$i}" . $extension;[m
[31m-                $i++;[m
[31m-            } while ($storageDisk->exists($newPath));[m
[31m-        }[m
[31m-[m
[31m-        return $this->executeMethod('move', [$oldPath, $newPath], $disk);[m
[32m+[m[32m        return $this->executeMethod('lastModified', [$path]);[m
     }[m
 [m
     /**[m
[31m-     * Get the file type (MIME type) of a file.[m
[32m+[m[32m     * Get metadata of a file in the storage.[m
      *[m
[31m-     * @param string $path The path of the file.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return string The MIME type of the file.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @param string|null $disk[m
[32m+[m[32m     * @return array|false[m
      */[m
[31m-    public function getFileType(string $path, ?string $disk = null): string[m
[32m+[m[32m    public function getMetadata(string $path, ?string $disk = null): array|false[m
     {[m
[31m-        return $this->executeMethod('mimeType', [$path], $disk);[m
[32m+[m[32m        return $this->executeMethod('getMetadata', [$path]);[m
     }[m
 [m
     /**[m
[31m-     * Copy a file from a source path to a destination path.[m
[32m+[m[32m     * Set metadata of a file in the storage.[m
      *[m
[31m-     * @param string $sourcePath The source path of the file.[m
[31m-     * @param string $destinationPath The destination path for the copied file.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return bool True if copied, false otherwise.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @param array $metadata[m
[32m+[m[32m     * @param string|null $disk[m
[32m+[m[32m     * @return bool[m
      */[m
[31m-    public function copy(string $sourcePath, string $destinationPath, ?string $disk = null): bool[m
[32m+[m[32m    public function setMetadata(string $path, array $metadata, ?string $disk = null): bool[m
     {[m
[31m-        $storageDisk = $this->disk($disk);[m
[31m-[m
[31m-        if ($storageDisk->exists($destinationPath)) {[m
[31m-            $pathInfo = pathinfo($destinationPath);[m
[31m-            $baseName = $pathInfo['filename'];[m
[31m-            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';[m
[31m-            $i = 1;[m
[31m-[m
[31m-            do {[m
[31m-                $destinationPath = $pathInfo['dirname'] . '/' . $baseName . "_copy_{$i}" . $extension;[m
[31m-                $i++;[m
[31m-            } while ($storageDisk->exists($destinationPath));[m
[31m-        }[m
[32m+[m[32m        return $this->executeMethod('setMetadata', [$path, $metadata]);[m
[32m+[m[32m    }[m
 [m
[31m-        return $this->executeMethod('copy', [$sourcePath, $destinationPath], $disk);[m
[32m+[m[32m    /**[m
[32m+[m[32m     * List files in a directory.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @param string $directory[m
[32m+[m[32m     * @param string|null $disk[m
[32m+[m[32m     * @return array[m
[32m+[m[32m     */[m
[32m+[m[32m    public function listFiles(string $directory, ?string $disk = null): array[m
[32m+[m[32m    {[m
[32m+[m[32m        return $this->executeMethod('files', [$directory]);[m
     }[m
 [m
     /**[m
      * Prepend data to a file.[m
      *[m
[31m-     * @param string $path The path of the file.[m
[31m-     * @param string $data The data to prepend.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return bool True if successful, false otherwise.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @param string $data[m
[32m+[m[32m     * @param string|null $disk[m
[32m+[m[32m     * @return bool[m
      */[m
     public function prepend(string $path, string $data, ?string $disk = null): bool[m
     {[m
[31m-        if (!$this->disk($disk)->exists($path)) {[m
[31m-            throw new NotFoundHttpException("File not found for prepending data.");[m
[31m-        }[m
[31m-    [m
[31m-        return $this->executeMethod('prepend', [$path, $data], $disk);[m
[32m+[m[32m        return $this->executeMethod('prepend', [$path, $data]);[m
     }[m
 [m
     /**[m
      * Append data to a file.[m
      *[m
[31m-     * @param string $path The path of the file.[m
[31m-     * @param string $data The data to append.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return bool True if successful, false otherwise.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @param string $data[m
[32m+[m[32m     * @param string|null $disk[m
[32m+[m[32m     * @return bool[m
      */[m
     public function append(string $path, string $data, ?string $disk = null): bool[m
     {[m
[31m-        if (!$this->disk($disk)->exists($path)) {[m
[31m-            throw new NotFoundHttpException("File not found for appending data.");[m
[31m-        }[m
[31m-    [m
[31m-        return $this->executeMethod('append', [$path, $data], $disk);[m
[32m+[m[32m        return $this->executeMethod('append', [$path, $data]);[m
     }[m
 [m
     /**[m
[31m-     * Create a new directory.[m
[32m+[m[32m     * Create a directory.[m
      *[m
[31m-     * @param string $path The path of the directory to create.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return bool True if created, false if it already exists or on failure.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @param string|null $disk[m
[32m+[m[32m     * @return bool[m
      */[m
     public function makeDirectory(string $path, ?string $disk = null): bool[m
     {[m
[31m-        if ($this->disk($disk)->exists($path)) {[m
[31m-            return false;[m
[31m-        }[m
[31m-    [m
[31m-        return $this->executeMethod('makeDirectory', [$path], $disk);[m
[32m+[m[32m        return $this->executeMethod('makeDirectory', [$path]);[m
     }[m
 [m
     /**[m
      * Delete a directory.[m
      *[m
[31m-     * @param string $path The path of the directory to delete.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return bool True if deleted, false if it doesn't exist or on failure.[m
[32m+[m[32m     * @param string $path[m
[32m+[m[32m     * @param string|null $disk[m
[32m+[m[32m     * @return bool[m
      */[m
     public function deleteDirectory(string $path, ?string $disk = null): bool[m
     {[m
[31m-        if (!$this->disk($disk)->exists($path)) {[m
[31m-            return false;[m
[31m-        }[m
[31m-[m
[31m-        return $this->executeMethod('deleteDirectory', [$path], $disk);[m
[31m-    }[m
[31m-[m
[31m-    /**[m
[31m-     * Enable or disable error logging.[m
[31m-     *[m
[31m-     * @param bool $log Whether to enable logging.[m
[31m-     * @return self Returns the current instance for method chaining.[m
[31m-     */[m
[31m-    public function withLog(bool $log = true): self[m
[31m-    {[m
[31m-        $this->logErrors = $log;[m
[31m-        return $this;[m
[32m+[m[32m        return $this->executeMethod('deleteDirectory', [$path]);[m
     }[m
 [m
     /**[m
[31m-     * Enable or disable throwing errors.[m
[32m+[m[32m     * Execute a custom method on the storage disk.[m
      *[m
[31m-     * @param bool $throw Whether to enable throwing exceptions on errors.[m
[31m-     * @return self Returns the current instance for method chaining.[m
[32m+[m[32m     * @param string $method[m
[32m+[m[32m     * @param array $parameters[m
[32m+[m[32m     * @param string|null $disk[m
[32m+[m[32m     * @return mixed[m
      */[m
[31m-    public function withError(bool $throw = true): self[m
[32m+[m[32m    public function customMethod(string $method, array $parameters = [], ?string $disk = null)[m
     {[m
[31m-        $this->throwErrors = $throw;[m
[31m-        return $this;[m
[32m+[m[32m        return $this->executeMethod($method, $parameters, $disk);[m
     }[m
 [m
     /**[m
[31m-     * Execute a custom storage method. WIP - Work in Progress![m
[32m+[m[32m     * Execute a method on the storage disk.[m
      *[m
[31m-     * This method allows you to call any method on the storage disk that is not explicitly defined in the EasyStorage class.[m
[31m-     * If the method does not exist on the storage disk, a StorageMethodNotSupportedException is thrown.[m
[31m-     *[m
[31m-     * @param string $method The name of the method to execute.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return CustomMethod An instance of the custom method, which can be executed or modified with parameters.[m
[31m-     */[m
[31m-    public function customMethod(string $method, array $parameters = [], ?string $disk = null): CustomMethod[m
[31m-    {[m
[31m-        $customMethod = new CustomMethod($method, $disk);[m
[31m-        $customMethod->withParameters($parameters);[m
[31m-        return $customMethod;[m
[31m-    }[m
[31m-[m
[31m-    /**[m
[31m-     * Executes a storage method with error handling.[m
[32m+[m[32m     * @param string $method[m
[32m+[m[32m     * @param array $parameters[m
[32m+[m[32m     * @return mixed[m
      *[m
[31m-     * @param string $method The name of the method to execute.[m
[31m-     * @param array $parameters The parameters to pass to the method.[m
[31m-     * @param string|null $disk The storage disk name. If null, uses the default disk.[m
[31m-     * @return mixed The result of the executed method.[m
[31m-     * @throws StorageMethodNotSupportedException if the method is not supported.[m
[31m-     * @throws Exception if an error occurs and throwing is enabled.[m
[32m+[m[32m     * @throws StorageMethodNotSupportedException[m
      */[m
[31m-    protected function executeMethod(string $method, array $parameters, ?string $disk = null)[m
[32m+[m[32m    protected function executeMethod(string $method, array $parameters)[m
     {[m
[31m-        $diskInstance = $this->disk($disk);[m
[32m+[m[32m        $diskInstance = $this->disk();[m
 [m
         if (!method_exists($diskInstance, $method)) {[m
             if ($this->logErrors) {[m
[31m-                Log::error("EasyStorage: Method {$method} not supported on disk {$disk}.");[m
[32m+[m[32m                Log::error("EasyStorage: Method {$method} not supported on disk {$this->disk}.");[m
             }[m
             return $this->throwErrors[m
                 ? throw new StorageMethodNotSupportedException("Method {$method} not supported on the selected storage disk.")[m
[36m@@ -382,7 +338,7 @@[m [mclass EasyStorage implements BaseStorage[m
             return $diskInstance->{$method}(...$parameters);[m
         } catch (Exception $e) {[m
             if ($this->logErrors) {[m
[31m-                Log::error("EasyStorage: Error executing method {$method} on disk {$disk}: {$e->getMessage()}");[m
[32m+[m[32m                Log::error("EasyStorage: Error executing method {$method} on disk {$this->disk}: {$e->getMessage()}");[m
             }[m
             return $this->throwErrors ? throw $e : false;[m
         }[m
