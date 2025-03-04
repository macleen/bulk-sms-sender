@extends('admin.layouts.page-container')
<link rel="stylesheet" href="{{$plugin_url}}/assets/admin/css/logging-page.css" media="all" />
@section('content')
    <p>
        <div style="padding-left:1vw;padding-top:2vh;">
            <div>
                <h1 style="color: #b5cfe59c;font-weight: 600;font-family: sans-serif;font-size: 1.6rem;padding-bottom: 10px;">
                    <span class="dashicons dashicons-clipboard" style="font-size: 1.7rem;"></span>&nbsp;
                    Logging
                </h1>
            </div>
            <div id="available_log_files">
                Waiting for the data collector...
            </div>
            <div id="log_file_content" class="hidden" style="padding-left:1vw;margin-top: 3vw;">
                <h1 class="file_name">
                    Sending date:&nbsp;
                    <span id="sending-date"></span>   
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
                    <button id="delete_log_file" data-logDate="" style="background-color:#e82929b5;font-weight:600;border:#928080 thin solid;">Delete this log</button>
                    <div>
                        <button id="prevPage">Previous</button>
                        <span id="pageInfo"></span>
                        <button id="nextPage">Next</button>
                    </div>    
                </div>

                <!-- Popup for displaying full row data -->
                <div id="popup" class="popup">
                    <div class="popup-content">
                        <span class="popup-close" onclick="Logging.closePopup()">&times;</span>
                        <pre id="popupData"></pre>
                    </div>
                </div>
            </div>
        </div>    
    </p>    
    <script>
        document.addEventListener('DOMContentLoaded', ()=> Logging.reset( ));
   </script>
@endsection
