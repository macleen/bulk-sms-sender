@extends('admin.layouts.page-container')
@section('content')

    <div style="width:100%;display: flex; justify-content:center; align-items: center;margin-top:2%;">
        <div style="width:60%;border:gray thin solid;padding:15px; border-radius:8px;">
            @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
                <small class="pro-version-availability-notice">* Pro version only</small>
            @endif    
            <form id="#msform" method="post">
                <h2 style="color: white;">Select Fields to Rename to mobile_number</h2>
                <p style="padding: 5px 10px 20px 1px;color: #e7d8d86b;">
                    Field names that will be checked in this list will be permanently changed to mobile_number
                </p>


                @if ( empty( $fields ))
                    <p style="padding-left:0;color: #df4141;font-size: 1.2rem;">
                        No potential phone number fields found.
                        <div style="color: #aba2a2;font-size: 0.9rem;">That is also good news, this means you will have a fresh start</div>
                    </p>
                @else
                    <p style="padding: 5px 10px 20px 1px;color: #ffffffb3;">
                        The following field names are found in your database as potentially a dial number for a phone, please select which ones you want to have synced
                        <div style="color: #ffffffb3;">
                            <span style="background-color: #4be7c3;color: #111102;font-weight: 600;"> &nbsp;NB </span>&nbsp;&nbsp;
                            Unless it is not a MOBILE NUMBER, it is advised to sync all fields
                        </div>
                    </p>

                    <div style="margin-bottom:15px;">
                        @foreach ( $fields as $field )
                            <div style="margin-bottom: 8px;">
                                <label>
                                    <input type="checkbox" name="phone_columns[]" value="{{ esc_attr($field->meta_key) }}" {{ $disabled }}> {{ esc_html($field->meta_key) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div style="width:100%; display: flex; justify-content: center;">
                        <input type="submit" name="submit_phone_fields" value="Sync Selected Fields" class="button-primary" style="background-color:{{$input_bg_color}}  !important; color:{{$input_fg_color}} !important;width:50% !important;" {{ $disabled }}>
                    </div>
                @endif                        
            </form>
        </div>
    </div>

    <div style="width:100%;display: flex; justify-content:center; align-items: center;margin-top:2%;">
        <div style=" width:60%;margin-top: 15px;font-family: Arial, sans-serif; padding: 20px; border: 1px solid gray; border-radius: 8px;">
            <div style="margin:1vh 0 2vh 0;">
                <h2 style="color:#2dcbc0">What does this mean?</h2>
            </div>
            <br>
            <h3 style="color: #c3e4cf;">📲 Sync Phone Fields</h3>
            <p style="color: #b0a9a9; line-height: 1.6;">
                When you activate this plugin, a new user field called <strong>mobile_number</strong> is added. However, users registered before the plugin was installed might have phone numbers stored under different names like 
                <code>phone</code>, <code>tel</code>, or <code>contact</code>.
            </p>
        
            <h3 style="color: #c3e4cf; margin-top: 20px;">🔄 How does syncing work?</h3>
            <p style="color: #b0a9a9; line-height: 1.6;">
                The <strong>Sync Phone Fields</strong> feature scans your user database for any fields that may contain phone numbers. 
                You’ll be shown a list of these fields, and you can select the ones you want to sync.
            </p>
        
            <h3 style="color: #c3e4cf; margin-top: 20px;">✅ What happens after syncing?</h3>
            <p style="color: #b0a9a9; line-height: 1.6;">
                Once confirmed, all selected fields will be merged into the <strong>mobile_number</strong> field — ensuring your phone data is clean, consistent, and compatible with both old and new users.
            </p>
        
            <p style="color: #777; font-size: 0.9em; margin-top: 30px;">
                Need help? Feel free to reach out for support.
            </p>
        </div>
    </div>        

@endsection