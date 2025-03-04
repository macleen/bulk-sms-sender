function run_js_function(f, ...args) {
    if ( f ) {
        let arr = f.split('.'), fn = window[ arr[0] ];
        for (let i = 1; i < arr.length; i++)
            { fn = fn[ arr[i] ]; }
        return fn.call( window, ...args );
    }
}


function dec2hex(dec) {
    return ('0' + dec.toString(16)).substring(-2)
}


function show_alert( alert_text, type ) {
    type = type ? type : 'error';
    flash(`${plugin_name}: ${alert_text}`, {
        type: type,
        closable: true,
      });
}

//-----------------------------

function setup_rotating_messages( form_inputs ){
    config.processing.message_container = [];
    for ( var i = 0; i < form_inputs.length; i++) {
          if ( form_inputs[i].value.trim() )  
          config.processing.message_container.push( form_inputs[i].value );
    }
}


function setup_index_links( form_inputs ){
    config.processing.index_container = [];
    if ( form_inputs.length ) {
         var raw_lines = form_inputs[0].value.trim();
         if ( raw_lines ) {
              var lines = raw_lines.split("\n");
              for ( var i = 0; i < lines.length; i++) {
                    if ( lines[i].trim( ))  
                          config.processing.index_container.push( lines[i].trim( ));
              }
         }                  
    }
}






function prepare_summary_panel( ) {

    let $content = jQuery('.templates').find('table#fs-preview');
    if ( $content.length ) {
         for ( let key of config.reviewable_fields(  )){
               let v = config.get_entry( key );
               v     = typeof(v) === 'boolean' ? yes_no[v] : v;
               $content.find('.review-'+key).text( v );
         }
         jQuery('table#preview-content').html( $content.html( ));
    }     

}


function enable_stage_buttons( ) {
    jQuery('.stage-link').removeClass('is-disabled');
}

function disable_stage_buttons( ) {
    jQuery('.stage-link').addClass('is-disabled');
}


function hide_restart_and_get_send_log_btn( ){
    jQuery('.restart-button').addClass('hidden');
    jQuery('.show-provider-log-button').addClass('hidden');
    jQuery('#stop-button').removeClass('hidden');
    jQuery('#processing-panel-title').text('Processing...');
}




function get_sending_summary( ){
    let values = {};  
    let get_messages = ( inputs ) => {
        let m = [];
        for( let i of [1,2,3,4])
             if ( inputs.hasOwnProperty('message-'+i) && inputs['message-'+i].trim( ))
                  m.push(inputs['message-'+i]);
        return m;
    }

    jQuery.each(jQuery('form#msform').serializeArray(), function(i, field) {
        values[field.name] = field.value;
        config.set_entry( field.name, field.value );
    });

    config.set_entry('sender_id', values.sender_id);
    config.set_entry('delay', values.delay);
    config.set_entry('line_format', values.line_format);
    config.set_entry('total_leads', get_leads( ).length);
    config.set_entry('provider', values.service_provider);
    config.set_entry('index_roll_in', values.index_roll_in);
    config.set_entry('message_container', get_messages( values ));
    config.set_entry('index_container', split_lines( jQuery('#indexes-container').val( )));

    config.set_entry('total_messages', config.get_entry('message_container').length);
    config.set_entry('total_indexes', config.get_entry('index_container').length);

    config.server.http.showLoader( );
    setTimeout(() => {
        prepare_summary_panel( );
        config.server.http.hideLoader( )
    }, 1000);
    
}




function construct_form_input_field( field_id, key, value, options, help ){

    let entry;
    if ( options ) {
        entry = `<select id="${field_id}" name="${field_id}" data-keyname="${key}">`;
        for ( let option_name in options ) {
            let selected = options[option_name] == value ? ' selected ' : '';
            entry += `<option value="${options[option_name]}" ${selected}>${option_name}</option>`;
        }
        entry +='</select>';

    } else {
        let type = key == 'password' ? 'password' : 'text';
        entry = `<input type="${type}" id="${field_id}" name="${field_id}" data-keyname="${key}" 
                 placeholder="${key}" value="${value}"/>`;
    }                

    jQuery(document).on('keyup change', '#'+field_id, activate_provider_account_fetch_btn );
    return `<div class="provider-input-group">${entry}
                <div class="provider-input-help">${help}</div>
            </div>`;
}


