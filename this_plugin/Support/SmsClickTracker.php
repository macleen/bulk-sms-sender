<?php namespace ThisPlugin\Support;

use App\Support\Tools;
use ThisPlugin\Support\DeviceTracer;


class SmsClickTracker {




    public static function capture_click_data() {
        return array_merge([
                 'time'              => date('H:i:s'),
                 'date'              => date('d-m-Y'),
                 'ip'                => Tools::ip( ),
                 'user_agent'        => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                 'referrer'          => $_SERVER['HTTP_REFERER'] ?? 'Direct',
                 'prefered-language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'Unknown',
               ], self::device_info( ));

    }

    protected static function device_info( ): array {
        $device = (new DeviceTracer( ))->info( );
        return empty( $device )
             ? ['device' => 'BOT: click performed by a web-bot. No info is gathered.']
             : Tools::flatten_array_withKeys( $device );
    }
 

}