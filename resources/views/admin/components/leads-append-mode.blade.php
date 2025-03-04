@if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
    <small class="pro-version-availability-notice">* Pro version only</small>
@endif
<select id="leads_append_mode" name="leads_input_source" title="Choose how to deal with multiple input sources" {{ $disabled }}>
    <option value="append" title="Selected input sources output will be appended to the existing list">Append to current list</option>
    <option value="replace" title="Current list content will be replaced with the selected input source output">Replace current list</option>
</select>