function stringify_provider_input_fields( input_fields ){
    let output  = ['<h2 class="fs-title" style="color:red;">Provider Settings are unavailable or could not be fetched</h2>'];
    config.set_page_accessibility_by_jump( 'provider-settings', true );
    if ( input_fields ) {    
         if ( Object.keys(input_fields).length ) {
              output = [];
              let fields = config.get_entry('fields');
              let settings = {};
              let settings_prefix = config.get_entry('settings_prefix');

              for( let key in input_fields){
                   let o = input_fields[key];
                   let value = o.hasOwnProperty('value') ? o.value : null;
                   let options = o.hasOwnProperty('options') && Object.keys(o.options).length ? o.options : null;
                   let help = o.hasOwnProperty('help') ? o.help : '';
                   if ( value !== null ) {
                        let field_id = settings_prefix + key;
                        fields[field_id] = {key: key, value: value};
                        settings[key] = value;
                        output.push( construct_form_input_field( field_id, key, value, options, help ));
                   }
              }
              config.set_entry('fields',fields);
              config.set_entry('settings',settings);
         } else show_alert(`The returned Config Data for this Service provider is empty`, 'warning');
    } else show_alert(`Could not retrieve service Provider's config data`);  
    return output.join('<br>')
}




function extract_provider_info_and_add_ons( provider_fields ){

    let add_ons = {};
    if ( provider_fields && Object.keys(provider_fields).length ){
         let global_info  = provider_fields.hasOwnProperty('global_info')
                          ? provider_fields.global_info : null;

         add_ons = provider_fields.hasOwnProperty('add_ons')
                          ? provider_fields.add_ons : {};

         if ( global_info ) {
              let help_text = global_info.hasOwnProperty('help_text')
                            ? global_info.help_text : null;
              if ( help_text )
                   show_alert(help_text, 'success');
         }

         let keys = Object.keys( add_ons );
         if ( keys.length ) {
              let $sender_id  = jQuery('#sender_id');
              let placeholder = {
                                  false:'This provider does not support Sender-IDs',   
                                  true: 'Sender-ID'
                                };
              let supports_sender_id = add_ons.hasOwnProperty( 'supports_sender_id' )
                                     ? add_ons.supports_sender_id : false;
              config.set_entry('add_ons', add_ons );
              $sender_id.val(supports_sender_id ? 'SMSINFO' : '')
                        .attr('disabled', !supports_sender_id)
                        .attr('placeholder', placeholder[supports_sender_id]);
         }
    }

}

function prepare_provider_fields( provider_fields ){
    config.set_entry('provider', provider_fields.provider);
    jQuery('#selected-service-provider').text( config.get_entry('provider'));
    let input_fields = provider_fields && provider_fields.hasOwnProperty('input_fields')
                     ? provider_fields.input_fields : '';
    extract_provider_info_and_add_ons( provider_fields );

    let pre_provider_settings_html = stringify_provider_input_fields( input_fields );
    let provider_settings_html = get_provider_settings_html( pre_provider_settings_html );
    jQuery('.provider-account-and-settings-container').html(provider_settings_html).removeClass('hidden');

}


function get_provider_settings_html( input_fields_html ){
    let add_ons                = config.get_entry('add_ons');
    let account_data_available = add_ons && add_ons.hasOwnProperty('account_info_available')
                               ? add_ons.account_info_available : false;
    let main_width = account_data_available ? '70%' : '100%';

    let output = '<div id="provider-settings-container" style="width: '+main_width+';">'+input_fields_html+'</div>';
    jQuery('.provider-account-and-settings-container').css('display','block');
    if ( account_data_available ) {
        jQuery('.provider-account-and-settings-container').css('display','flex');
         output += `<div class="provider-account-info-container">
                         <div class="provider-account-info">
                             <h2 class="fs-title" style="font-weight:bold; color: #ca0000c9;">
                                 Provider account info not available
                             </h2>
                             <p style="font-size:9px;text-align:center;">Fill in the fields on the left side to authenticate first</p>
                         </div>                
                         <input type="button" class="action-button specific-provider-account-info-btn is-disabled" value="Account Info" style="background:#06aeabba;"/>
                     </div>`;
    }
    return output;

}


