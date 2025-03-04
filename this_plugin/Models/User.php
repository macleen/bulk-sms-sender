<?php

namespace ThisPlugin\Models;

use ThisPlugin\Models\Model;

class User extends Model {

    protected array $available_roles = [
        'users',
        'administrator',
        'super_admin',
        'moderator',
        'author',
        'contributor',
        'subscriber',
        'shop_manager',
        'editor',
        'customer',
    ];

    public function member_list_with_phones($role): array {

        if (in_array($role, $this->available_roles)) {
            $msg_complement = (!empty($role) ? "with Role: $role" : '').' having a valid mobile number';

            $res = match ($role) {
                'users'  => $this->users_with_role(),
                default  => $this->users_with_role($role ),
            };

            $this->result['data'] = $res;

            if (empty($res)) {
                $this->result = [
                    'ok'      => false,
                    'status'  => 404,
                    'message' => "No users found $msg_complement",
                    'data'    => [],
                ];
            }
        } else {
            $this->result = [
                'ok'      => false,
                'status'  => 400,
                'message' => "Invalid user role",
                'data'    => [],
            ];
        }

        return $this->result;
    }

    protected function users(): array {
        // Get all users
        return $this->distructure(get_users([
            'orderby' => 'display_name',
            'order'   => 'ASC',
        ]));
    }

    protected function users_with_role( ?string $role = null ): array  {
        $is_woocommerce = $role === 'customer';
        // Get users with a specific role
        $args = [
            'orderby' => 'display_name',
            'order'   => 'ASC',
            'fields'  => 'all_with_meta',
        ];

        if (!empty($role)) {            
            $args['role'] = $role;
        } else $args['role__not_in'] = $this->available_roles;

        return $this->distructure(get_users($args), $is_woocommerce);
    }



    protected function distructure($members, bool $is_woocommerce = false): array {
        $res         = [];
        $seen_phones = [];
    

        foreach ( $members as $user_id => $member ) {
            $entry = [
                'user_name' => $member->data->display_name,
                'email'     => $member->data->user_email,
            ];
    
            if ($is_woocommerce) {
                // Fetch billing details
                $billing_address  = get_user_meta( $user_id, 'billing_address_1', true );
                $billing_phone    = get_user_meta( $user_id, 'billing_phone', true );
    
                // Fetch shipping details
                $shipping_address = get_user_meta( $user_id, 'shipping_address_1', true );
                $shipping_phone   = get_user_meta( $user_id, 'shipping_phone', true );

                // Normalize phone numbers by removing non-digit characters
                $billing_phone    = $billing_phone  ? preg_replace('~\D+~', '', $billing_phone) : '';
                $shipping_phone   = $shipping_phone ? preg_replace('~\D+~', '', $shipping_phone) : '';

                // Include billing details if phone number is present and not seen before
                if ($billing_phone && !isset($seen_phones[$billing_phone])) {
                    $entry['mobile'] = $billing_phone;
                    if ($billing_address) {
                        $entry['address'] = $billing_address;
                    }
                    $seen_phones[$billing_phone] = true;
                }

                // Include shipping details if phone number is present, different from billing phone, and not seen before
                if ( $shipping_phone && $shipping_phone !== $billing_phone && !isset($seen_phones[$shipping_phone])) {
                     $entry['mobile'] = $shipping_phone;
                     if ( $shipping_address ) {
                          $entry['address'] = $shipping_address;
                     }
                     $seen_phones[ $shipping_phone ] = true;
                }
            } else {
                // For non-WooCommerce users
                $address = get_user_meta( $member->data->ID, 'address', true );
                $mobile  = get_user_meta( $member->data->ID, 'mobile_number', true );
                $mobile  = $mobile ? \preg_replace('~\D+~', '', $mobile) : ''; // Normalize phone number

                if ( $mobile && !isset( $seen_phones[ $mobile ])) {     // Include details if phone number is present and not seen before
                     $entry['mobile'] = $mobile;
                     if ( $address ) {
                          $entry['address'] = $address;
                     }
                     $seen_phones[$mobile] = true;
                }
            }

            // Only include entry if a mobile number is present
            if ( isset( $entry['mobile'] )) {
                 // Format the result as: mobile; user_name; email
                 $res[] = "{$entry['mobile']}; {$entry['user_name']}; {$entry['email']}" . 
                           (isset($entry['address']) ? "; {$entry['address']}" : "");
            }
        }
        return $res;
    }
}