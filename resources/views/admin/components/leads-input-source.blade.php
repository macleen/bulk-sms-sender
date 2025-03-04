@if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
    <small class="pro-version-availability-notice">* Pro version only</small>
@endif
<select id="leads_input_source" name="leads_input_source" title="Choose from where to get the contact list" {{ $disabled }}>
    <option value="" data-info="Select an input source or copy and paste">Select a leads input source</option>
    <option value="" data-info="Paste a block of text in the destined area, 1 lead per line">I will paste a list</option>
    <option value="users" data-info="All Wordpress users will be pulled in">Inject all WP users</option>
    <option value="subscriber" data-info="All Wordpress users will be pulled in">Inject all WP subscribers</option>
    <option value="editor" data-info="All Wordpress users will be pulled in">Inject all WP editors</option>
    <option value="author" data-info="All Wordpress users will be pulled in">Inject all WP authors</option>
    <option value="shop_manager" data-info="All Wordpress users will be pulled in">Inject all WP shop_managers</option>
    <option value="moderator" data-info="All Wordpress moderators will be pulled in">Inject all WP moderators</option>
    <option value="administrator" data-info="The list will contain Wordpress admins only">Inject all WP admins</option>
    <option value="super_admin" data-info="All Wordpress users will be pulled in">Inject all WP super-admins</option>
    <option value="customer" data-info="You must have WOO-COMMERCE installed">Inject all WC users</option>
</select>