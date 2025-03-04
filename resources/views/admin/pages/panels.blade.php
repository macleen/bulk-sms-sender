@extends('admin.layouts.sending-app')
@section('content')

<!-- fieldsets -->
<fieldset id="provider" class="fs current-fs" data-element-action="get_available_providers">
  <h2 class="fs-title">Service Provider</h2>
  <h3 class="fs-subtitle">Select a service provider</h3>
  <select id="service_provider" name="service_provider"></select>
  <div class="ajax-loader-box" style="margin-top:1rem;">
    <img src="{{$plugin_url}}/assets/admin/img/loading-bar.gif" class="loading-bar package-loader hidden" />
  </div>
  <input type="button" name="next" class="next action-button" value="Next" data-target-func="set_provider"/>
  <div class="panel-footer"></div>
</fieldset>

<fieldset id="provider-settings" class="fs" data-element-action="get_provider_fields">
  <h2 class="fs-title">
      <span id="selected-service-provider" style="font-weight:bold; color: red;">Provider</span>: 
      Authentication & configuration
  </h2>
  <div class="provider-account-and-settings-container hidden"></div>
  <div class="ajax-loader-box">
    <img src="{{$plugin_url}}/assets/admin/img/loading-bar.gif" class="loading-bar package-loader" />
  </div>
  <input type="button" name="previous" class="previous action-button" value="Previous" data-target-func="set_provider_settings"/>
  <input type="button" name="next" class="next action-button" value="Next" />
  <div class="panel-footer"></div>
</fieldset>

<fieldset id="local-settings" class="fs" data-element-action="">
  <h2 class="fs-title">Local Settings</h2>
  <h3 class="fs-subtitle">
    Client Delay will be added to the server processing time
  </h3>
  <input type="text" id="sender_id" name="sender_id" minlength="3" maxlength="15" placeholder="Sender-ID" title="Set the sender name or id" value="SMSINFO" data-optional="true"/>
  <div class="provider-input-help" style="text-align:left;margin-bottom:10px;">Max length is 11 if the sender ID is alphanumeric, 15 otherwise.</div>
  @include('admin.components.time-interval')
  @include('admin.components.language-codes')
  <div class="country-container">
    @include('admin.components.country-list')
  </div>
  <input type="button" name="previous" class="previous action-button" value="Previous"  data-target-func="set_local_settings"/>
  <input type="button" name="next" class="next action-button" value="Next" />
  <div class="panel-footer"></div>
</fieldset>

