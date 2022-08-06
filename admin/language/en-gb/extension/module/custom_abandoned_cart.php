<?php

// Heading
$_['heading_title']    = 'Abandoned carts 1.0 (Custom version)';

// Text
$_['text_extension']   = 'Extensions';
$_['text_success']     = 'Success: You have modified category module!';
$_['text_edit']        = 'Edit Category Module';
$_['text_cron_wget']   = 'Command to add a call via Wget';

// Entry
$_['entry_status']                  = 'Status';
$_['entry_abandoned_cart_id']       = '№ Cart';
$_['entry_date_added']              = 'Date added';
$_['entry_order_statuses']          = 'Statuses of failure orders';
$_['entry_minutes']                 = 'Minutes';
$_['entry_copy_auto_cron']          = 'Command to call by cron (scheduler)';

// Help
$_['help_order_statuses']           = 'The list of statuses under which the abandoned cart will be fixed';
$_['help_minutes']                  = 'The number of whole minutes after which, from the moment of fixing the abandoned cart, an email will be sent to the email (required: integer)';
$_['help_copy_auto_cron']           = 'Convenient period editor for cron <a href="https://crontab.guru/" target="_blank">here</a>';

// Button
$_['button_list']               = 'Show list';

// Column
$_['column_cart_id']            = '№ Cart';
$_['column_email']              = 'Email';
$_['column_discord']            = 'Discord';
$_['column_character_name']     = 'Character name';
$_['column_character_server']   = 'Character server';
$_['column_date_added']         = 'Date added';
$_['column_action']             = 'Action';

// Command
$_['command_cron_wget']         = '* * * * * wget -q -O - %s/index.php?route=extension/module/custom_abandoned_cart/cron > /dev/null 2>&';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify category module!';