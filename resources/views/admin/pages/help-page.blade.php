@extends('admin.layouts.page-container')
<link rel='stylesheet' href="{{$plugin_url}}/assets/admin/css/settings-page.css?ver=1.0.0" media="all" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css">

@section('content')

    <h1 style="color: #ffffff70;font-size: 2rem;font-weight: 600;font-family: 'Open Sans';text-align: center;margin: 1vh 0 6vh 0;">
        Features
    </h1>
    <!-- Tabs for Basic and Pro Versions -->
    <div class="tabs" style="margin-left: 3%;margin-bottom:20px;border-bottom:#8080809c solid 1px;padding-bottom: 2px;">
        <div class="tab active" onclick="switchTab('help')">General help</div>
        <div class="tab" onclick="switchTab('msp')">The Msp page</div>
        <div class="tab" onclick="switchTab('basic')">Basic Version</div>
        <div class="tab" onclick="switchTab('pro')">Pro Version</div>
    </div>

    <!-- General help tab Content -->
    @include('admin.components.help-general-help-tab')
    <!-- Msp tab Content -->
    @include('admin.components.help-msp-tab')
    <!-- Basic Version Content -->
    @include('admin.components.help-basic-version-tab')
    <!-- Pro Version Content -->
    @include('admin.components.help-pro-version-tab')
    <!-- Contact Form Section -->
    @include('admin.components.contact-us')

    <script>
        // Initialize the active tab on page load
        document.addEventListener('DOMContentLoaded', function () {
            switchTab('help'); // Set the default active tab
            
        });

        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
                tab.style.display = 'none';
            });

            // Show the selected tab
            const activeTab = document.getElementById(tabName);
            activeTab.classList.add('active');
            activeTab.style.display = 'block';

            
            // Reinitialize Highlight.js for the active tab
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightBlock(block);
            });
            document.querySelector(`.tab[onclick="switchTab('${tabName}')"]`).classList.add('active');            
        }

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/languages/php.min.js"></script>    
@endsection