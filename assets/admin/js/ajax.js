class Ajax {
    static instance;
    static RETHROW_ERRORS = false;

    constructor() {
        if (Ajax.instance) {
            return Ajax.instance;
        }
        Ajax.instance = this;
    }

    get_json_error( e ) {
        return e.responseJSON 
             ? `${e.responseJSON.message} - status: ${e.status}` : false;
    }    

    get_text_error( e ) {
        return e.statusText
             ? `${e.statusText} - status: ${e.status}` : false;
    }    


    simplifyError(e) {
        console.error( 'simplifyError =>>>> ', e );
        const JSON_error = this.get_json_error( e );     
        const Text_error = this.get_text_error( e );     
        const error = JSON_error ? JSON_error : Text_error;
        if ( error ) show_alert( error, 'error' );
    }


    filtered(content) {
        console.log("============================== ");
        console.log("Response: ");
        console.log(content);
        console.log("============================== ");
        return content;
    }


    showLoader() {
        document.querySelectorAll(".package-loader").forEach(el => el.classList.remove("hidden"));
    }

    hideLoader() {
        document.querySelectorAll(".package-loader").forEach(el => el.classList.add("hidden"));
    }

    show_sending_loader() {
        jQuery(".loader-container").removeClass(function(){
            var soundTrack = document.getElementById("sound-track");    
            soundTrack.loop = !!1;
            config.get_entry('sound_is_on') ? soundTrack.play( ) : void(0);
            return "hidden";
        });
    }

    hide_sending_loader() {
        jQuery(".loader-container").addClass(function(){
            var soundTrack = document.getElementById("sound-track");    
            soundTrack.pause( );
            return "hidden";
        });
    }    

    sendToServer(options, isDownload = false) {

        console.log('/////////////////////////////////////////');
        console.log('relative url', options.url);
        console.log('ajax url', ajaxData.ajaxUrl + "/api");
        options.url = ".." + ajaxData.ajaxUrl + "/api" + options.url;
        console.log('final url', options.url);
        console.log('/////////////////////////////////////////');
        let settings = {
            url: "",
            method: "GET",
            data: "",
            async: true,
            crossDomain: true,
            timeout: 30000, // 10 seconds timeout
            dataType: "JSON",
            headers: { "Content-Type": "application/json; charset=UTF-8" },
            beforeSend: this.showLoader,
            ...options,
        };

        let isFormData = settings.data instanceof FormData; // Check if data is FormData
        if (isDownload) {
            settings.dataType = null;
            settings.xhrFields = { responseType: "blob" };
        } else if ( isFormData ) {
            settings.processData = false; // Prevent jQuery from processing FormData
            settings.contentType = false;
        }

        console.log('options:', settings);

        return new Promise((resolve, reject) => {
            jQuery.ajax(settings)
            .done((data, status, xhr) => {
                if (!isDownload) {
                    resolve(this.filtered(data));
                } else {
                    const disposition = xhr.getResponseHeader("Content-Disposition");
                    const filename = disposition
                        ? disposition.split("filename=")[1]?.replace(/"/g, "")
                        : "download.pdf";

                    const blob = new Blob([data], { type: "application/pdf" });
                    const link = document.createElement("a");
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();
                    window.URL.revokeObjectURL(link.href);

                    resolve(blob);
                }
            })
            .fail((jqXHR) => {this.simplifyError(jqXHR); reject()})
            .always(() => this.hideLoader());
        });
    }

    get(url, options = {}, isDownload = false) {
        return this.sendToServer({ ...options, url, method: "GET" }, isDownload);
    }

    post(url, data, options = {}, isDownload = false) {
        return this.sendToServer(
            {
                ...options,
                url,
                method: "POST",
                headers: ajaxData.nonce ? { "X-WP-Nonce": ajaxData.nonce } : {},
                data,
            },
            isDownload
        );
    }

    send_message(packet) {
        return this.post(`/${config.get_entry("provider")}/send`, packet);
    }

    get_available_providers() {
        return this.get("/providers");
    }

    get_provider_fields() {
        return this.get(`/${config.get_entry("provider")}/fields`);
    }

    get_balance(providerSettings) {
        return this.post(`/${config.get_entry("provider")}/get_balance`, providerSettings);
    }

    get_account_info() {
        return this.post(`/${config.get_entry("provider")}/account/info`, config.get_entry("settings"));
    }
    install_plugin( form_data ) {
        return this.post(`/install/plugin`, form_data);
    }

    get_plugins_tree( ) {
        return this.get(`/installed/plugins`);
    }

    get_wp_leads( role ){
        return this.get(`/users/${role}`);
    }


    get_available_log_files(  ){
        return this.get(`/log/all`);
    }

    get_log_by_date( date ){
        return this.get(`/log/date/${date}`);
    }

    delete_log_by_date( date ){
        return this.get(`/log/delete/${date}`);
    }


    get_available_analytics_files(  ){
        return this.get(`/analytics/all`);
    }

    get_analytics_file_by_path( path ){
        return this.post(`/analytics/file`, { path: path });
    }

    delete_analytics_by_path( path ){
        return this.post(`/analytics/delete`, { path: path });
    }

}