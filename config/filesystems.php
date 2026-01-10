<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // AWS S3
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        // DigitalOcean Spaces
        'digitalocean' => [
            'driver' => 's3',
            'key' => env('DO_SPACES_KEY'),
            'secret' => env('DO_SPACES_SECRET'),
            'region' => env('DO_SPACES_REGION', 'nyc3'),
            'bucket' => env('DO_SPACES_BUCKET'),
            'endpoint' => env('DO_SPACES_ENDPOINT', 'https://nyc3.digitaloceanspaces.com'),
            'url' => env('DO_SPACES_URL'),
            'use_path_style_endpoint' => false,
            'throw' => false,
        ],

        // Wasabi Cloud Storage
        'wasabi' => [
            'driver' => 's3',
            'key' => env('WASABI_ACCESS_KEY_ID'),
            'secret' => env('WASABI_SECRET_ACCESS_KEY'),
            'region' => env('WASABI_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('WASABI_BUCKET'),
            'endpoint' => env('WASABI_ENDPOINT', 'https://s3.wasabisys.com'),
            'use_path_style_endpoint' => false,
            'throw' => false,
        ],

        // Backblaze B2
        'backblaze' => [
            'driver' => 's3',
            'key' => env('BACKBLAZE_KEY_ID'),
            'secret' => env('BACKBLAZE_APPLICATION_KEY'),
            'region' => env('BACKBLAZE_REGION', 'us-west-002'),
            'bucket' => env('BACKBLAZE_BUCKET'),
            'endpoint' => env('BACKBLAZE_ENDPOINT'),
            'use_path_style_endpoint' => false,
            'throw' => false,
        ],

        // Google Cloud Storage
        'gcs' => [
            'driver' => 'gcs',
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'key_file' => env('GOOGLE_CLOUD_KEY_FILE'), // Path to JSON key file
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
            'path_prefix' => env('GOOGLE_CLOUD_STORAGE_PATH_PREFIX', ''),
            'storage_api_uri' => env('GOOGLE_CLOUD_STORAGE_API_URI'),
            'visibility' => 'public',
            'throw' => false,
        ],

        // Cloudflare R2
        'r2' => [
            'driver' => 's3',
            'key' => env('R2_ACCESS_KEY_ID'),
            'secret' => env('R2_SECRET_ACCESS_KEY'),
            'region' => env('R2_DEFAULT_REGION', 'auto'),
            'bucket' => env('R2_BUCKET'),
            'endpoint' => env('R2_ENDPOINT'),
            'use_path_style_endpoint' => false,
            'throw' => false,
        ],

        // MinIO (Self-hosted S3-compatible)
        'minio' => [
            'driver' => 's3',
            'key' => env('MINIO_ACCESS_KEY_ID'),
            'secret' => env('MINIO_SECRET_ACCESS_KEY'),
            'region' => env('MINIO_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('MINIO_BUCKET'),
            'endpoint' => env('MINIO_ENDPOINT', 'http://localhost:9000'),
            'use_path_style_endpoint' => true,
            'throw' => false,
        ],

        // Linode Object Storage
        'linode' => [
            'driver' => 's3',
            'key' => env('LINODE_ACCESS_KEY_ID'),
            'secret' => env('LINODE_SECRET_ACCESS_KEY'),
            'region' => env('LINODE_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('LINODE_BUCKET'),
            'endpoint' => env('LINODE_ENDPOINT'),
            'use_path_style_endpoint' => false,
            'throw' => false,
        ],

        // Vultr Object Storage
        'vultr' => [
            'driver' => 's3',
            'key' => env('VULTR_ACCESS_KEY_ID'),
            'secret' => env('VULTR_SECRET_ACCESS_KEY'),
            'region' => env('VULTR_DEFAULT_REGION', 'ewr1'),
            'bucket' => env('VULTR_BUCKET'),
            'endpoint' => env('VULTR_ENDPOINT'),
            'use_path_style_endpoint' => false,
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
