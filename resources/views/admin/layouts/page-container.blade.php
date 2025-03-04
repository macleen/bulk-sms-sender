<div class="wrap" style="background-color:#181a29;">

    <div class="viewable-area" style="background-color: transparent;">

        <div class="wrap" style="padding:10px;margin-bottom:5vh;">

            <div class="preloader-wrapper package-loader hidden">
                <div class="preloader-inner">
                    <img src="{{$plugin_url}}/assets/admin/img/u__loader.gif" class="loading-bar" style="width:70px;"/>
                </div>
            </div>   

            <div class="settings-page-header" style="margin-bottom:10px;">
                <div class="" style="display:flex; justify-content: center;">
                    <div class="macleen-logo-container" style="width: 6%;height: 6%;border: #80808073 1px solid;border-radius: 5px;padding-right: 5px;background-color: #ffffff45;">
                        <img src="{{$plugin_url}}/assets/admin/img/macleen.png" class="" />
                    </div>
                    <div style="text-align: left;padding-left:1%;">
                        <div class="author" style="color: #ffffffa6;margin-top: 0;padding: 1.2rem 1rem 1rem 1rem;font-size: 1rem;line-height: 1.2rem;height: fit-content;border: 1px #eadede63 solid;border-radius: 5px;background-color: #5456614a;font-family: 'dashicons';">
                            {{ config('app.name')}}
                            {{ config('app.version')}}<br>
                            Licence: 
                            <span class="{{ config('app.commercial_usage') }}-licence">{{ config('app.commercial_usage') }}</span>
                        </div>  
                        @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
                            @include('admin.components.get-the-pro-version-header')
                        @endif    
                    </div>    
                </div>    
            </div>

            @yield('content')
                
        </div>
    </div>
</div>