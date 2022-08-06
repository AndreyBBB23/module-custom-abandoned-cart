<?php

// Heading
$_['heading_title']    = 'Брошенные корзины 1.0 (Кастомная версия)';

// Text
$_['text_extension']   = 'Модули';
$_['text_success']     = 'Настройки успешно изменены!';
$_['text_edit']        = 'Редактирование';
$_['text_cron_wget']   = 'Команда добавления вызова через Wget';

// Entry
$_['entry_status']                  = 'Статус';
$_['entry_abandoned_cart_id']       = '№ Корзины';
$_['entry_date_added']              = 'Дата добавления';
$_['entry_order_statuses']          = 'Статусы неуспешных заказов';
$_['entry_minutes']                 = 'Отсчет времени в минутах';
$_['entry_copy_auto_cron']          = 'Команда для вызова по cron (планировщик)';

// Help
$_['help_order_statuses']           = 'Список статусов при которых брошенная корзина будет зафиксирована';
$_['help_minutes']                  = 'Кол-во целых минут, после которых с момента фиксирования брошенной корзины будет отправлено письмо на email (обязательно: целое число)';
$_['help_copy_auto_cron']           = 'Удобный редактор периода для cron <a href="https://crontab.guru/" target="_blank">здесь</a>';

// Button
$_['button_list']               = 'Показать список';

// Column
$_['column_cart_id']            = '№ Корзины';
$_['column_email']              = 'Email';
$_['column_discord']            = 'Дискорд';
$_['column_character_name']     = 'Имя персонажа';
$_['column_character_server']   = 'Сервер';
$_['column_date_added']         = 'Дата добавления';
$_['column_action']             = 'Действие';

// Command
$_['command_cron_wget']         = '* * * * * wget -q -O - %s/index.php?route=extension/module/custom_abandoned_cart/cron > /dev/null 2>&';

// Error
$_['error_permission'] = 'У вас недостаточно прав для внесения изменений!';