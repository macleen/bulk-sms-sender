const Logging = (function( $ ){

    let data = []; // This will hold the data fetched from the backend
    let currentPage = 1;
    const rowsPerPage = 10;
    let currentSendingDate = ''; // Store the current sending date globally
    const usage_licence = log_arguments.license ? log_arguments.license : 'Basic';
    const tableBody = document.querySelector('#dataTable tbody');
    
    function truncateString(str, maxLength) {
        if (str.length > maxLength) {
            return str.slice(0, maxLength) + '...';
        }
        return str;
    }
    
    function get_available_log_files() {
        config.server.get_available_log_files()
            .then(response => {
                const tableHtml = availableLogFilesToHtmlTable(response.data);
                $('#available_log_files').html(tableHtml);

                // Rebind event listeners after rendering the table
                document.querySelectorAll('.get-log-file-by-date').forEach(row => {
                    row.addEventListener('click', function() {
                        fetchLogData(this.dataset.logdate);
                    });
                });
            });
    }

    function fetchLogData(date) {
        if ( usage_licence == 'Pro') {
             data = '';
             currentPage = 1;
             $('#log_file_content').addClass('hidden');
             config.server.get_log_by_date(date)
                .then(response => {
                    data = cleanAndParseJsonArray(response.data.raw_data);
                    currentSendingDate = date; // Store the current sending date
                    generateTableHeaders();
                    displayData(currentPage, currentSendingDate); // Pass the current sending date
                    $('#log_file_content').removeClass('hidden');
                });
        } else show_alert('This functionality is available in the Pro version only', 'warning');
    }

    function cleanAndParseJsonArray(jsonStrings) {
        return jsonStrings
            .map(str => {
                try {
                    // Remove trailing carriage return and whitespace
                    const cleanedStr = str.trim().replace(/\r$/, '');
                    // Parse JSON
                    return JSON.parse(cleanedStr);
                } catch (error) {
                    // Skip invalid JSON lines
                    return null;
                }
            })
            .filter(item => item !== null); // Remove null entries
    }

    function availableLogFilesToHtmlTable(data) {
        if (data.length) {
            let html = `<table class=""><tbody>`;
            html += `
                    <tr>
                        <th>Date</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th>Total messages</th>
                        <th>Successful messages</th>
                        <th>Failed messages</th>                    
                    </tr>`;

            for (const entry of data) {
                html += `
                            <tr class="row-clickable get-log-file-by-date" data-logdate="${entry.date}">
                                <td>${entry.date}</td>
                                <td>${entry.header.created_at}</td>
                                <td>${entry.header.updated_at}</td>
                                <td>${entry.header.total_number_of_lines}</td>
                                <td>${entry.header.successful_messages}</td>
                                <td>${entry.header.failed_messages}</td>
                            </tr>`;
            }
            html += '</tbody></table>';
            return html;
        } else return '<div class="error">[ 404 ] - No log files were found</div>';
    }

    function jsonToHtmlTable(item) {
        let html = `<table class="table"><tbody>`;
        for (const entry of Object.keys(item)) {
            if (['request','response'].includes( entry )){
                html += `
                        <tr>
                            <td colspan="2"><span style="color:yellow">${entry.toUpperCase( )}</span></td>
                        </tr>
                    `;
                for (const [key, value] of Object.entries(item[entry])) {
                    html += `
                        <tr>
                            <th>${key}</th>
                            <td>${value}</td>
                        </tr>
                    `;
                }
            }    
        }    
        html += `</tbody></table>` ;
        return html;
    }

    // Function to generate table headers dynamically
    function generateTableHeaders() {
        const headerRow = document.getElementById('tableHeader');
        headerRow.innerHTML = ''; // Clear existing headers

        if ( data.length > 0 ) {
             const theaders = ['Provider','SenderID','Name','Recipient','Sent','Balance'];
             theaders.forEach(key => {
                 const th = document.createElement('th');
                 th.innerText = key.replace(/_/g, ' '); // Replace underscores with spaces
                 headerRow.appendChild(th);
             });
        }
    }

    // Function to display data for the current page
    function displayData(page, sending_date) {
        
        tableBody.innerHTML = '';
        document.querySelector('#delete_log_file').dataset.logdate = sending_date;
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedData = data.slice(start, end);

        paginatedData.forEach(item => {
            const row = document.createElement('tr');
            if ( row ) {
                row.classList.add('row-clickable');
                row.addEventListener('click', () => showPopup(item, sending_date));

                if (item.hasOwnProperty('response') && item.hasOwnProperty('request')) {
                    const response = item.response;
                    const request  = item.request;
                    const tdata = {
                        'Provider' : response.hasOwnProperty('provider') ? response.provider : 'NA',
                        'SenderID' : request.hasOwnProperty('sender_id') ? request.sender_id : 'NA',
                        'Name'     : request.hasOwnProperty('full_name') ? truncateString(request.full_name,20) : 'NA',
                        'Recipient': response.hasOwnProperty('lead') ? response.lead : 'NA',
                        'Sent'     : response.hasOwnProperty('result') ? (response.result ? '<span class="success">Success</span>': '<span class="failed">Failed</span>') : 'NA',
                        'Balance'  : response.hasOwnProperty('balance') ? '<span class="warning">'+response.balance+'</span>' : 'NA',
                    }
                    Object.keys(tdata).forEach(key => {
                         const td = document.createElement('td');
                         td.innerHTML = tdata[key]; // Replace underscores with spaces
                         row.appendChild(td);
                    });
                    tableBody.appendChild(row);
                }
            }    
        });

        // Update the sending date in the HTML
        document.getElementById('sending-date').innerText = sending_date || 'N/A'; // Fallback to 'N/A' if undefined
        document.getElementById('pageInfo').innerText = `Page ${page} of ${Math.ceil(data.length / rowsPerPage)}`;
    }

    // Function to show popup with full row data
    function showPopup(item) {
        const popup = document.getElementById('popup');
        const popupData = document.getElementById('popupData');
        popupData.innerHTML = jsonToHtmlTable(item);

        // Add the 'open' class to trigger the animation
        popup.style.display = 'block';
        setTimeout(() => {
            popup.classList.add('open');
        }, 10); // Small delay to allow the display change to take effect
    }

    // Function to close the popup
    function closePopup() {
        const popup = document.getElementById('popup');

        // Add the 'closing' class to trigger the closing animation
        popup.classList.remove('open');
        popup.classList.add('closing');

        // Wait for the animation to finish before hiding the popup
        setTimeout(() => {
            popup.style.display = 'none';
            popup.classList.remove('closing'); // Reset the class for future use
        }, 300); // Match the duration of the CSS transition
    }

    try {
        // Pagination event listeners
        document.getElementById('prevPage').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                displayData(currentPage, currentSendingDate); // Pass the current sending date
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (currentPage < Math.ceil(data.length / rowsPerPage)) {
                currentPage++;
                displayData(currentPage, currentSendingDate); // Pass the current sending date
            }
        });
    } catch ( e ) {}    

    function reset( ){
        tableBody.innerHTML = '';
        $('#log_file_content').addClass('hidden');
        get_available_log_files();
    }
    // Initial setup: Fetch available log files

    return {
        reset: reset,
        closePopup: closePopup,
    }
}( jQuery ))