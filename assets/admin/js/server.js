class AjaxHttpServer {
    
    static instance;

    constructor( ) { 

        if (AjaxHttpServer.instance) {
            return AjaxHttpServer.instance;
        }
        this.http = new Ajax( );
        AjaxHttpServer.instance = this;

    }

    get_available_providers() {
        this.http.get_available_providers( ).then(function(response) {
            if ( response && response.hasOwnProperty('data')) {            
                 if ( response.data) {
                      var providers = response.data;
                      var options = '<option value="">Select a service provider</option>';
                      if ( providers.length) {
                           for (var i = 0; i < providers.length; i++)
                                options += '<option value="' + providers[i] + '">' + providers[i] + '</option>';
                      }
                      return jQuery('#service_provider').html(options);
                 }    
            }
            return show_alert('Invalid server-response format', 'error');
        }).catch( console.log );
    }

    get_provider_fields( ) {
        this.http.get_provider_fields( ).then( function( response ) {
             if ( response && response.hasOwnProperty('data')) {
                  prepare_provider_fields( response.data );
             } else Promise.reject('Invalid server response');
        }).catch( console.log );
    }

   
    get_account_info( ) {
        return this.http.get_account_info( );
    }

    install_plugin( blob ) {
        return this.http.install_plugin( blob ).then(
             response => {
                (new ProviderTree( config )).show_plugin_tree( '.plugin-tree', true );                
                return response.message;
             });    
    }

    get_plugins_tree( context ) {
        context = context ? context : this;
        return context.http.get_plugins_tree( );
    }

    get_wp_leads( role ){
        return this.http.get_wp_leads( role );
    }

    get_available_log_files( ){
        return this.http.get_available_log_files( );
    }

    get_log_by_date( date ){
        return this.http.get_log_by_date( date );
    }

    delete_log_by_date( date ){
        return this.http.delete_log_by_date( date );
    }

    get_available_analytics_files( ){
        return this.http.get_available_analytics_files( );
    }

    get_analytics_file_by_path( path ){
        console.log( ' requested dump path: ', path);
        return this.http.get_analytics_file_by_path( path );
    }
    delete_analytics_by_path( path ){
        return this.http.delete_analytics_by_path( path );
    }

}