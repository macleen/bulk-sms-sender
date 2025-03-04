;(function($) {
    // Activate jQuery events / MacLeen v1 04-2021
    var current_fs, next_fs, previous_fs; //fieldsets
    var left, opacity, scale; //fieldset properties which we will animate
    var animating; //flag to prevent quick multi-click glitches
    var tabs = $(".tabs li a");

    get_and_dump_leads = function (  role ){
        config.server.get_wp_leads( role )
          .then( response => {
                const data = response.data;
                const append_mode = $('#leads_append_mode').find('option:selected').val( );
                switch( append_mode ) {
                    case 'replace' : $('#leads-container').val( data );
                                    break;   
                    default        : const v = $('#leads-container').val().trim();
                                    $('#leads-container').val( v+"\n"+data);
                                    break;
                }
                console.log('leads ', response );
          });
    }

    $(".lined").numberedtextarea();
    $(".panel-footer").html(event_vars.copyright_data);
        
    $('#service_provider').on('change', function( ){
        config.set_entry('add_ons', {});
        config.set_entry('provider', $(this).val( ));
        $('#selected-service-provider').text( config.get_entry('provider'));
        config.set_page_accessibility_by_jump( 'provider', true );
        config.set_page_accessibility_by_jump( 'provider-settings', false );
    });

    $('#use_shortner_code_as_route_arg').on('change', function( ){
        config.set_entry('use_shortner_code_as_route_arg', $(this).val( ));
    });

    $('#full_country_name').on('change', function( ){
        let $this = $('#full_country_name option:selected');
        config.set_entry('sending_target_country_code', $this.val( ));
        config.set_entry('full_country_name', $this.text( ));
        config.set_entry('dial_code_length', $this.data('dial_code_length'));
        config.set_page_accessibility_by_jump( 'local-settings', true );
    });

    $('#msg_url_mode').on('change', function () {
        let v = $(this).val( );
        console.log('msg_url_mode: ', v);
        switch ( v ) {
            case 'shortner': $('#language-indicator').attr('disabled', false);
                             $('#indexes-container').attr('disabled', true);
                             $('#index_roll_in').attr('disabled', true);
                             
                             break;
            case 'index'  :  $('#language-indicator').attr('disabled', false);
                             $('#indexes-container').attr('disabled', false);
                             $('#index_roll_in').attr('disabled', false);                            
                            
                             break;
            default       :  $('#language-indicator').attr('disabled', true);
                             $('#indexes-container').attr('disabled', true);
                             $('#index_roll_in').attr('disabled', true);
                            
                             break;
        }
        config.set_entry('indexing_method', v);
    });


    $('#leads_input_source').on('change', function(){
        const $this          = $(this);
        const selectedOption = $this.find('option:selected');
        const info           = selectedOption.data('info');
        $('#leads_input_selection_title').text(info).css('color', 'blue');
        if ( $this.val( ))
             get_and_dump_leads( $this.val( ));
    });
    

    $(document).on('click', '.specific-provider-account-info-btn', function( e ){
        e.preventDefault( );
        if ( Object.keys( config.get_entry( 'settings' )))
                config.server.get_account_info( )
                    .then(display_provider_account_info) 
                    .catch(remove_provider_account_info);
        else remove_provider_account_info('Some Provider fields are missing or invalid');
    });





    $('#upload-new-plugin-btn').click( function () {
        document.getElementById('zip-file-input').click();
    });

    $('#zip-file-input').change( function () {
        let file = this.files[0];

        if (!file) return;
        if (!file.name.endsWith('.zip')) {
            show_alert( 'Only zip files are allowed', 'error' );
            return;
        }

        let formData = new FormData();
        formData.append('zip_file', file);
        config.server.install_plugin( formData )
            .then( msg => {
                if ( msg )
                    show_alert( msg, 'success' );
                    setTimeout(()=> {
                        file.name.startsWith('mac')
                           ? window.location.reload( )
                           : jQuery('#provider-tab').trigger('click');
                    },1500);
            });
    });

    $('#macleen-selected-purchasable-plugin-purchase').click( function(){
        const selected_plugin = $(this).attr('data-selected-plugin'); 
        const plugin_location = $(this).attr('data-plugin-location'); 
        if ( selected_plugin && plugin_location )
            window.open( `${plugin_location}/${selected_plugin}`, '_blank');
        else show_alert('Please select a plugin in the purchasable area first','warning');
    })

    $('#delete_log_file').on('click', function(){
        const log_date = $(this).attr('data-logDate');
        if ( log_date ) {
            config.server.delete_log_by_date( log_date )
                  .then(( ) => {
                    Logging.reset();
                    show_alert( `Log File for ${log_date} successfully deleted`, 'success');
                }); 
                  
        } else show_alert('Invalid log file identifier', 'error');
    });

    $('#delete_analytics_file').on('click', function(){
        const file_path = $(this).attr('data-filepath');
        if ( file_path ) {
            config.server.delete_analytics_by_path( file_path )
                  .then(( ) => {
                    Analytics.reset();
                    show_alert( `Hashed-Dump with index ${dump_index} successfully deleted`, 'success');
                }); 
                  
        } else show_alert('Unknown hash-index or invalid file path', 'error');
    });

    $(".sound-control").on('click', function(){
        if ($(".loader-container").is(':visible')) {
            var soundTrack = document.getElementById("sound-track");    
            config.set_entry('sound_is_on', !config.get_entry('sound_is_on'));
            config.get_entry('sound_is_on') ? $("#loader").addClass(( ) => {
                                                  $('#loader').removeClass(( ) => {
                                                      soundTrack.play( )
                                                      return 'zz_loader';
                                                  });   
                                                  return 'loader-md';
                                              }).attr("src",`${event_vars.plugin_url}assets/admin/img/loader.gif`)
                                           : $("#loader").addClass(( ) => {
                                                  $('#loader').removeClass(( ) => {
                                                      soundTrack.pause( )
                                                      return 'loader-md';
                                                  });   
                                                  return 'zz_loader';
                                             }).attr("src",`${event_vars.plugin_url}assets/admin/img/zz_loader.gif`)
        }    
    });

    
    $('.btn').on("click", function(){
        $('.btn').toggleClass('close-btn');
        $('.sidebar').toggleClass('sidebar-open');
    })

    $(".next").click(function(){
        if(animating) return false;
        animating = true;
        $('.fs').removeClass('current-fs');
        current_fs = $(this).parent();
        next_fs = $(this).parent().next();

        let page = current_fs.prop('id');
        if ( !['provider','provider-settings'].includes( page )){
            config.set_page_accessibility_by_jump( page, true );
        }
        //activate next step on progressbar using the index of next_fs
        $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
        
        //show the next fieldset
        next_fs.addClass('current-fs');
        next_fs.show(); 

        //hide the current fieldset with style
        current_fs.animate({opacity: 0}, {
            step: function(now, mx) {
                //as the opacity of current_fs reduces to 0 - stored in "now"
                //1. scale current_fs down to 80%
                // scale = 1 - (1 - now) * 0.2;
                //2. bring next_fs from the right(50%)
                left = (now * 50)+"%";
                //3. increase opacity of next_fs to 1 as it moves in
                opacity = 1 - now;
                current_fs.css({
            // 'transform': 'scale('+scale+')',
            'position': 'absolute'
        });
                next_fs.css({'position': 'relative','left': left, 'opacity': opacity});
            }, 
            duration: 600, 
            complete: function(){
                current_fs.hide();
                animating = false;
                index_action( next_fs, current_fs, next_fs );
            }, 
            //this comes from the custom easing plugin
            easing: 'easeInOutBack'
        });
        
    });

    $(".previous").click(function(){
        if(animating) return false;
        animating = true;
        
        $('.fs').removeClass('current-fs');
        current_fs = $(this).parent();
        previous_fs = $(this).parent().prev();
        
        //de-activate current step on progressbar
        $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");
        $("#progressbar li").eq($("fieldset").index(previous_fs)).addClass("active");
        
        //show the previous fieldset
        previous_fs.addClass('current-fs');
        previous_fs.show(); 
        //hide the current fieldset with style
        current_fs.animate({opacity: 0}, {
            step: function(now, mx) {
                //as the opacity of current_fs reduces to 0 - stored in "now"
                //1. scale previous_fs from 80% to 100%
                // scale = 1 - (1 - now) * 0.2;
                //2. bring next_fs from the left(50%)
                right = (now * 50)+"%";
                //3. increase opacity of previous_fs to 1 as it moves in
                opacity = 1 - now;
                // current_fs.css({'left': left});
                current_fs.css({
                     'position': 'absolute'
                    // 'transform': 'scale('+scale+')', 
                    // 'opacity': opacity
                });
                previous_fs.css({'position': 'relative','right': right, 'opacity': opacity});
            }, 
            duration: 600, 
            complete: function(){
                current_fs.hide();
                animating = false;
                index_action( previous_fs, current_fs, previous_fs );
            }, 
            //this comes from the custom easing plugin
            easing: 'easeInOutBack'
        });
    });

    $(".stage-btn").click(function( ){

        let page = $(this).data("page");

        if(animating) return false;
        animating = true;
        let stage_id = $(this).data("target");
        current_fs = $('.current-fs').first();
        $('.fs').removeClass('current-fs');
        next_fs = $(stage_id);

        //activate next step on progressbar using the index of next_fs
        $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
        
        //show the next fieldset
        next_fs.addClass('current-fs');
        next_fs.show(); 

        //hide the current fieldset with style
        current_fs.animate({opacity: 0}, {
            step: function(now, mx) {
                //as the opacity of current_fs reduces to 0 - stored in "now"
                //1. scale current_fs down to 80%
                scale = 1 - (1 - now) * 0.2;
                //2. bring next_fs from the right(50%)
                left = (now * 50)+"%";
                //3. increase opacity of next_fs to 1 as it moves in
                opacity = 1 - now;
                current_fs.css({
                    //'transform': 'scale('+scale+')',
                    'position': 'absolute'
                });
                next_fs.css({'position': 'relative','left': left, 'opacity': opacity});
            }, 
            duration: 600, 
            complete: function(){
                current_fs.hide();
                animating = false;
                index_action( next_fs, current_fs, next_fs );
            }, 
            //this comes from the custom easing plugin
            easing: 'easeInOutBack'
        });

    });

    $('.tab').on('click', function( ){
        $('.tab-label').removeClass('active-tab');
        $(this).find('label').addClass('active-tab');
    });

    $('.stop-button').on('click', function( e ){
        e.preventDefault( );
        $('#stop-button').val('Stopping...');
        config.set_entry('halted_by_user', true);
    });

    $('.restart-button').on('click', function( ){
        $("a.provider-settings-stage").find('i.stage-btn').trigger('click');
        $('ul#progressbar').children('li').removeClass('active');
        $('ul#progressbar').children('li').removeClass('active');
        $('ul#progressbar').children().eq(0).addClass('active');
        $('ul#progressbar').children().eq(1).addClass('active');
    });

    tabs.click(function() {
        var content = this.hash.replace('/','');
        tabs.removeClass("active");
        $(this).addClass("active");
        $("#content").find('p').addClass('hidden');
        $(content).removeClass('hidden');
    });


    const tab_buttons = document.querySelectorAll(".responsive-tab-btn");
    const tab_panels = document.querySelectorAll(".responsive-tab-panel");

    tab_buttons.forEach((button) => {
        button.addEventListener("click", () => {
            // Remove active class from all buttons
            tab_buttons.forEach((btn) => btn.classList.remove("active"));
            button.classList.add("active");

            // Hide all tab panels
            tab_panels.forEach((panel) => panel.classList.remove("active"));
            
            // Show the selected tab
            document.getElementById(button.dataset.tab).classList.add("active");
        });
    });
    setTimeout(()=> index_action( $('#provider.fs' )),100);

}(jQuery));