<div id="msp" class="tab-content active">

    <div class="feature-section">
        <div class="container">
            <br>
            <h1>
                <i class="fas fa-anchor icon"></i> 
                The msp redirection page
            </h1>

            <h3 style="color: #f9850c;font-size: large;padding: 15px 0;">What is the msp page</h3>
            msp stands for MacLeen Shortner Page. To understand what the msp page exactly is please click 
            on the Pro version tab on this page and navigate to Built-in URL Marker, Shortener & Redirector.<br><br>

            <h3 style="color: #f9850c;font-size: large;padding: 15px 0;">Availability</h3>
            The msp redirection page is only available when you select "<strong style="color:yellow;">shortner</strong>" 
            in the "<strong style="color:yellow;">URL Linking Mode</strong>" panel at the beginning of your sending session.<br>
            <div style="text-align:center;margin: 15px 0;">
                <img src="{{$plugin_url}}/assets/admin/img/help/url-mode.png" />
            </div>    
            The message must also contain the __INDEX__ tag for the parser to determin where to place the link, like this for example:<br> 
            <strong style="color:#10f11f">Hi __FULL_NAME__, this is my first test message. click on __INDEX__</strong><br>
            which will be converted into the following:<br> 
            <strong style="color:#0bf090">Hi Jhon, this is my first test message. click on https://yoursite.com/msp/?r=23-1</strong><br><br>
            Example2:<br> 
            <strong style="color:#10f11f">Hi __FULL_NAME__, please click on __INDEX__ to visit my site</strong><br>
            which will be converted into the following:<br> 
            <strong style="color:#0bf090">Hi Jhon, please click on https://yoursite.com/msp/?r=23-1 to visit my site</strong>


            <p>&nbsp</p>
            When someone clicks on your link he will land on https://yourwebsite.com/msp/?r=xx, 
            this page on its turn will redirect him to whatever url you saved under "Redirection to" 
            in the settings page. For the sake of example let us say that the <strong style="color:yellow;">"Redirect to"</strong>
            field has the following page <strong style="color:yellow;"> https://yourwebsite.com/week-7/promotions/product/category/shoes</strong> 
            when the user clicks on the url in the sms message he will land on https://yourwebsite.com/week-7/promotions/product/category/shoes 
            page that was pulled in via a post request wrapping a bunch of data available for grabbing and which looks like this for example:

            <p>                    
                data.dial_code_length: 4<br>
                data.sending_target_country_code: AI<br>
                data.full_country_name: Anguilla<br>
                data.language: EN<br>
                data.recipient: 32465888661<br>
                data.message: Hi Jhon, this is my first test message. click on __INDEX__<br>
                data.email: <br>
                data.full_name: choko1 momo<br>
                data.generic_name: choko1 momo<br>
                data.address: some address<br>
                data.other_info: <br>
                meta_data.time: 08:36:24<br>
                meta_data.date: 26-02-2025<br>
                meta_data.ip: 123.14.15.12<br>
                meta_data.user_agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 etc...<br>
                meta_data.referrer: Direct<br>
                meta_data.prefered-language: en-US,en;q=0.9,nl;q=0.8,fr;q=0.7,de;q=0.6<br>
                meta_data.device.client_info.type: browser<br>
                meta_data.device.client_info.name: Chrome<br>
                meta_data.device.client_info.short_name: CH<br>
                meta_data.device.client_info.version: 133.0.0.0<br>
                meta_data.device.client_info.engine: Blink<br>
                meta_data.device.client_info.engine_version: 133.0.0.0<br>
                meta_data.device.client_info.family: Chrome<br>
                meta_data.device.client_type: BROWSER<br>
                meta_data.device.browser_family: Chrome<br>
                meta_data.device.os_info.os_name: Windows<br>
                meta_data.device.os_info.device_details: UNKNOWN<br>
                meta_data.device.os_info.device_type: desktop<br>
                meta_data.device.os_family: Windows<br>
                meta_data.device.brand: <br>
                meta_data.device.model: <br>
                index: 0-9
            </p>
            In case you are not interested in this info, you can just ignore it,<br>
            but if you are, you can use the following function in you page to get it:

            <h2 style="color:#3b9cf1de;">Dot-Notation to Nested PHP Array</h2>
            @verbatim
            <pre>
                <code class="php" style="background:#020725 !important;color: #a6a883 !important;">
                    &lt;?php

                    // Function to convert dot-notation keys in $_POST into nested arrays
                    function parse_dot_notation_post($postData) {
                        $result = [];
                    
                        foreach ($postData as $key => $value) {
                            $keys = explode('.', $key);
                            $current = &$result;
                    
                            foreach ($keys as $part) {
                                if (!isset($current[$part])) {
                                    $current[$part] = [];
                                }
                                $current = &$current[$part];
                            }
                    
                            $current = $value;
                        }
                    
                        return $result;
                    }
                    
                    // Now you can use that function to parse the $_POST data like this
                    $nestedArray = parse_dot_notation_post($_POST);
                    // Have a look at it ( for debugging )
                    print_r( $nestedArray );
                    
                    // Or output the parsed array in json format if you want ( for debugging )
                    header('Content-Type: application/json');
                    echo json_encode($nestedArray, JSON_PRETTY_PRINT);
                </code>
            </pre>
            @endverbatim

        </div>
    </div>

</div>