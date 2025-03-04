const Analytics = (function( $ ){

    let data = []; // This will hold the data fetched from the backend
    let currentPage = 1;
    const rowsPerPage = 10;
    let currentSendingDate = '';
    const usage_licence = analytics_arguments.license ? analytics_arguments.license : 'Basic';
    const tableBody = document.querySelector('#dataTable tbody');
    
    function truncateString(str, maxLength) {
        if (str.length > maxLength) {
            return str.slice(0, maxLength) + '...';
        }
        return str;
    }
    
    
    function get_available_analytics_files() {
        config.server.get_available_analytics_files()
            .then(response => {
                const tableHtml = availableAnalyticsFilesToHtmlTable(response.data);
                $('#available_analytics_files').html(tableHtml);

                // Rebind event listeners after rendering the table
                document.querySelectorAll('.get-analytics-file-by-path').forEach(row => {
                    row.addEventListener('click', function() {
                        fetchAnalyticsData(this.dataset.sendingdate, this.dataset.filepath);
                    });
                });
            });
    }

    function fetchAnalyticsData(sending_date, file_path) {
        if ( usage_licence == 'Pro') {
             data = '';
             currentPage = 1;
             $('#analytics_file_content').addClass('hidden');
             config.server.get_analytics_file_by_path(file_path)
                .then(response => {
                    console.log('Backend response:', response.data);
                    data = response.data;
                    console.log('clean data', data);
                    generateTableHeaders();
                    displayData(currentPage);
                    document.querySelector('#delete_analytics_file').dataset.filepath = file_path;
                    document.querySelector('#sending_date').innerText = sending_date;
                    
                    $('#analytics_file_content').removeClass('hidden');
                });
        } else show_alert('This functionality is available in the Pro version only', 'warning');
    }

    function availableAnalyticsFilesToHtmlTable(data) {
        if (data.length) {
            let locator;
            let html = `<table class=""><tbody>`;
            html += `
                    <tr>
                        <th>Compaign Date</th>
                        <th>Updated at</th>
                        <th>Size</th>
                    </tr>`;

            for (const entry of data) {
                html += `
                            <tr class="row-clickable get-analytics-file-by-path" data-sendingDate="${entry.file}" data-filePath="${entry.path}">
                                <td>${entry.file}</td>
                                <td>${entry.updated_at}</td>
                                <td>${entry.size}</td>
                            </tr>`;
            }
            html += '</tbody></table>';
            return html;
        } else return '<div class="error">[ 404 ] - No data was found</div>';
    }

    function jsonToHtmlTable(item) {
        let html = `<table class="table"><tbody>`;
        for (const entry of Object.keys(item)) {
            if (['data','meta_data'].includes( entry )){
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
             const theaders = ['HashIndex', 'Recipient','Name','Country','Clicked'];
             theaders.forEach(key => {
                 const th = document.createElement('th');
                 th.innerText = key;
                 headerRow.appendChild(th);
             });
        }
    }

    // Function to display data for the current page
    function displayData(page) {
        tableBody.innerHTML = '';
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedData = data.slice(start, end);  // Now 'data' is an array
    
        paginatedData.forEach(item => {
            const row = document.createElement('tr');
            if (row) {
                row.classList.add('row-clickable');
                row.addEventListener('click', () => showPopup(item));
    
                if (item.hasOwnProperty('data') && item.hasOwnProperty('meta_data')) {
                    const lead_data = item.data;
                    const lead_meta_data = item.meta_data;
                    const reacted = Object.entries(lead_meta_data).length ? 'Yes' : 'No';
                    const tdata = {
                        'HashIndex': item.hasOwnProperty('index') ? item.index : 'NA',
                        'Recipient': lead_data.hasOwnProperty('recipient') ? lead_data.recipient : 'NA',
                        'Name': lead_data.hasOwnProperty('full_name') ? truncateString(lead_data.full_name, 20) : 'NA',
                        'Country': lead_data.hasOwnProperty('sending_target_country_code') ? lead_data.sending_target_country_code : 'NA',
                        'Clicked': reacted == 'Yes' ? '<span class="success">Yes</span>' : '<span class="failed">No</span>',
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
                displayData(currentPage, currentSendingDate ); 
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (currentPage < Math.ceil(data.length / rowsPerPage)) {
                currentPage++;
                displayData(currentPage, currentSendingDate ); 
            }
        });
    } catch ( e ) {}    

    function reset( ){
        tableBody.innerHTML = '';
        $('#analytics_file_content').addClass('hidden');
        get_available_analytics_files();
    }

    return {
        reset: reset,
        closePopup: closePopup,
    }
}( jQuery ))