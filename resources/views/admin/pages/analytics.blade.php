@extends('admin.layouts.page-container')
<link rel='stylesheet' href="{{$plugin_url}}/assets/admin/css/logging-page.css?ver=1.0.0" media="all" />
@section('content')
    <p>
        <div style="padding-left:1vw;padding-top:2vh;">
            <div>
                <h1 style="color: #b5cfe59c;font-weight: 600;font-family: sans-serif;font-size: 1.6rem;padding-bottom: 10px;">
                    <span class="dashicons dashicons-visibility" style="font-size: 1.7rem;"></span>&nbsp;
                    Analytics
                </h1>
            </div>
            <div id="available_analytics_files">
                Waiting for the data collector...
            </div>
            <div id="analytics_file_content" class="hidden" style="padding-left:1vw;margin-top: 3vw;">
                <h1 class="file_name">
                    Sending Date:&nbsp;
                    <span id="sending_date"></span>   
                </h1>
                <table id="dataTable">
                    <thead>
                        <tr id="tableHeader">
                            <!-- Headers will be inserted here by JavaScript -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be inserted here by JavaScript -->
                    </tbody>
                </table>
                <div class="pagination">                      
                    <button id="delete_analytics_file" data-filePath="" style="background-color:#e82929b5;font-weight:600;border:#928080 thin solid;">Delete</button>
                    <div>
                        <button id="prevPage">Previous</button>
                        <span id="pageInfo"></span>
                        <button id="nextPage">Next</button>
                    </div>    
                </div>

                <!-- Popup for displaying full row data -->
                <div id="popup" class="popup">
                    <div class="popup-content">
                        <span class="popup-close" onclick="Analytics.closePopup()">&times;</span>
                        <pre id="popupData"></pre>
                    </div>
                </div>
            </div>
        </div>    
    </p>    
    <script>
        document.addEventListener('DOMContentLoaded', ()=> Analytics.reset( ));
   </script>
@endsection
