<?php

return [
    [
        'page_title'     => 'Bulk Sms Sender',
        'menu_title'     => 'Sms Sender',
        'position'       => 25,
        'submenus'   => [
            [
                'page_title' => 'Sending session',
                'menu_title' => 'Start sending',
                'callback'   => 'show_sms_panels',
            ],
            [
                'page_title' => 'Sender Settings',
                'menu_title' => 'Sender Settings',
                'callback'   => 'show_settings_page',
            ],
            [
                'page_title' => 'Sender Settings',
                'menu_title' => 'Sync DB',
                'callback'   => 'show_db_field_sync_page',
            ],
            [
                'page_title' => 'Send Log',
                'menu_title' => 'Sms Log',
                'callback'   => 'show_logging_page',
            ],
            [
                'page_title' => 'Analytics',
                'menu_title' => 'Analytics',
                'callback'   => 'show_analytics_log',
            ],
            [
                'page_title' => 'Help docs',
                'menu_title' => 'Documentation',
                'callback'   => 'show_help_page',
            ],            
        ],
    ],
];