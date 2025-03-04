<?php namespace ThisPlugin\Models;

use ThisPlugin\Models\Model;


class Phone extends Model {
    public function getPotentialPhoneFields(){
        $like_clauses = array_map(function ($keyword) {
            return $this->wpdb->prepare("meta_key LIKE %s", str_replace('*', '%', $keyword));
        }, config('sms.field_keywords'));
    
        $sql = "SELECT DISTINCT meta_key 
                FROM {$this->wpdb->prefix}usermeta 
                WHERE (" . implode(' OR ', $like_clauses) . ")
                AND meta_key != 'mobile_number'";
    
        return $this->wpdb->get_results($sql);
    }
    
    

    public function update_field_names_from_form() {
        if (!isset($_POST['submit_phone_fields']) || empty($_POST['phone_columns'])) {
            return;
        }

        $selected_columns = array_map('sanitize_text_field', $_POST['phone_columns']);

        foreach ($selected_columns as $column) {
            $this->wpdb->update(
                $this->wpdb->usermeta,
                ['meta_key' => 'mobile_number'],
                ['meta_key' => $column]
            );
        }
        echo "<script>document.addEventListener('DOMContentLoaded',function(){setTimeout(()=>show_alert('Action completed, selected fields have been renamed to \"mobile_number\"','success'), 1500)})</script>";
    }
}