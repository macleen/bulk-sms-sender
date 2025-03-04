class Config {
    static instance;

    constructor( default_delay, settings_prefix, default_line_format ) {

        if (Config.instance) {
            return Config.instance;
        }

        this.ui                = this.#init_ui( );
        this.pages             = this.#init_pages( ); 
        this.service_provider  = this.#init_service_provider( settings_prefix );
        this.processing        = this.#init_processing( default_line_format, default_delay );
        this.server            = new AjaxHttpServer( );
        this.lw                = new LwJs( );
        Config.instance        = this;

    }

    #init_ui( ){
        return {
            sound_is_on: false,
          }
    }

    #init_service_provider( settings_prefix ) {
        return {            
            provider:'',
            add_ons: {},
            settings_prefix: settings_prefix,
            settings: {}, 
            fields: {},
        }
    }    


    #init_pages( ) {
        return {            
            'provider': false,
            'provider-settings': false,
            'local-settings': false,
            'messages': false,
            'indexes': false,
            'leads': false,
            'tags-settings': false,
            'preview': false,
        }
    }    

    is_accessible_by_jump( page ) {
        let pages = Object.keys( this.pages );
        let page_index = pages.indexOf( page );
        if ( page_index > 0 ) {
             let previous_page = page_index - 1;
             return this.pages[ pages[ previous_page ]];
        }
        return true;
       
    }    
    
    set_page_accessibility_by_jump( page, is_accessible ){
        if ( page in this.pages )
             this.pages[ page ] = is_accessible;
    }


    reset_all_pages_accessibility( ){
        for( let page in this.pages )
             this.pages[ page ] = false;
    }

    #init_processing( default_line_format, default_delay ) {
        return { 
                sender_id: '',
                delay: default_delay,
                total_leads     : 0,
                total_sent      : 0,
                total_messages: 0,
                total_indexes: 0,
                index_roll_in: 1,
                index_sending_counter: 0,
                use_shortner_code_as_route_arg: 'NO',
                msg_url_mode: '',
                message_container: [],
                index_container: [],
                current_msg_pointer: -1,
                current_index_pointer: 0,
                name_format: 'FullName',
                ndx_with_packet_id: false,
                language: 'EN',
                random_string_length: 10,
                line_format: default_line_format,
                dial_code_length: '',
                sending_target_country_code: '',
                full_country_name: '',
                remove_successful_items: true,                
              }
    }
    
    set_entry( k, v ) {
        if ( this.ui.hasOwnProperty( k ))
             this.ui[k] = v;
        else if ( this.processing.hasOwnProperty( k ))     
             this.processing[k] = v;
        else if ( this.service_provider.hasOwnProperty( k ))     
             this.service_provider[k] = v;

    }


    get_entry( k ) {
        if ( this.ui.hasOwnProperty( k ))
             return this.ui[k];
        else if ( this.processing.hasOwnProperty( k ))     
             return this.processing[k];

        return this.service_provider.hasOwnProperty( k )
             ? this.service_provider[k] : undefined;

    }

    reviewable_fields( ){
        return [
            'dial_code_length', 'full_country_name','line_format',
            'delay','sending_target_country_code','provider','msg_url_mode','index_roll_in',
            'name_format','language','total_leads','total_messages','total_indexes','use_shortner_code_as_route_arg'
        ]
    }

    get_next_rotation_msg(  ) {

        let msg_container = this.get_entry('message_container');
        if ( msg_container.length) {
             this.processing.current_msg_pointer++;
             if ( this.processing.current_msg_pointer >= msg_container.length )
                  this.processing.current_msg_pointer = 0;
            return msg_container[this.processing.current_msg_pointer];
        }
        
        return '';

    }

    get_next_index_link( ) {

        let ndx_container = this.get_entry('index_container');
        if ( ndx_container.length) {
             if ( this.processing.index_sending_counter <= this.processing.index_roll_in ) {
                  this.processing.index_sending_counter++;
             } else {
                  this.processing.current_index_pointer++;
                  this.processing.index_sending_counter = 1;
             }
             
             if ( this.processing.current_index_pointer >= ndx_container.length )
                  this.processing.current_index_pointer = 0;

             return ndx_container[this.processing.current_index_pointer];
        }
        
        return '';

    }

}