function activate_provider_account_fetch_btn( e ){

    jQuery('.specific-provider-account-info-btn').addClass('is-disabled');
    if ( Object.keys(config.get_entry('fields')).length ) {
         let fields_are_filled_in = true;
         let fields = config.get_entry('fields');
         let settings = config.get_entry('settings');
         for ( let field_id in fields ) {
               let v = String(jQuery('#'+field_id).val( )).trim( );
               fields[field_id].value = v;
               settings[fields[field_id].key] = v;
               if ( !v ) {
                     fields_are_filled_in = false;
                     break;
               }
         }
         config.set_entry('fields', fields);
         config.set_entry('settings', settings);
         if ( fields_are_filled_in )
              jQuery('.specific-provider-account-info-btn').removeClass('is-disabled')
    }
}


function remove_provider_account_info( message ) {

    let output = '<h2 class="fs-title" style="font-weight:bold; color: #ca0000c9;">Provider account info not available</h2>'+
                 '<p style="font-size:9px;text-align:center;">Fill in the fields on the left side to authenticate first</p>';

    jQuery('.provider-account-info').html(output);
    jQuery('.specific-provider-account-info-btn').addClass('is-disabled');
    if ( message )
         show_alert(message);
}


function display_provider_account_info( response ) {
    let output = '<h2 class="fs-title" style="font-weight:bold; color: #ca0000c9;">Account data could not be parsed</h2>';
    if ( Object.keys(response).length ) {        
        console.log('Response from account info: ', response);
         output = '<table class="dynamic-account-info-table"><tbody>';
         for ( let key in response.data ) {
             output += `<tr>
                          <td>${key}</td>
                          <td>${response.data[key]}</td>
                        </tr>`;
         }
         output += '</tbody></table>';
    }                 
    jQuery('.provider-account-info').html(output);
    jQuery('.specific-provider-account-info-btn').addClass('is-disabled');
}

function invalid_data_found( ){
    var page_fault = false;
    let config_pages = ['local-settings','tags-settings'];

    let check_and_display = function( $e, page ) {
        if ( !$e.val( ) && !$e.data('optional')) {
             show_alert(`Data on page ${page} contains invalid fields`);
             jQuery(`i.stage-btn[data-page="${page}"]`).trigger('click');
             page_fault = true;
             return false;
        }
        return true;     
    }
    config_pages.forEach( page => {
        if ( !page_fault ) {
              let inputs = jQuery('fieldset#'+page).find('input:not(.action-button)');
              inputs.each(( k,input ) => {
                if ( !check_and_display( jQuery(input), page ))  
                      return false;
              })   
        }      
        if ( !page_fault ) {              
              let selects = jQuery('fieldset#'+page).find('select');
              selects.each(( k,select ) => {
                if ( !check_and_display( jQuery(select), page ))  
                     return false;
              })
        }
        if ( !page_fault ) {
             if ( config.get_entry('total_leads') < 1 ) {
                  show_alert('Data on the leads page still needs to be updated');
                  jQuery(`i.stage-btn[data-page="leads"]`).trigger('click');
                  page_fault = true;
              } else if ( parseInt(config.get_entry('index_roll_in')) < 1) {
                  show_alert('Index roll in must have a valid value higher than 0');
                  jQuery(`i.stage-btn[data-page="indexes"]`).trigger('click');
                  page_fault = true;
              }
        }
    })

    if ( !page_fault ) {
          let total_messages = config.get_entry('total_messages');
          let total_indexes = config.get_entry('total_indexes');
          let msg_url_mode = config.get_entry('msg_url_mode');
          if ( !total_messages ) {
                show_alert('Messages container is empty');
                jQuery(`i.stage-btn[data-page="messages"]`).trigger('click');
                page_fault = true;
          } else if ( !total_indexes ) {
                if ( msg_url_mode == 'index') {
                     show_alert('You have chosen to apply indexing but no indexes were found');
                     jQuery(`i.stage-btn[data-page="indexes"]`).trigger('click');
                     page_fault = true;
                }
          }  
    }

    return page_fault;
}

function split_lines( text_content ) {
    text_content = text_content.trim();
    return text_content ? text_content.split('\n') : [];
}


function get_leads( ) {
    let leads = split_lines( jQuery('#leads-container').val( ));
    jQuery("#leads-count").text(leads.length);
    return leads;
}

