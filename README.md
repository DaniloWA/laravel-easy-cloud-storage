# Laravel EasyCloudStorage

Laravel EasyCloudStorage is a flexible and intuitive package designed to streamline cloud storage management within Laravel applications. It provides a clean interface for interacting with various storage providers, including local disks, Amazon S3, and Google Cloud Storage.

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
    - [Managing Metadata](#managing-metadata)
    - [Listing Files](#listing-files)
    - [Moving and Copying Files](#moving-and-copying-files)
    - [Creating Directories](#creating-directories)
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

The configuration file is located at `config/easycloudstorage.php`. Key settings include:

```php
return [
    'default' => 'local', // Default disk for storage operations.
    'log_errors' => false, // Enable error logging.
    'throw_errors' => false, // Enable exception throwing.
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

```php
use Danilowa\LaravelEasyCloudStorage\Facades\EasyStorage;

$filePath = EasyStorage::upload($uploadedFile, 'uploads/myfile.txt')
    ->withLog(true)
    ->withError(true);
```

1. **Namespace Import**: Imports the `EasyStorage` facade for convenient method calls.
2. **Upload Method**:
   - **Parameters**:
     - `$uploadedFile`: An instance of `Illuminate\Http\UploadedFile`.
     - `'uploads/myfile.txt'`: Path for storing the file.
   - **Returns**: The name of the saved file or `false` on failure.
3. **Error Handling and Logging**:
   - **`withLog(true)`**: Enables error logging during the upload.
   - **`withError(true)`**: Throws exceptions for upload errors.

### File Operations

#### Uploading Files

To upload a file:

```php
// Upload to the default disk (local)
$filePath = EasyStorage::upload($uploadedFile, 'uploads/myfile.txt');

// Upload to a specific disk (S3)
$filePathS3 = EasyStorage::upload($uploadedFile, 'uploads/myfile.txt', 's3');
```

#### Downloading Files

To download a file:

```php
// Download from the default disk
return EasyStorage::download('uploads/myfile.txt');

// Download from a specific disk (S3)
return EasyStorage::download('uploads/myfile.txt', 's3');
```

#### Getting File URL

To retrieve the URL of a stored file:

```php
// Get URL from the default disk
$fileUrl = EasyStorage::url('uploads/myfile.txt');

// Get URL from a specific disk (Google Cloud)
$fileUrlGoogle = EasyStorage::url('uploads/myfile.txt', 'google');
```

#### Deleting Files

To delete a file:

```php
// Delete from the default disk
$deleted = EasyStorage::delete('uploads/myfile.txt');

// Delete from a specific disk (S3)
$deletedS3 = EasyStorage::delete('uploads/myfile.txt', 's3');
```

#### Checking File Existence

To verify if a file exists:

```php
// Check existence on the default disk
$exists = EasyStorage::exists('uploads/myfile.txt');

// Check existence on a specific disk (Google Cloud)
$existsGoogle = EasyStorage::exists('uploads/myfile.txt', 'google');
```

#### Managing Metadata

To obtain a file's metadata:

```php
// Get metadata from the default disk
$metadata = EasyStorage::getMetadata('uploads/myfile.txt');

// Get metadata from a specific disk (S3)
$metadataS3 = EasyStorage::getMetadata('uploads/myfile.txt', 's3');
```

#### Listing Files

To list files in a directory:

```php
// List files in the default disk
$files = EasyStorage::listFiles('uploads');

// List files in a specific disk (Google Cloud)
$filesGoogle = EasyStorage::listFiles('uploads', 'google');
```

#### Moving and Copying Files

To move or rename a file:

```php
// Move from the default disk
$success = EasyStorage::move('uploads/myfile.txt', 'uploads/newfile.txt');

// Move from a specific disk (S3)
$successS3 = EasyStorage::move('uploads/myfile.txt', 'uploads/newfile.txt', 's3');
```

To copy a file:

```php
// Copy from the default disk
$copied = EasyStorage::copy('uploads/myfile.txt', 'uploads/myfile_copy.txt');

// Copy from a specific disk (Google Cloud)
$copiedGoogle = EasyStorage::copy('uploads/myfile.txt', 'uploads/myfile_copy.txt', 'google');
```

#### Creating Directories

To create a new directory:

```php
// Create directory on the default disk
$created = EasyStorage::makeDirectory('uploads/new_directory');

// Create directory on a specific disk (S3)
$createdS3 = EasyStorage::makeDirectory('uploads/new_directory', 's3');
```

## Error Handling

The Laravel EasyCloudStorage package offers flexible error handling via two configuration options: `log_errors` and `throw_errors`.

- **`log_errors`**: When enabled, any errors encountered during storage operations will be logged, aiding in monitoring and diagnosing issues without interrupting the user experience.
- **`throw_errors`**: When activated, the package throws exceptions upon errors, allowing developers to implement custom error handling logic for immediate issue resolution.

### Example

To enable both logging and exception throwing during an upload operation, use:

```php
$filePath = EasyStorage::upload($uploadedFile, 'uploads/myfile.txt')
    ->withLog(true)
    ->withError(true);
```

### Security and Stability

This error handling strategy enhances application security and stability by:

- Logging errors without exposing sensitive information, facilitating easier debugging.
- Preventing application crashes due to unsupported operations, allowing for graceful error management.
- Providing configurable settings that empower developers to choose their preferred error handling strategy, aligning with the specific needs of their applications.

By managing errors effectively, the package fosters a more robust and maintainable codebase.

## Driver-Specific Method Availability

It's important to note that not all storage drivers will support every method available in the EasyCloudStorage package. The functionality provided depends exclusively on the capabilities of each specific driver, such as Google Cloud Storage, Amazon S3, and others.

For example, while uploading and deleting files are common operations across most drivers, certain methods like managing metadata or creating directories may not be supported by all drivers.

Rest assured, the EasyCloudStorage package has built-in logic to handle these discrepancies gracefully. If a method is not available for a specific driver, an informative error will be returned, ensuring that you are promptly notified of the limitation. This design allows you to implement your logic without the worry of unexpected failures, promoting a smooth development experience.

## Design Patterns: Facade and Contracts

### Facade

The Facade pattern in Laravel offers a simple and intuitive interface to access classes within the application’s service container. By utilizing the EasyStorage facade, you can invoke methods directly without having to resolve the underlying service each time. This approach not only streamlines your code but also improves readability, allowing you to concentrate on building features rather than managing complex dependencies. The result is a cleaner and more efficient API that enhances your development experience.

### Contracts

Contracts in Laravel define the expected behaviors of services, providing a clear guideline for how they should operate. By utilizing contracts, you ensure that your application can easily switch between different implementations of a service without affecting the code that interacts with it. This flexibility is beneficial for developers, as it allows for easier updates or changes to the underlying logic without requiring significant code alterations.

For users, this means a more reliable and maintainable application. You can confidently integrate new features or optimize existing ones while ensuring that the core functionality remains intact. By adhering to this practice, you also promote a clean architecture that is easier to understand and extend, ultimately enhancing the long-term sustainability of your codebase.

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.
