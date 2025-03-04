<?php namespace ThisPlugin\Support;

use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\OperatingSystem;
use DeviceDetector\Parser\Device\AbstractDeviceParser;


#-------------------------------------------------------------------------------------------
#                            D E V I C E   T R A C E R
#                           by  MacLeen   v2.0.0-A01-2024
#  -----------------------------------------------------------------------------------------
#               Copyright (C) MacLeen / Start of work 11.09.2024 / 05:50
#-------------------------------------------------------------------------------------------

class DeviceTracer {

    #-------------------------------------

    protected DeviceDetector $device_parser;
    public array $bot_handlers = [];
    
    #--------------------------------------------------
    #
    #--------------------------------------------------
    public function __construct( ) { 
        AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

        $userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse
        $clientHints = ClientHints::factory($_SERVER); // client hints are optional
        
        $this->device_parser = new DeviceDetector($userAgent, $clientHints);
    
    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    public function set_bot_handler( Callable $handler ):self {
        $this->bot_handlers[] = $handler;
        return $this;
    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    public function info( ) : ? array {

        $this->device_parser->parse( );

        if ( $this->device_parser->isBot( )) {

             \array_map( fn( $v ) => $v( $this ), $this->bot_handlers );
             return null;

        } else {

             $os_info = $this->getOs();
             return [                
                'device' => [
                             'client_info'      => $this->device_parser->getClient(), // holds information about browser, feed reader, media player, ...
                             'client_type'      => $this->client_type( ),
                             'browser_family'   => Browser::getBrowserFamily($this->device_parser->getClient('name')),
                             'os_info'          => $os_info,
                             'os_family'        => OperatingSystem::getOsFamily($this->device_parser->getOs('name')),
                             'brand'            => $this->device_parser->getBrandName(),
                             'model'            => $this->device_parser->getModel(),
                           ],
             ];   
        }     
    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    private function getOs(  ) : ? array {

        $os_info = $this->device_parser->getOs();
        return  [
                  'os_name'       => $os_info['name'],
                  'device_details'=> $this->device_type(),
                  'device_type'   => $this->device_parser->getDeviceName(),
                ];   
    }


    private function device_type( ) : string {
        return match ( true ) {
                    $this->device_parser->isSmartphone()            => 'SMART_PHONE',
                    $this->device_parser->isFeaturePhone()          => 'FEATURE_PHONE',
                    $this->device_parser->isTablet()                => 'TABLET',
                    $this->device_parser->isConsole()               => 'CONSOLE',
                    $this->device_parser->isPortableMediaPlayer()   => 'PORTABLE_MEDIA_PLAYER',
                    $this->device_parser->isCarBrowser()            => 'CAR_BROWSER',
                    $this->device_parser->isTV()                    => 'TV',
                    $this->device_parser->isSmartDisplay()          => 'SMART_DISPLAY',
                    $this->device_parser->isSmartSpeaker()          => 'SMART_SPEAKER',
                    $this->device_parser->isCamera()                => 'CAMERA',
                    $this->device_parser->isWearable()              => 'WEARABLE',
                    $this->device_parser->isPeripheral()            => 'PERIPHERAL',
                    default                                         => 'UNKNOWN',
        };
    }



    private function client_type( ) : string {

        return match ( true ) {
                $this->device_parser->isBrowser()                   => 'BROWSER',
                $this->device_parser->isFeedReader()                => 'FEED_READER',
                $this->device_parser->isMobileApp()                 => 'MOBILE_APP',
                $this->device_parser->isPIM()                       => 'PIM',
                $this->device_parser->isLibrary()                   => 'LIBRARY',
                $this->device_parser->isMediaPlayer()               => 'MEDIA_PLAYER',
                default                                             => 'UNKNOWN',
        };
    }        
}