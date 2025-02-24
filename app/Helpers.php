<?php

if(!function_exists('status_formatted')) {
    
    function status_formatted($status) {

        return $status ? __('Approved') : __('Declined');
    }
}