<fieldset id="messages" class="fs" data-element-action="">
  <h2 class="fs-title">Rotational Messages</h2>
  <button type="button" class="tag-collapsible">Show Available System Tags</button>
  <div class="content">
    <h3 class="fs-subtitle">
      Available System Tags: <br><br>
      <div>
        <table style="width:100%">
          <thead>
            <tr>
              <th style="padding:10px;color:#ffff009e;">Tag</th>
              <th style="padding:10px;color:#ffff009e;">Function</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><span>__FULL_NAME__</span> </td>
              <td>Returns the full name</td>  
            </tr>  
            <tr>
              <td><span>__PHONE__</span></td>
              <td>The Phone nbr</td>
            </tr>  
            <tr>
              <td><span>__INDEX__</span></td>
              <td>The url to be clicked</td>
            </tr>  
            <tr>
              <td><span>__ID__</span></td>
              <td>Packet ID</td>
            </tr>  
            <tr>
              <td><span>__RANDOM_STR__</span></td>
              <td>System generated</td>
            </tr>  
            <tr>
              <td><span>__DATE__</span></td>
              <td>Today's date</td>
            </tr>  
            <tr>
              <td><span>__TIME__</span></td>
              <td>Current time</td>
            </tr>  
            <tr>
              <td><span>__DAY_OF_WEEK__</span></td>
              <td>[ Monday,...., Sunday ]</td>
            </tr>  
            <tr>
              <td><span>__DAY_OF_YEAR__</td>
              <td>[ 1, ...., 366 ]</td>
            </tr>              
            <tr>
              <td><span>__WEEK_OF_YEAR__</span></td>
              <td>[ 1, ...., 52 ]</td>
            </tr>              
            <tr>
              <td><span>__MONTH_NAME__</span></td>
              <td>[ January,...., December ]</td>
            </tr>              
            <tr>
              <td><span>__MONTH_NUMBER__</span></td>
              <td>[ 01, ...., 12 ]</td>
            </tr>  
            <tr>
              <td><span>__CURRENT_YEAR__</span></td>
              <td>YYYY</td>
            </tr>  
          </tbody>
        </table>   
      </div>  
    </h3>
  </div>
  <input type="text" name="message-1" placeholder="Message 1" value="Hi __FULL_NAME__, this is msg1 id __RANDOM_STR__. click on __INDEX__"/>
  @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
    <small class="pro-version-availability-notice">* Pro version only</small>
  @endif  
  <input type="text" name="message-2" placeholder="Message 2" {{ $disabled }}/>
  @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
    <small class="pro-version-availability-notice">* Pro version only</small>
  @endif  
  <input type="text" name="message-3" placeholder="Message 3"  {{ $disabled }}/>
  @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
    <small class="pro-version-availability-notice">* Pro version only</small>
  @endif  
  <input type="text" name="message-4" placeholder="Message 4"  {{ $disabled }}/>  
  <input type="button" name="previous" class="previous action-button" value="Previous" />
  <input type="button" name="next" class="next action-button" value="Next" />
  <div class="panel-footer"></div>
</fieldset>

<fieldset id="indexes" class="fs" data-element-action="adjust_language_and_index_fields">
  <h2 class="fs-title">URL Linking Mode</h2>
  <h3 class="fs-subtitle">Operational mode of urls in your messages</h3>
  @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
    <small class="pro-version-availability-notice">* Pro version only</small>
  @endif  
  <select id="msg_url_mode" name="msg_url_mode" title="Indexation option" {{ $disabled }}>
    <option value="" selected>None</option>                                            
    <option value="shortner">Shortner</option>
    <option value="index" title="Link(s) will be used as is">as Url</option>
  </select>
  @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
    <small class="pro-version-availability-notice">* Pro version only</small>
  @endif  
  <input type="text" id="index_roll_in" name="index_roll_in" placeholder="Index roll in frequency" title="Index roll in frequency" value="1" disabled {{ $disabled }}/>
  @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
    <small class="pro-version-availability-notice">* Pro version only</small>
  @endif  
  <select id="use_shortner_code_as_route_arg" name="use_shortner_code_as_route_arg" title="Use the shortner code as a route argument or route segment" {{ $disabled }}>
    <option value="NO">No</option>                                            
    <option value="YES" selected>Yes</option>
  </select>
  @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
    <small class="pro-version-availability-notice">* Pro version only</small>
  @endif 
  <textarea id="indexes-container" class="lined shadowed" placeholder="index links" style="height: 8rem;resize:none;" disabled {{ $disabled }}>https://your-index-url.com</textarea>
  <input type="button" name="previous" class="previous action-button" value="Previous" />
  <input type="button" name="next" class="next action-button" value="Next" />
  <div class="panel-footer"></div>
</fieldset>

<fieldset id="leads" class="fs" data-element-action="">
  <img src="{{$plugin_url}}/assets/admin/img/u__loader.gif" class="loading-bar package-loader" style="width: 70px;position: absolute;right: 45%;top: 50%;z-index: 100;"/>
  <h2 class="fs-title">Leads</h2>
  <h3 id="leads_input_selection_title" class="fs-subtitle">Leads & Parts</h3>  
  @include('admin.components.name-format')
  @include('admin.components.leads-append-mode')
  @include('admin.components.leads-input-source')  
  <textarea id="leads-container" class="lined shadowed" placeholder="Leads" style="height: 8rem;resize:none;">32465888660; choko momo; some address</textarea>
  <input type="button" name="previous" class="previous action-button" value="Previous" />
  <input type="button" name="next" class="next action-button" value="Next"/>
  <div class="panel-footer"></div>
