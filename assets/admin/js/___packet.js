class Packet {

    static char_table = ['1','a','3','b','c','d','5','e','7','f'];  

    constructor( parent_config ) {
        this.init( parent_config );
    }

   
    init( parent_config ){
        this.config             = parent_config;
        this.line_parts         = [];
        this.line_fields        = {};
        this.fixed_packet_parms = this.#init_packet_parms( );
        this.packet_container   = this.#init_packet_container( );
        this.#set_fixed_parms( );
    }        

    set_entry( k, v ) {

        if ( this.fixed_packet_parms.hasOwnProperty( k ))
             this.fixed_packet_parms[k] = v;
        else if ( this.packet_container.hasOwnProperty( k ))     
             this.packet_container[k] = v;
    }


    get_entry( k ) {

        if ( this.fixed_packet_parms.hasOwnProperty( k ))
             return this.fixed_packet_parms[k];
        return this.packet_container.hasOwnProperty( k )
             ? this.packet_container[k] : 'undefined';
    }



    #init_packet_parms( ) {
        return {
            sender_id: '', 
            dial_code_length:'',
            sending_target_country_code:'',
            full_country_name:'',
            language:'', 
            msg_url_mode: 0,
            ndx_with_packet_id: false, 
            use_shortner_code_as_route_arg:'NO',
          }
    }          
         
    #init_packet_container( ) {
        return {
            recipient: '', 
            message: '', 
            phone:'',
            email: '', 
            full_name:'', 
            generic_name: '', 
            address: '', 
            other_info: '',
            index:'',
          }        
    }

    #set_fixed_parms(  ) {

        for( let key in this.fixed_packet_parms ) {
            this.fixed_packet_parms[key] = this.config.get_entry( key );
        }
        this.#set_field_markers( );

    }



    #convert( digits_str ) {
        let result = '';
        digits_str = digits_str.trim();
        if (digits_str) {
            digits_str.split('').forEach(function(v) {
                if (typeof Packet.char_table[v] !== 'undefined')
                    result += Packet.char_table[v];
                else result += v;
            })
        }
        return result;

    }

    #set_packet_identifier( ) {
        let recipient = this.packet_container.phone.trim( )
                      ? this.packet_container.phone : this.packet_container.recipient;
        return this.#convert( recipient );
    }



    #random_str( ) {
        let arr = new Uint8Array(( this.config.get_entry('random_string_length') || 40) / 2)
        window.crypto.getRandomValues(arr)
        return Array.from(arr, dec2hex).join('')
    }

 
    #compose_message( ) {
        let msg = this.config.get_next_rotation_msg( );
        if ( msg ) {
             let tags   = [/__FULL_NAME__/g,/__PHONE__/g,/__RANDOM_STR__/g,/__ID__/g];
             let fields = [
                            this.packet_container.generic_name,
                            this.packet_container.phone,
                            this.#random_str( ),
                            this.#set_packet_identifier( ),
                          ];

             tags.forEach(function( tag, index ){
                msg = msg.replace(tag, fields[index]);
             });
    
        }
        return msg;
    }

 

    #set_field_markers( ) {

        let raw_tags                  = this.config.get_entry('line_format');
        raw_tags                      = raw_tags ? raw_tags : 'P-N';
        let tags                      = raw_tags.split('-');
        this.line_fields['phone']     = tags.indexOf('P');
        this.line_fields['full_name'] = tags.indexOf('N');
        this.line_fields['email']     = tags.indexOf('E');
        this.line_fields['address']   = tags.indexOf('A');

    }


    #set_generic_name( ){

        let full_name    = this.packet_container.full_name;
        let generic_name = full_name;
        let name_format  = this.config.get_entry('name_format');
        let name_parts   = full_name.split(' ');
        let nl           = name_parts.length;
        name_format      = name_format ? name_format : 'FullName';
        if ( nl ) {
             switch ( name_format ) {
                    case 'FirstName'  : generic_name = name_parts[0];break;
                    case 'SecondName' : generic_name = nl > 1 ? name_parts[1] : name_parts[0];break;
                    case 'LastName'   : generic_name = name_parts[nl-1];break;
                    case 'skip1st'    : name_parts.shift();
                                        generic_name = name_parts.length 
                                                     ? name_parts.join(' ') : recipient_name;
                                        break;             
             }
        }

        return generic_name;

    }


    #set_packet_container( line ) {

        let ndx_with_packet_id = this.config.get_entry('index_with_packet_id');
        let other_data         = [];
        let line_parts         = line.split( field_seperator );
        let nbr_of_line_parts  = line_parts.length;
        
        for( let field in this.line_fields ){
             let i = this.line_fields[field];
             if ( i >= 0 ) {
                  let this_data = i < nbr_of_line_parts ? line_parts[i] : '';
                  this_data = String(this_data).trim();
                  if ( field in this.packet_container ){
                       this.packet_container[field] = this_data;
                  } else other_data.push( this_data );
             }
        }
        this.packet_container.generic_name = this.#set_generic_name( );
        this.packet_container.other_info   = other_data.join('|');
        this.packet_container.recipient    = this.packet_container['phone'];
        this.packet_container.index        = this.config.get_next_index_link( ) + ( ndx_with_packet_id ? '/'+this.#set_packet_identifier( ) : '');
        this.packet_container.message      = this.#compose_message( );
    }



    // Main Assembly func
    assemble( line ) {

        line = line.trim();
        if (line) {
            jQuery('.composed-message-container').removeClass('hidden');
            this.#set_packet_container( line );
            return {...this.fixed_packet_parms, ...this.packet_container, ...this.config.get_entry('settings')}
        }
        return '';
    }

    sequencer( leads ) {
        const self = this;
        return function * (  ) {

            let halted;
            for (let i = 0; i < leads.length; i++) {
                halted = yield self.assemble( leads[i] );
                if ( halted ) return;
            }
        }
    }    
}