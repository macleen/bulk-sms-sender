<?php

return [

    __USER_TYPE_ADMIN__ => [
        __ENQUEUE_TYPE_STYLES__ =>  [
            [   'id' => 'https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css'],
            [   'id' => 'reset.min.css'         ],
            [   'id' => 'ui.fancytree.min.css'  ],
            [   'id' => 'tabs.css'              ],
            [   'id' => 'flash.css'             ],
            [   'id' => 'reset.min.css'         ],
            [   'id' => 'side-bar.css'          ],
            [   'id' => 'style.css'             ],            
        ],
        __ENQUEUE_TYPE_SCRIPTS__ =>  [
            [   'id' => 'jquery.easing.min.js',  'dependency' => ['jquery']],
            [   'id' => 'jquery-ui.min.js',      'dependency' => ['jquery']],
            [   'id' => 'jquery.fancytree-all-deps.min.js','dependency' => ['jquery-ui.min.js']],
            [   'id' => 'constants.js'           , __SCRIPT_LOCALIZE_LTR__=> [
                                                        'usageParameters'=> [
                                                            'license' => '',
                                                        ],
                                                    ],
                                                 ],   
            [   'id' => 'lwjs.js'                ],
            [   'id' => 'gauge.js'               ],
            [   'id' => 'ajax.js'          , __SCRIPT_LOCALIZE_LTR__=> [
                                                    'ajaxData'=> [
                                                        'ajaxUrl' => '',
                                                        'nonce'   => 'wp_rest',  // for admin --- required
                                                    ],
                                                ],
            ],
            [   'id' => 'server.js'        , 'dependency' => ['ajax.js']],
            [   'id' => 'config.js'        ],
            [   'id' => 'provider_tree.js' ],
            [   'id' => 'packet.js'        ],
            [   'id' => 'flash.js'         ],
            [   'id' => 'index.js'         , 'dependency' => ['config.js']],
            [   'id' => 'utils.js'         ],
            [   'id' => 'jquery-linedtextarea.js'],
            [   'id' => 'jquery-events.js' , 'dependency' => ['index.js'],
                                            __SCRIPT_LOCALIZE_LTR__=> [
                                                    'event_vars' => [
                                                        'copyright_data'=> '',
                                                        'plugin_url'=> ''
                                                    ]
                                                ],
                                            ],    
            [   'id' => 'rxjs.js'        ,  ],
            [   'id' => 'streamer.js'        , 'dependency' => ['rxjs.js']],
            [   'id' => 'mobile_number_validation.js' ],
            [   'id' => 'logging.js'       , 'dependency' => ['config.js'],
                                                __SCRIPT_LOCALIZE_LTR__=> [
                                                    'log_arguments' => [
                                                        'license'=> '',
                                                        'plugin_url'=> ''
                                                    ]
                                               ]],
            [   'id' => 'analytics.js'       , 'dependency' => ['config.js'],
                                               __SCRIPT_LOCALIZE_LTR__=> [
                                                   'analytics_arguments' => [
                                                       'license'=> '',
                                                       'plugin_url'=> ''
                                                   ]
                                              ]],

        ],    
    ],
    __USER_TYPE_USER__ => [
        __ENQUEUE_TYPE_STYLES__ =>  [
            [   'id' => 'flash.css'             ],
        ],
        __ENQUEUE_TYPE_SCRIPTS__ =>  [
            [   'id' => 'flash.js', 'dependency' => ['ajax.js']],
            [   'id' => 'mobile_number_validation.js' ],
        ],    
    ],    
];