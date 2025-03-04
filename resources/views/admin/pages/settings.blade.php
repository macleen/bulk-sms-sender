@extends('admin.layouts.page-container')
@section('content')

    <ul class="macleen-tabs-box">
        <li class="macleen-tab-box active"><span class="macleen-box-span">Sender Settings</span>
            <div class="macleen-tab-box-detail">
                <h2 style="color:#cac0c0;">Sms Bulk Sender Settings</h2>
                <form method="post" action="{{ esc_attr(admin_url('options.php')) }}">
                        
                    {!! $settings_fields_html !!}
                    {!! $settings_sections_html !!}

                    <table class="form-table wp-list-table widefat fixed striped settings_table settings-form-table">
                        <tr>
                            <th>
                                <label for="bot_name">Shortner page</label>
                                @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
                                    <small class="pro-version-availability-notice">* Pro version only</small>
                                @endif    
                            </th>
                            <td>
                                <input type="text" name="__shortner_page_name" id="shortner_page_name" value="{{ esc_attr(get_option('shortner_page_name', '')) }}" class="regular-text" placeholder="optional" disabled style="background-color: #a9ada880;color:#e3d7d7;">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="bot_name">Redirection to</label>
                                @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
                                    <small class="pro-version-availability-notice">* Pro version only</small>
                                @endif    
                            </th>
                            <td>
                                <input type="text" name="redirect_to" id="redirect_to" value="{{ esc_attr(get_option('redirect_to', '')) }}" class="regular-text" placeholder="optional" {{ $disabled }} style="background-color: {{ $input_bg_color }};color:{{ $input_fg_color }};">
                            </td>
                        </tr>
                        <tr>
                            <th><label for="api_key">Bulk Sender API Key</label></th>
                            <td><input type="text" name="api_key" id="api_key" value="{{ esc_attr(get_option('api_key', '')) }}" class="regular-text" style="background-color: {{ $input_bg_color }};color: {{ $input_fg_color }};" {{ $disabled }}></td>
                        </tr>
                        <tr>
                            <th>
                                <label for="is_visible">Keep Logs</label>
                                @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
                                    <small class="pro-version-availability-notice">* Pro version only</small>
                                @endif    
                            </th>
                            <td>
                                <input type="checkbox" name="keep_logs" id="keep_logs" value="1" {{ checked(get_option('keep_logs', true), true) }} {{ $disabled }} style="width:10px !important;background-color: {{ $input_fg_color }};">
                                <label for="is_visible">Save Send Log</label>                                
                            </td>
                        </tr>
                    </table>

                    {!! $submit_button_html !!}

                </form>
            </div>
        </li>


        <li id="provider-tab" class="macleen-tab-box"><span class="macleen-box-span">Providers</span>
        
            <div class="macleen-tab-box-detail">
            <h2 style="color:#faf4f4;">Install a new SMS provider</h2>
            <small style="font-size:10px;">Or check which providers you already have installed</small>
            <form class="settings-form" style="margin-top: 5px;">

                <div class="" style="width: 100%;">
                    <div style="display: flex; justify-content:space-between;">
                        <div class="scrollbar" style="max-height:38vh;min-height:38vh;overflow-x: hidden;width:70%;">
                            <div class="widget bg-dark" style="margin-bottom:15px;text-align:left;">
                                <h4 class="widget-title d-inline-block">
                                    Provider-List
                                </h4>
                            </div>
                            <div class="plugin-tree"></div>
                        </div>
                        <div style="display:flex;justify-content:center;flex-direction:column;width:30%;">
                            Selected Plugin: 
                            <div id="macleen-selected-purchasable-plugin-show" style="padding:8px 0;font-weight:bold;color:#10dba4;">    
                                none
                            </div>
                            <button id="macleen-selected-purchasable-plugin-purchase" type="button" class="button button-primary" data-selected-plugin="" style="width: 90%;margin-left:12px;">Show me this</button>
                        </div>    
                    </div>
                    <div style="margin-top:1.5rem;border-top: gray 1px solid;padding-top:4px;">
                        Already installed:&nbsp; <span id="macleen-installed-plugins" style="font-size:1.5rem;color:#10dba4; font-weight: bold;">0</span> &nbsp;|&nbsp; 
                        Available:&nbsp; <span id="macleen-purchasable-plugins" style="font-size:1.5rem;color:#ec9718; font-weight: bold;">0</span>
                    </div>
                </div>
                
            </form>
            </div>
        </li>



        <li class="macleen-tab-box"><span class="macleen-box-span">Install</span>
            <div class="macleen-tab-box-detail">
                <div class="help-page">  
                    <h2 style="color:#faf4f4;">Extending your license or have a plugin file ready? click to install it</h2>
                    <p style="color:#cac0c0;">
                    If you already have purchased the pro version or a new sms provider plugin, 
                    you should have received or downloaded a file starting with the provider name 
                    ( if it is a an SMS-Provider plugin ) or having the name "macBSS" 
                    ( if you are extending your license ), in both cases the file will have the .zip extension.
                    Juts click on the install button here under to install it.<br><br>
                    <small style="font-size:10px; color:red; border: gray thin solid;border-left: rgb(52, 49, 49) 4px solid;padding:1px 3px 1px 5px;color:white;background-color:rgb(244, 76, 76);"> NB </small>&nbsp;
                    In case uou have purchased an SMS-provider plugin and the provider already exists in your repository, the old provider data will be overwritten. Which is actually the point here, but in case you prefer to keep it for some reason, 
                    you can backup the repo or the full plugin before starting the installation.<br> 
                    </p>    
                    <div class="" style="margin-top:2rem;">
                        <form class="settings-form" style="text-align:center;margin-top:1rem;display:flex;align-items:center;justify-content:center;padding:10px 0 10px 15px;background-color:transparent;">
                            <input  id="zip-file-input" type="file" id="zip-file-input" accept=".zip" style="display: none;">
                            <button id="upload-new-plugin-btn" type="button" class="button button-primary" style="width: 100%;">Install Package</button>
                            <p id="upload-status"></p>
                        </form>    
                    </div>
                </div>    
            </div>     
        </li>
        
        <li class="macleen-tab-box"><span class="macleen-box-span">Features</span>
            <div class="macleen-tab-box-detail">
            
            <div class="help-page scrollbar" style="max-height: 100% !important;">
                <h2 style="color:#e0d6d6;">Options & Features</h2>
                <p style="color:#aea5a5;">Our plugin comes in two versions: <strong>Basic</strong> and <strong>Pro</strong>.</p>
        
                <div class="license basic">
                    <h2>Basic Version</h2>
                    <p style="color:#161515;">Includes two free SMS provider plugins, allowing you to send messages worldwide.</p>
                    <h3>Features:</h3>
                    <ul>
                        <li>✔️ Lifetime license with unlimited use</li>
                        <li>✔️ Adjustable delays between messages</li>
                        <li>✔️ View provider account details without logging in (*)</li>
                        <li>✔️ Instant balance updates after each message (*)</li>
                        <li>✔️ Powerful message enqueuer using RxJS reactive streams</li>
                        <li>✔️ Pause and resume sending process anytime</li>
                        <li>✔️ Supports input lists of any format</li>
                        <li>✔️ Separates successful and failed message results</li>
                        <li>✔️ One-click installation for new provider plugins</li>
                        <li>✔️ Predefined variables for dynamic message content</li>
                    </ul>
                    <span style="font-size:11px; font-weight:500;font-style: italic;padding-top:10px;">(*): If natively supported by the provider API</span>
                </div>
        
                <div class="license pro">
                    <h2>Pro Version</h2>
                    <p>Includes all features of the Basic version, plus advanced messaging options.</p>
                    <h3>Additional Features:</h3>
                    <ul>
                        <li>🚀 Anchored messages using multiple indexing hosts</li>
                        <li>🚀 4 messages / 4-cycle-round-robin rotations</li>
                        <li>🚀 Rotating indexing hosts with configurable cycles</li>
                        <li>🚀 Built-in URL shortener reducing message costs</li>
                        <li>🚀 Shortener supports language-based redirection</li>
                        <li>🚀 Free updates for future releases</li>
                        <li>🚀 One year of free support</li>
                    </ul>
                </div>
        
                <p style="color:#c5b7b7;">For more details, please check the <a href="{{$help_page_link}}">documentation</a> for usage tips.</p>
            </div>

            </div>
        </li>
    </ul>
       
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var getslide = jQuery(".macleen-tabs-box li").length - 1;
            var slidecal = 30 / getslide + "%";

            jQuery(".macleen-tab-box").css({ width: slidecal });

            jQuery(".macleen-tab-box").click(function () {
                jQuery(".macleen-tab-box").removeClass("active");
                jQuery(this).addClass("active");
            });
            setTimeout(()=> (new ProviderTree( config )).show_plugin_tree( '.plugin-tree' ), 0);
        });        
    </script>

@endsection