</fieldset>

<fieldset id="tags-settings" class="fs" data-element-action="">
  <h2 class="fs-title">Lead Line Format</h2>
  <h3 class="fs-subtitle" style="font-size: 0.8rem;font-weight: bold;">
    <span style="color:red;font-size:1rem;">P</span>: Phone, 
    <span style="color:red;font-size:1rem;">N</span>: Name, 
    <span style="color:red;font-size:1rem;">A</span>: Address, 
    <span style="color:red;font-size:1rem;">E</span>: Email
  </h3>
  <div class="tags-container">
    <table class="tags-table">
      <tbody>
        <tr>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="P-N" class="msg-model" /><label class="lead-type">P-N</label>
            </div>
          </td>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="P-N-A" class="msg-model" checked/><label class="lead-type">P-N-A</label>
            </div>
          </td>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="P-N-A-E" class="msg-model" /><label class="lead-type">P-N-A-E</label>
            </div>
          </td>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="N-P" class="msg-model" /><label class="lead-type">N-P</label>
            </div>
          </td>
        </tr>

        <tr>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="N-P-A" class="msg-model" /><label class="lead-type">N-P-A</label>
          </div>

          </td>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="N-P-A-E" class="msg-model" /><label class="lead-type">P-N-A-E</label>
          </div>
            
          </td>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="N-P-E" class="msg-model" /><label class="lead-type">N-P-E</label>
            </div>
              
          </td>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="N-A-P" class="msg-model" /><label class="lead-type">N-A-P</label>
            </div>
            
          </td>
        </tr>
        <tr>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="N-A-P-E" class="msg-model" /><label class="lead-type">N-A-P-E</label>
          </div>

          </td>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="E-P-N" class="msg-model" /><label class="lead-type">E-P-N</label>
          </div>
            
          </td>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="E-N-P" class="msg-model" /><label class="lead-type">E-N-P</label>
          </div>
            
          </td>
          <td>
            <div class="radio-input-label">
              <input type="radio" name="line_format" value="E-N-P-A" class="msg-model" /><label class="lead-type">E-N-P-A</label>
            </div>
          </td>

        </tr>
      </tbody>
    </table>
  </div>
  <input type="button" name="previous" class="previous action-button" value="Previous"  data-target-func="set_local_settings"/>
  <input type="button" name="next" class="next action-button" value="Next" />
  <div class="panel-footer"></div>
</fieldset>

<fieldset id="preview" class="fs" data-element-action="get_sending_summary">
  <h2 class="fs-title">Preview Summary</h2>
  <table id="preview-content">
    <tbody class="place-holder-element">
      <tr>
        <td>
          <div class="ajax-loader-box">
             <img src="{{$plugin_url}}/assets/admin/img/loading-bar.gif" class="loading-bar package-loader" />
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <input type="button" name="previous" class="previous action-button" value="Previous" />
  <input id="start_sending" type="button" name="next" class="next action-button" value="Start Sending" />
  <div class="panel-footer"></div>
</fieldset>

