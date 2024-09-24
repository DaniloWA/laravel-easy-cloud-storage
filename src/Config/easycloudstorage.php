<?php

return [
    /*
    |---------------------------------------------------------------------------
    | Default Disk
    |---------------------------------------------------------------------------
    |
    | Specify the default disk to be used for storage operations.
    | This can be set to any of the disks defined in the 'disks' array.
    |
    */
    'default' => 'local',

    /*
    |---------------------------------------------------------------------------
    | Log Errors
    |---------------------------------------------------------------------------
    |
    | Determine if errors should be logged. By default, this is set to
    | false, meaning errors will not be logged unless explicitly enabled.
    |
    */
    'log_errors' => false,

    /*
    |---------------------------------------------------------------------------
    | Throw Errors
    |---------------------------------------------------------------------------
    |
    | Determine if exceptions should be thrown. By default, this is set to
    | false, meaning exceptions will not be thrown unless explicitly enabled.
    |
    */
    'throw_errors' => false,

    /*
    |---------------------------------------------------------------------------
    | Disks Configuration
    |---------------------------------------------------------------------------
    |
    | Array of available storage disks, each defined with specific
    | configuration options. You can add more disks as needed.
    |
    */
    'disks' => [

        /*
        |---------------------------------------------------------------------------
        | Local Disk
        |---------------------------------------------------------------------------
        |
        | Configuration for the 'local' disk, which uses the local file system.
        |
        */
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        /*
        |---------------------------------------------------------------------------
        | S3 Disk
        |---------------------------------------------------------------------------
        |
        | Configuration for the 's3' disk, which connects to Amazon S3 storage.
        |
        */
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        /*
        |---------------------------------------------------------------------------
        | Google Disk
        |---------------------------------------------------------------------------
        |
        | Configuration for the 'google' disk, which connects to Google Cloud Storage.
        |
        */
        'google' => [
            'driver' => 'gcs',
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'key_file' => env('GOOGLE_CLOUD_KEY_FILE'),
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
            'url' => env('GOOGLE_CLOUD_URL'),
        ],

        // Additional providers can be added here as needed.
    ],
];
