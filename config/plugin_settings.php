<?php

return [
    'groups'     => [
        [
            'group_name'  => 'sms_bulk_sender_settings_group',
            'group_settings'   => [
                'redirect_to'        => 'https://your-index-url.com',
                'shortner_page_name' => 'msp',    // key_name => default  value
                'api_key'            => '',
                'keep_logs'          => '1',         // key_name => default  value
            ],
        ],    
    ],
];