<fieldset id="send" class="fs" data-element-action="start_sending" style="width: 100%; margin:0;">
  <div id="message-counter" class="message-counter" title="Line nbr being currently processed">
    <span id="total-messages-sent">0</span>/<span id="total-available-messages">0</span>
  </div>  
  <div id="current-balance" class="current-balance" title="Available Balance">0</div>
  <h2 id="processing-panel-title" class="fs-title">Processing...</h2>
  <div style="display: flex;justify-content: space-between;">
    <div class="" style="width:80%;">

      <ul class="tabs group">
        <li>
          <a class="active" href="#/accepted-messages-tab">
            <span id="accepted-messages" class="accepted-messages numberCircle">0</span>
              Queued
          </a>
        </li>
        <li>
          <a href="#/failed-messages-tab">
            <span id="failed-messages" class="failed-messages numberCircle">0</span>
            Failed
          </a>
        </li>

      </ul>
      
      <div id="content">
        <p id="accepted-messages-tab"></p>
        <p id="failed-messages-tab" class="hidden"></p>
      </div>
      
    </div>

    <div class="processing-progress op-indicator">
      <div class="loader-container text-center hidden">
          <audio id="sound-track" class="sound-track-control hidden" controls="controls">
            <source src="{{$plugin_url}}/assets/admin/img/connected.mp3" type="audio/mpeg" />
          </audio>
          <img id="loader" class="zz_loader sound-control" src="{{$plugin_url}}/assets/admin/img/zz_loader.gif" alt="loading..." title="Click to set sound on/off" style="width: 100%;margin: 50% 0 0 0;">
          <div id="delay-timer-countdown" style="color:#0000004f;font-weight:700;font-size:1.5rem;font-family: sans-serif;">0</div>
      </div>
      <div class="pause-container pause-signal-container hidden">
        <img id="paused" class="zz_loader" src="{{$plugin_url}}/assets/admin/img/pause.png" alt="PAUSED" style="width: 50%;">          
      </div>
    </div>
  </div>  
  <div class="gauge-container">
    <div id="gauge-meter"></div>
  </div>
  <div class="composed-message-container hidden">
      <div style="font-size: 0.6rem;font-weight: bold;padding-bottom: 0.1rem;color:#174c1a;">Composed Message [ This is how the message will appear on the receiver's end ]:</div>
      <div id="composed-message" style="margin-top:0.5rem;color:black;"></div>
  </div>

  <input id="stop-button" type="button" name="stop" class="action-button stop-button" value="Cancel" />
  <input id="pause-button" type="button" name="pause" class="action-button pause-button" value="Pause" />
  <input id="resume-button" type="button" name="resume" class="action-button resume-button hidden" value="Resume" />
  <input type="button" class="action-button show-provider-log-button hidden" value="Live Log" />
  <input type="button" class="action-button restart-button hidden" value="Restart" />
  <div class="panel-footer"></div>
</fieldset>


<div class="templates hidden">
  <table id="fs-preview">
  <tbody>
      <tr>
          <td>Provider</td>
          <td class="review review-provider"></td>  
          <td>Delay</td>
          <td class="review">
          <span class="review-delay">0</span> sec
          </td>  

      </tr>
      <tr>
          <td>Message Rotations</td>
          <td class="review review-total_messages">0</td>  
          <td>Total Leads</td>
          <td class="review review-total_leads">0</td>                
      </tr>
      <tr>
          <td>Shortner code as route arg</td>
          <td class="review review-use_shortner_code_as_route_arg"></td>
          <td>Language</td>
          <td class="review review-language"></td>  
      </tr>
      <tr>
          <td>Country</td>
          <td class="review review-full_country_name"></td>  
          <td>a2-code</td>
          <td class="review review-sending_target_country_code"></td>  
      </tr>
      <tr>
          <td>Dial code length</td>
          <td class="review review-dial_code_length"></td>  
          <td>line format</td>
          <td class="review review-line_format"></td>  
      </tr>
      <tr>
          <td>Name in message</td>
          <td class="review review-name_format"></td>  
          <td>Shortner mode</td>
          <td class="review review-msg_url_mode"></td>  
      </tr>
      <tr>
      <td>Total indexes</td>
      <td class="review review-total_indexes">0</td>  
      <td>Ndx roll in</td>
      <td class="review review-index_roll_in">0</td>  
  </tr>
  </tbody>
  </table>
</div> 
<script>
  const button = document.querySelector('.tag-collapsible');
  const content = document.querySelector('.content');

  button.addEventListener('click', () => {
    content.classList.toggle('open');
  });
</script>
@endsection