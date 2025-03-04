@if ($is_registration)
    <h4>Additional Information</h4>
@else
    <h2>Additional Information</h2>
@endif        
@php
    $padding = $is_registration ? 'padding:0;' : '';
@endphp
<table class="form-table">
    <tr>
        <th style="{{$padding}}">
            <label for="mobile_number">
                Mobile Number 
                <span class="description" style="font-size:10px;">(required)</span>
            </label>
        </th>
        @if ($is_registration)
            </tr>
            <tr>
        @endif        
        <td style="{{$padding}}">
            <input type="text" name="mobile_number" id="mobile_number" value="{{ $mobile_number }}" class="regular-text" required><br>
            <small style="font-style:italic;color:#0a0a0b8f;">This field is appended by Bulk-Sms-Sender</span>
        </td>
    </tr>
</table>
<br>