<?php

return [
    'event_base_path' => getenv('EVENT_BASE_PATH') ?: __DIR__ . '/../var/events',
    'database' => [
        'dsn' => getenv('DATABASE_DSN') ?: 'mysql:host=127.0.0.1;dbname=Informes;charset=utf8mb4',
        'user' => getenv('DATABASE_USER') ?: 'root',
        'password' => getenv('DATABASE_PASSWORD') ?: '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],
];
