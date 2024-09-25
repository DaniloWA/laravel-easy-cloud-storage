# Laravel EasyCloudStorage

Laravel EasyCloudStorage is a flexible and intuitive package designed to simplify cloud storage management within Laravel applications. It provides a clean interface for interacting with various storage providers, including local disks, Amazon S3, and Google Cloud Storage.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Initial Example](#initial-example)
  - [File Operations](#file-operations)
    - [Uploading Files](#uploading-files)
    - [Downloading Files](#downloading-files)
    - [Getting File URL](#getting-file-url)
    - [Deleting Files](#deleting-files)
    - [Checking File Existence](#checking-file-existence)
    - [Copying Files](#copying-files)
    - [Moving Files](#moving-files)
    - [Getting File Size](#getting-file-size)
    - [Getting Last Modified Time](#getting-last-modified-time)
    - [Getting Metadata](#getting-metadata)
    - [Setting Metadata](#setting-metadata)
    - [Listing Files](#listing-files)
    - [Prepending and Appending Data](#prepending-and-appending-data)
    - [Creating and Deleting Directories](#creating-and-deleting-directories)
  - [Error Handling](#error-handling)
  - [Driver-Specific Method Availability](#driver-specific-method-availability)
  - [Design Patterns: Facade and Contracts](#design-patterns-facade-and-contracts)
- [License](#license)

## Installation

To install the package, run the following command via Composer:

```bash
composer require danilowa/laravel-easy-cloud-storage
```

After installation, the service provider will be automatically registered. To customize the configuration, publish the package's config file:

```bash
php artisan vendor:publish --provider="Danilowa\LaravelEasyCloudStorage\EasyCloudStorageServiceProvider"
```

## Configuration

The configuration file is located at `config/easycloudstorage.php`. The main settings include:

```php
return [
    'default' => 'local', // Default disk for storage operations.
    'log_errors' => false, // Enable error logging.
    'throw_errors' => false, // Enable exception throwing for errors.
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'), // Root path for local storage.
        ],
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],
        'google' => [
            'driver' => 'gcs',
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'key_file' => env('GOOGLE_CLOUD_KEY_FILE'),
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
            'url' => env('GOOGLE_CLOUD_URL'),
        ],
        // Additional providers can be added as needed.
    ],
];
```

## Usage

After configuration, you can use the EasyCloudStorage package for various operations.

### Initial Example

This section demonstrates how to perform a file upload using the EasyCloudStorage package. The example illustrates the process of uploading a file to a specified directory, along with optional logging and error handling configurations.

```php
use Danilowa\LaravelEasyCloudStorage\Facades\EasyStorage;

// Assume $uploadedFile is an instance of UploadedFile, obtained from an HTTP request.
$filePath = EasyStorage::upload($uploadedFile, 'uploads/myfile.txt')
    ->withLog(true)  // Set error logging behavior. Defaults to true if not specified.
    ->withError(false) // Set error handling behavior. Defaults to true if not specified.
    ->setDisk('s3'); // Specify the disk manually instead of using the default.

// Check if the upload was successful
if($filePath === false) {
    return echo "Error uploading the file.";
}

echo "File uploaded successfully: $filePath";

```

### Explanation of the Code:

1. **Use Statement:**

   - The `use` statement imports the `EasyStorage` facade, which allows you to access storage functionalities easily.

2. **Uploading a File:**

   - The `upload` method is called on the `EasyStorage` facade, taking in:
     - `$uploadedFile`: An instance of `UploadedFile`, typically obtained from a file upload in an HTTP request.
     - `'uploads/myfile.txt'`: The destination path where the file will be stored on the disk.

3. **Error Logging Configuration:**

   - The `withLog` method allows you to control error logging during the upload process:
     - **Enabled (true):** Error logging is activated for the upload operation.
     - **Disabled (false):** Turns off error logging.
     - **Default Behavior:** If called without parameters, it assumes `true`, enabling error logging by default.
     - **Configuration Fallback:** If not used, the package utilizes the error logging setting specified in the configuration file.

4. **Error Handling Configuration:**

   - The `withError` method configures whether to throw exceptions when errors occur during the upload process:
     - **Throw Exceptions (true):** System will throw exceptions for any encountered errors.
     - **Do Not Throw Exceptions (false):** Prevents exceptions from being thrown.
     - **Default Behavior:** If called without parameters, it defaults to `true`.
     - **Configuration Fallback:** Follows the error handling configuration specified in the configuration file.

5. **Setting the Disk:**

   - The `setDisk` method allows you to manually specify the storage disk to be used, overriding the default disk configuration.

6. **Success Check:**
   - After attempting the upload, the result is stored in `$filePath`. This variable will either contain a string representing the path to the uploaded file or `false` if the upload failed.
   - The `if` statement checks the value of `$filePath` to determine the success of the upload:
     - **Successful Upload:** If `$filePath` is of type `string`, it prints a message displaying the path of the uploaded file.
     - **Unsuccessful Upload:** If `$filePath` is `false`, it indicates that the upload failed, and an error message is displayed.

### File Operations

#### Uploading Files

Upload a file to the specified path on the storage disk.

```php
$filePath = EasyStorage::upload($uploadedFile, 'uploads/myfile.txt', 'banana.txt');

// Upload to a specific disk (S3)
$filePathS3 = EasyStorage::upload($uploadedFile, 'uploads/myfile.txt')->setDisk('s3');
```

- **Parameters:**

  - `UploadedFile $file`: The file to upload.
  - `string $path`: The destination path for the upload.
  - `string|null $newName`: Optional. The new name for the file; the original name is used if not provided.

- **Returns:** `string|false` - The file path if successful; `false` otherwise.

#### Downloading Files

Download a file from the specified path on the storage disk.

```php
return EasyStorage::download('uploads/myfile.txt', 'name.txt')->withLog();
```

- **Parameters:**

  - `string $path`: The path of the file to download.
  - `string|null $newName`: Optional. The new name for the downloaded file; the original name is used if not provided.

- **Returns:** `BinaryFileResponse`

- **Throws:** `NotFoundHttpException` if the file does not exist.

#### Getting File URL

Retrieve the URL of the stored file.

```php
$fileUrl = EasyStorage::url('uploads/myfile.txt');
```

- **Parameters:**

  - `string $path`: The path of the file.

- **Returns:** `string` - The URL of the file.

#### Deleting Files

Delete a file at the specified path.

```php
$deleted = EasyStorage::delete('uploads/myfile.txt');
```

- **Parameters:**

  - `string $path`: The path of the file to be deleted.

- **Returns:** `bool` - Returns `true` on success, `false` otherwise.

#### Checking File Existence

Check if a file exists at the specified path.

```php
$exists = EasyStorage::exists('uploads/myfile.txt');
```

- **Parameters:**

  - `string $path`: The path of the file.

- **Returns:** `bool` - Returns `true` if it exists, `false` otherwise.

#### Copying Files

Copy a file from a source path to a destination path.

```php
$copied = EasyStorage::copy('uploads/myfile.txt', 'uploads/myfile_copy.txt');
```

- **Parameters:**

  - `string $from`: The path of the source file.
  - `string $to`: The destination path.

- **Returns:** `bool` - Returns `true` on success, `false` otherwise.

#### Moving Files

Move or rename a file.

```php
$success = EasyStorage::move('uploads/myfile.txt', 'uploads/newfile.txt');
```

- **Parameters:**
  - `string $from`: The old path of the file.
  - `string

$to`: The new path.

- **Returns:** `bool` - Returns `true` on success, `false` otherwise.

#### Getting File Size

Retrieve the size of a file in bytes.

```php
$size = EasyStorage::size('uploads/myfile.txt');
```

- **Parameters:**

  - `string $path`: The path of the file.

- **Returns:** `int|null` - Returns the file size in bytes or `null` if the file does not exist.

#### Getting Last Modified Time

Get the last modified timestamp of a file.

```php
$lastModified = EasyStorage::lastModified('uploads/myfile.txt');
```

- **Parameters:**

  - `string $path`: The path of the file.

- **Returns:** `int|null` - Returns the timestamp or `null` if the file does not exist.

#### Getting Metadata

Retrieve metadata for a file.

```php
$metadata = EasyStorage::metadata('uploads/myfile.txt');
```

- **Parameters:**

  - `string $path`: The path of the file.

- **Returns:** `array|null` - Returns metadata or `null` if the file does not exist.

#### Setting Metadata

Set metadata for a file.

```php
$success = EasyStorage::setMetadata('uploads/myfile.txt', [
    'Content-Type' => 'application/pdf',
]);
```

- **Parameters:**

  - `string $path`: The path of the file.
  - `array $metadata`: The metadata to set.

- **Returns:** `bool` - Returns `true` on success, `false` otherwise.

#### Listing Files

List files in a specified directory.

```php
$files = EasyStorage::list('uploads');
```

- **Parameters:**

  - `string $directory`: The directory path.

- **Returns:** `array` - An array of file names.

#### Prepending and Appending Data

Prepend or append data to a file.

```php
$successPrepend = EasyStorage::prepend('uploads/myfile.txt', 'Header data');
$successAppend = EasyStorage::append('uploads/myfile.txt', 'Footer data');
```

- **Parameters:**

  - `string $path`: The path of the file.
  - `string $data`: The data to prepend or append.

- **Returns:** `bool` - Returns `true` on success, `false` otherwise.

#### Creating and Deleting Directories

Create or delete a directory.

```php
EasyStorage::createDirectory('uploads/new_directory');
EasyStorage::deleteDirectory('uploads/old_directory');
```

- **Parameters:**

  - `string $directory`: The directory path.

- **Returns:** `bool` - Returns `true` on success, `false` otherwise.

### Error Handling

You can enable error logging and exception throwing through the configuration or method chaining. Use the `withLog()` and `withError()` methods to customize error handling per operation.

### Driver-Specific Method Availability

It's important to note that not all storage drivers will support every method available in the EasyCloudStorage package. The functionality provided depends exclusively on the capabilities of each specific driver, such as Google Cloud Storage, Amazon S3, and others.

For example, while uploading and deleting files are common operations across most drivers, certain methods like managing metadata or creating directories may not be supported by all drivers.

Rest assured, the EasyCloudStorage package has built-in logic to handle these discrepancies gracefully. If a method is not available for a specific driver, an informative error will be returned, ensuring that you are promptly notified of the limitation. This design allows you to implement your logic without the worry of unexpected failures, promoting a smooth development experience.

### Design Patterns: Facade and Contracts

**Facade**

The Facade pattern in Laravel offers a simple and intuitive interface to access classes within the application’s service container. By utilizing the EasyStorage facade, you can invoke methods directly without having to resolve the underlying service each time. This approach not only streamlines your code but also improves readability, allowing you to concentrate on building features rather than managing complex dependencies. The result is a cleaner and more efficient API that enhances your development experience.

**Contracts**

Contracts in Laravel define the expected behaviors of services, providing a clear guideline for how they should operate. By utilizing contracts, you ensure that your application can easily switch between different implementations of a service without affecting the code that interacts with it. This flexibility is beneficial for developers, as it allows for easier updates or changes to the underlying logic without requiring significant code alterations.

For users, this means a more reliable and maintainable application. You can confidently integrate new features or optimize existing ones while ensuring that the core functionality remains intact. By adhering to this practice, you also promote a clean architecture that is easier to understand and extend, ultimately enhancing the long-term sustainability of your codebase.

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
