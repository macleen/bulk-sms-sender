const config  = new Config( default_delay, settings_prefix, default_line_format );

function show_pause_signal() {
	jQuery(".loader-container").addClass('hidden');
    jQuery(".pause-container").removeClass('hidden')
	jQuery("#pause-button").addClass('hidden');
	jQuery("#resume-button").removeClass('hidden');
	jQuery('#processing-panel-title').text('PAUSED...');
}

function show_resume_signal() {
	jQuery(".pause-container").addClass('hidden')
	jQuery(".loader-container").removeClass('hidden');
	jQuery("#resume-button").addClass('hidden');
	jQuery("#pause-button").removeClass('hidden');
	jQuery('#processing-panel-title').text('PROCESSING...');

}

function update_total_sent( ) {
	const $total_messges_sent = jQuery('#total-messages-sent');
	const total_sent = +$total_messges_sent.text();
	$total_messges_sent.text( total_sent + 1);
}

function show_restart_and_pdf_btn( show_pdf_download_btn ){
    jQuery('.stop-button').addClass('hidden');
    jQuery('.restart-button').removeClass('hidden');
    if ( show_pdf_download_btn ){
        // enable partial log saving with pdf download 
        //  jQuery('.show-provider-log-button').removeClass('hidden');
    }         
}

function sendng_session_ended( gauge ) {
	gauge.stop( );
	jQuery('#processing-panel-title').text('PROCESSING ENDED');
	jQuery('#pause-button').addClass('hidden');
	jQuery('#resume-button').addClass('hidden');

	config.server.http.hideLoader();
	config.server.http.hide_sending_loader();
	enable_stage_buttons();

}

function stop_sending( gauge, msg, msg_type, flag ){

	show_alert( msg, msg_type );
	sendng_session_ended( gauge );
	show_restart_and_pdf_btn( flag );

}

function successfull_end( gauge ) {
	return stop_sending( gauge, 'Sending session has ended', 'success', true );
}

function interrupted_end( gauge ) {
	return stop_sending( gauge, 'Sending session interrupted', 'warning', false );
}

function display_new_balance( response ) {
		 let balance = response.hasOwnProperty('balance') ? response.balance : false;
		 balance    = balance ? balance : 'Balance: NA';
		 jQuery('#current-balance').removeClass('hidden').text( balance );

		 return response;
}

function display_results( response ) {

	let total;
	if ( response ) {

		let lead = response.hasOwnProperty('lead') ? `<span class="lead_in_result">${response.lead}</span>: ` : '';
		if ( response.result ) {
			total = +jQuery('#accepted-messages').text( );
			jQuery('#accepted-messages').text( total+1 );
			jQuery('#accepted-messages-tab').prepend( lead+(response.description)+'<br>' );
		} else {
			total = +jQuery('#failed-messages').text( );
			jQuery('#failed-messages').text( total+1 );
			jQuery('#failed-messages-tab').prepend( lead+(response.description)+'<br>' );
		}

	}
	return response;
}



function show_composed_msg( response ) {

	if ( response ) {
		 let display_message = response.hasOwnProperty('message') ? response.message : null;
		 let lead = response.hasOwnProperty('lead') ? `<span class="lead_in_result">${response.lead}</span>: ` : '';
		 if ( lead && display_message )
			  jQuery('#composed-message').html(lead+display_message);
	}
	return response;
}


function init_for_start_of_sending_session( ) {
	config.processing.total_sent = 0;
	config.processing.current_msg_pointer = -1;
	config.processing.current_index_pointer = 0;
	config.server.http.show_sending_loader();
	jQuery('#total-available-messages').text(config.get_entry('total_leads'));
	disable_stage_buttons( );
	hide_restart_and_get_send_log_btn( );
	jQuery('#accepted-messages-tab, #failed-messages-tab, #composed-message').html('');
	jQuery('#failed-messages').text('0');
	jQuery('#accepted-messages').text('0');
	jQuery('#total-messages-sent').text('0');
}
	

function start_sending( ){

	if ( !invalid_data_found( )){

		   const packet = new Packet( config ); 
		   const gauge  = new Gauge( config.get_entry( 'delay' ));
		   init_for_start_of_sending_session( );

		   new Streamer({
				generator: packet.sequencer( get_leads( )),
				stopButton: document.querySelector("#stop-button"),
				pauseButton: document.querySelector("#pause-button"),
				resumeButton: document.querySelector("#resume-button"),
				delayTime: config.get_entry( 'delay' ),				
				sendMessage: packet => config.server.http.send_message( packet ),
				onMessageSent: response => {
											const res = response.data;	
											display_new_balance( res );
											show_composed_msg( res );
											display_results( res );
											update_total_sent( );
											},
				onStart: 	 ( ) => {
					jQuery('#pause-button').removeClass('hidden');
					jQuery('resume-button').addClass('hidden');
				},
				onStreamEnd: ( ) => {},
				onCancel:	 ( ) => {},
				onPause:  	 ( ) => show_pause_signal(),
				onResume: 	 ( ) => show_resume_signal(),
				onEnd: 		 ( success ) => success ? successfull_end( gauge ) : void(0),
				onError: 	 ( ) => interrupted_end( gauge ),
				onTick: 	 (t) => gauge.updateUI( t ),
			}).start( );
	}
}


 
function index_action( $e, ...args ){
	const index = {
		get_available_providers: ( ) => config.server.get_available_providers(),
		get_provider_fields: ( ) => config.server.get_provider_fields( ),
		get_sending_summary: get_sending_summary,
		start_sending: start_sending,
	}
    let f = $e.data('element-action');
    if ( f && index.hasOwnProperty(f))
         setTimeout(() => {
            index[f](...args )
         }, 0);
}