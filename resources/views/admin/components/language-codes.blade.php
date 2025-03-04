@if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
    <small class="pro-version-availability-notice">* Pro version only</small>
@endif
<select id="language-indicator" name="language" title="Language code" {{ $disabled}}>
    <option value="">Language</option>
    <option value="EN" selected>EN</option>
    <option value="DE">DE</option>
    <option value="FR">FR</option>
    <option value="JP">JP</option>
    <option value="HE">IL</option>
    <option value="PT">PT</option>
    <option value="ES">ES</option>
    <option value="IT">IT</option>
    <option value="NL">NL</option>
    <option value="SE">SV</option>
    <option value="DK">DA</option>
    <option value="RU">RU</option>
</select>