<?php

return [

        /*
        |--------------------------------------------------------------------------
        | SMS PROVIDER CONFIG
        |--------------------------------------------------------------------------
        |
        |
        */

        'repository_location' => 'ThisPlugin\\SmsServiceProviders\\%sProviderRepository',
        'provider_folder'  => 'SmsServiceProviders',
        'provider_postfix'  => 'ProviderRepository',
        'field_keywords'    => [
                                '*phone*', 
                                '*fone*', 
                                '*foon*', 
                                '*tel*', 
                                '*mobile*',
                                '*gsm*', 
                                '*device*', 
                                '*portable*', 
                                '*handy*',
                                '*contact*',
                              ],
];