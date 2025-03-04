<div class="wrap">
    <div class="viewable-area">
        <!-- multistep form -->
        <div class="sidebar">
            <ul>
            <li><a href="#" class="stage-link provider-settings-stage" ><i class="stage-btn bx bx-globe-alt" data-target="#provider-settings" data-page="provider-settings" title="Provider settings"></i></a></li>
            <li><a href="#" class="stage-link local-settings-stage" ><i class="stage-btn bx bx-cog" data-target="#local-settings" data-page="local-settings" title="Local settings"></i></a></li>
            <li><a href="#" class="stage-link messages-stage" ><i class="stage-btn bx bx-comment-detail" data-target="#messages" data-page="messages" title="Messages"></i></a></li>
            <li><a href="#" class="stage-link indexes-stage" ><i class="stage-btn bx bx-library" data-target="#indexes" data-page="indexes" title="Indexes"></i></a></li>
            <li><a href="#" class="stage-link leads-stage" ><i class="stage-btn bx bx-street-view" data-target="#leads" data-page="leads" title="Leads"></i></a></li>
            <li><a href="#" class="stage-link tags-settings-stage" ><i class="stage-btn bx bx-customize" data-target="#tags-settings" data-page="tags-settings" title="Message line format"></i></a></li>        
            <li><a href="#" class="stage-link preview-stage" ><i class="stage-btn bx bx-paper-plane" data-target="#preview" data-page="preview" title="Review"></i></a></li>
            </ul>
        </div>

        <div class="btn">
            <span class="bar1"></span>
            <span class="bar2"></span>
            <span class="bar3"></span>
        </div>

        <form id="msform" action="javascript:void(0);">
            <!-- progressbar -->
            <div class="author">
                {{ config('app.name') }} - 
                {{ config('app.short_version') }} - Licence: 
                {{ config('app.commercial_usage') }}
            </div>   
                
            <ul id="progressbar">
                <li class="active">Provider</li>
                <li>Settings</li>
                <li>Config</li>
                <li>Messages</li>
                <li>Index</li>
                <li>Leads</li>
                <li>Format</li>
                <li>Review</li>
                <li>Sending</li>
            </ul>

            @yield('content')
                
        </form>       
   
    </div>    
</div>    