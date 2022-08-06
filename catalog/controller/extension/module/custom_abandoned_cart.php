<?php

class ControllerExtensionModuleCustomAbandonedCart extends Controller {

    public function index() {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(
            [
                "success" => true,
                "code" => 200,
                "message" => 'Only for cronJob',
                "data" => []
            ]
        ));
    }

    public function cron()
    {
        $this->load->model('setting/setting');
        $this->load->model('extension/module/custom_abandoned_cart');
        $this->load->model('checkout/order');

        $module_info = $this->model_setting_setting->getSetting('module_custom_abandoned_cart');

        if ((int)$module_info['module_custom_abandoned_cart_status'] === 1) {
            $results = $this->model_extension_module_custom_abandoned_cart->getAbandonedCarts();
            $order_status_ids = array();

            if (isset($module_info['module_custom_abandoned_cart_order_status']) && !empty($module_info['module_custom_abandoned_cart_order_status'])) {
                foreach ($module_info['module_custom_abandoned_cart_order_status'] as $order_status_id) {
                    $order_status_ids[] = 0;
                    $order_status_ids[] = (int)$order_status_id;
                }
            }

            if ($results) {
                foreach ($results as $abandoned_cart) {
                    $current_time = $this->model_extension_module_custom_abandoned_cart->getDate();
                    $minutes = $this->getCountMinutes($current_time['NOW()'], $abandoned_cart['date_modified']);

                    if ($minutes > (int)$module_info['module_custom_abandoned_cart_minutes'] && in_array((int)$abandoned_cart['order_status_id'], $order_status_ids) && (int)$abandoned_cart['send_email'] !== 1) {
                        if (!empty($abandoned_cart['email'])) {
                            $cart_products = $this->model_checkout_order->getOrderProducts($abandoned_cart['cart_id']);
                            $this->sendMail($abandoned_cart, $cart_products);
                            $this->model_extension_module_custom_abandoned_cart->updateAbandonedCart($abandoned_cart['cart_id']);
                        }
                    }

                    if (!in_array((int)$abandoned_cart['order_status_id'], $order_status_ids)) {
                        $this->model_extension_module_custom_abandoned_cart->deleteAbandonedCart($abandoned_cart['cart_id']);
                    }
                }
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(
                [
                    "success" => true,
                    "code" => 200,
                    "message" => 'The cronJob was successfully completed',
                    "data" => []
                ]
            ));
        }
    }

    public function sendMail($request = array(), $products = array())
    {
        $this->load->language('extension/module/custom_abandoned_cart');

        $data['text_heading'] = $this->language->get('text_heading');
        $data['text_title'] = sprintf($this->language->get('text_title'), $request['discord']);
        $data['text_description'] = $this->language->get('text_description');
        $data['text_cart_total'] = $this->language->get('text_cart_total');
        $data['total'] = $request['total'];

        $data['button_submit'] = sprintf($this->language->get('button_submit'), $this->url->link('checkout/cart', 'cart_id=' . $request['user_session_id']));

        // Products
        $data['products'] = array();

        foreach ($products as $product) {
            $data['products'][] = array(
                'name'     => $product['name'],
                'model'    => $product['model'],
                'quantity' => $product['quantity'],
                'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $request['currency_code'], $request['currency_value']),
                'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $request['currency_code'], $request['currency_value'])
            );
        }

        $mail = new Mail();
        $mail->protocol = $this->config->get('config_mail_protocol');
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

        $mail->setTo($request['email']);
        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
        $mail->setSubject(html_entity_decode($this->language->get('text_heading'), ENT_QUOTES, 'UTF-8'));
        $mail->setHtml($this->load->view('mail/custom_abandoned_cart', $data));
        $mail->send();
    }

    function getCountMinutes($d1, $d2): int
    {
        $date1 = strtotime($d1);
        $date2 = strtotime($d2);

        $seconds = abs($date1 - $date2);

        return floor($seconds / 60);
    }
}