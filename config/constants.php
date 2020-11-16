<?php
return [
    'email_template'=>[
        'register'=>'Emails/register',
        'login'=>'Emails/login',
        'reset_password'=>'Emails/reset_password',
    ],
    'email_heading'=>[
        'register'=>'Port Logistics Account Creation',
        'login'=>'Port Logistics Account Creation',
        'reset_password'=>'Port Logistics Password Reset',
    ],
    'email_token_expiry_time'=>3600,
    'email_resend_time'=>60,
    'app_salt' => '!A%C*F-J',
    'default_truck_company' => 1,
    'location_plot' => 'P',
    'location_berth' => 'B',
    'api_version' => 1,
    'cache_url' => array(
        0 => 'vessel',
        1 => 'cargo',
        2 => 'user',
        3 => 'location',
        4 => 'challan',
        5 => 'role',
        6 => 'truck',
        7 => 'truckc',
        8 => 'consignee',
        9 => 'org',
        10 => 'dash',
        11 => 'department',
       // 12 => 'rolep',
        13 => 'planning',
        14 => 'challan',
        15 => 'cachekey'
    ),   
    'cache_time' => 100000,  //in seconds
    'exception_msg' => 'Something Went Wrong!',
    'empty_err_msg' => 'No Record Found!',
    'id_validation_rules' => 'required|integer|gt:0',
    'plot_validation_msg' => 'Please provide a correct plot no.'
];