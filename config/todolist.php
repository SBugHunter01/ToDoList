<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TodoList Configuration
    |--------------------------------------------------------------------------
    |
    | Config file buat aplikasi ToDo List
    | Sengaja dibikin terpisah supaya mudah diubah-ubah settingnya
    | tanpa harus edit code satu-satu
    |
    */


    // Pagination Configuration
    'pagination' => [
        'tasks_per_page' => env('TODOLIST_TASKS_PER_PAGE', 10),
        'users_per_admin_page' => env('TODOLIST_USERS_PER_PAGE', 20),
        'recent_activities_limit' => env('TODOLIST_RECENT_ACTIVITIES', 10),
    ],

    // Default Values
    'defaults' => [
        'category_color' => '#3B82F6',
        'user_role' => 'user',
        'admin_role' => 'admin',
        'default_password' => env('TODOLIST_DEFAULT_PASSWORD', 'password'),
    ],

    // Auto-refresh Configuration
    'refresh' => [
        'admin_dashboard_interval' => env('TODOLIST_ADMIN_REFRESH_INTERVAL', 30000), // 30 seconds
        'alert_display_time' => env('TODOLIST_ALERT_DISPLAY_TIME', 5000), // 5 seconds
    ],

    // Default Users Configuration
    'default_users' => [
        'admin' => [
            'name' => 'Admin User',
            'email' => 'admin@todolist.com',
            'role' => 'admin',
        ],
        'demo' => [
            'name' => 'Demo User', 
            'email' => 'demo@todolist.com',
            'role' => 'user',
        ],
    ],
];
