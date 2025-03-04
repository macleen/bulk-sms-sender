<?php

/* Global definitions */
#----------------------------------------------------------------

define('__ROOT_FOLDER__'      , dirname(__FILE__).'/..');
define('__CURRENT_YEAR__'     , date('Y'));
define('__YES_NO__'           , [0 => 'No', 1 => 'Yes']);
define('__YES_NO_LTR__'       , ['F' => 'No', 'T' => 'Yes']);
define('__YES_NO_HTML__'      , ['F' => '<span class="text-red-600">No</span>', 'T' => '<span class="text-green-600">Yes</span>']);
define('__YES_NO_BOOL_HTML__' , [0 => '<span class="text-red-600">No</span>', 1 => '<span class="text-green-600">Yes</span>']);
define('__PLUGIN_PATH__'      , function_exists('plugin_dir_path') ? plugin_dir_path(__FILE__).'../' : '');
define('__PLUGIN_URL__'       , function_exists('plugin_dir_url') ? plugin_dir_url(__FILE__).'../' : '');
define('__APP_LAST_UPDATE__'  , '17-01-2024'); // keep track of changes
#-------------------------------------------------------------------------------------------

/* Global constants */
const __CR__                                = "\r";
const __LF__                                = "\n";
const __CRLF__                              = __CR__.__LF__;
const __HTML_BREAK__                        = '<br/>';
const __EOL__                               = PHP_EOL;
const __UNDEFINED__                         = null;
const __EMPTY_STRING__                      = '';
const __NO__                                = 0;
const __YES__                               = 1;
const __READ_ONLY__                         = 'r';
const __UNSET__                             = 'NOT-SET';
const __ALL__                               = 'ALL';
const __ENV_IS_LOCAL__                      = 'local';
const __ENV_IS_PRODUCTION__                 = 'production';
const __COMMERCIAL_TYPE_PRO__               = 'Pro';
const __COMMERCIAL_TYPE_BASIC__             = 'Basic';
const __WP__                                = 'WP';
#----------------------------------------------------------------

const __BRAND_ID__                          = 'macleen';
const __PLUGIN_ID__                         = 'bulk-sms-sender';
const __USER_ROLE__                         = 'user';
const __VERSION__                           = 'V1.2 2025';

#-------------------------------------------------------------------------------------------
const __ENQUEUE_TYPE_STYLES__               = 'styles';
const __ENQUEUE_TYPE_SCRIPTS__              = 'scripts';
const __ES6_MOD__                           = 'es6_module';

#-------------------------------------------------------------------------------------------
const __SCRIPT_HANDLE_LTR__                 = 'handle';
const __SCRIPT_LOCALIZE_LTR__               = 'localize';

#-------------------------------------------------------------------------------------------
const __USER_TYPE_ADMIN__                   = 'admin';
const __USER_TYPE_USER__                    = 'user';
const __USER_TYPE_GUEST__                   = 'guest';

#-------------------------------------------------------------------------------------------
const __WP_ENV_IS_MISSING_MSG__             = 'Macleen [ Package error ]: WordPress environment is missing...';

#-------------------------------------------------------------------------------------------
const __RESPONSE_SUCCESS_TAG__              = '<b style="color:green;">%s</b>';
const __RESPONSE_ERROR_TAG__                = '<b style="color:red;">%s</b>';
const __PING_BALANCE_AVAILABLE__            = 'ping_balance_available'; 

const __SUCCESS__                           = 'success'; 
const __RESULT__                            = 'result'; 
const __MSG_ID__                            = 'msgID'; 
const __LEAD__                              = 'lead'; 
const __DESCRIPTION__                       = 'description'; 
const __MESSAGE__                           = 'message'; 
const __BODY__                              = 'body'; 
const __STATUS_CODE__                       = 'status_code'; 
const __BALANCE__                           = 'balance'; 
const __PROVIDER__                          = 'provider'; 
const __SMS_STATUS_CODE__                   = 'sms_status_code';


#-------------------------------------------------------------------------------------------
const __MSG_URL_MODE__                      = 'msg_url_mode';
const __MSG_URL_MODE_INDEX__                = 'index';
const __MSG_URL_MODE_SHORTNER__             = 'shortner';


