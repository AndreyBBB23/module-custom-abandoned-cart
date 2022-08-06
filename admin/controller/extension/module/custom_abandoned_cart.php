<?php

class ControllerExtensionModuleCustomAbandonedCart extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/custom_abandoned_cart');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_custom_abandoned_cart', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/custom_abandoned_cart', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/custom_abandoned_cart', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_custom_abandoned_cart_status'])) {
            $data['module_custom_abandoned_cart_status'] = $this->request->post['module_custom_abandoned_cart_status'];
        } else {
            $data['module_custom_abandoned_cart_status'] = $this->config->get('module_custom_abandoned_cart_status');
        }

        if (isset($this->request->post['module_custom_abandoned_cart_minutes'])) {
            $data['module_custom_abandoned_cart_minutes'] = $this->request->post['module_custom_abandoned_cart_minutes'];
        } else {
            $data['module_custom_abandoned_cart_minutes'] = $this->config->get('module_custom_abandoned_cart_minutes');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = array();

        if (isset($this->request->post['module_custom_abandoned_cart_order_status'])) {
            $order_statuses = $this->request->post['module_custom_abandoned_cart_order_status'];
        } else {
            $order_statuses = $this->config->get('module_custom_abandoned_cart_order_status');
        }

        foreach ($order_statuses as $order_status_id) {
            $order_status_info = $this->model_localisation_order_status->getOrderStatus($order_status_id);

            if ($order_status_info) {
                $data['order_statuses'][] = array(
                    'order_status_id' => $order_status_info['order_status_id'],
                    'name' => $order_status_info['name']
                );
            }
        }

        if (isset($this->request->server['HTTP_HOST'])) {
            $url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'];
        } else {
            $url = '';
        }

        $data['command_cron_wget'] = sprintf($this->language->get('command_cron_wget'), $url);

        $data['user_token'] = $this->session->data['user_token'];
        $data['list'] = $this->url->link('extension/module/custom_abandoned_cart/showList', 'user_token=' . $this->session->data['user_token'], true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/custom_abandoned_cart', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/custom_abandoned_cart')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function install()
    {
        $this->load->model('extension/module/custom_abandoned_cart');
        $this->model_extension_module_custom_abandoned_cart->install();
    }

    public function uninstall()
    {
        $this->load->model('extension/module/custom_abandoned_cart');
        $this->model_extension_module_custom_abandoned_cart->uninstall();
    }

    public function showList()
    {
        $this->load->language('extension/module/custom_abandoned_cart');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/custom_abandoned_cart');

        $this->getList();
    }

    protected function getList()
    {
        if (isset($this->request->get['filter_cart_id'])) {
            $filter_cart_id = $this->request->get['filter_cart_id'];
        } else {
            $filter_cart_id = '';
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.cart_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_cart_id'])) {
            $url .= '&filter_cart_id=' . $this->request->get['filter_cart_id'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/custom_abandoned_cart', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['cancel'] = $this->url->link('extension/module/custom_abandoned_cart', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $data['delete'] = str_replace('&amp;', '&', $this->url->link('extension/module/custom_abandoned_cart/delete', 'user_token=' . $this->session->data['user_token'] . $url, true));

        $data['orders'] = array();

        $filter_data = array(
            'filter_cart_id' => $filter_cart_id,
            'filter_date_added' => $filter_date_added,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $order_total = $this->model_extension_module_custom_abandoned_cart->getTotalAbandonedCarts($filter_data);

        $results = $this->model_extension_module_custom_abandoned_cart->getAbandonedCarts($filter_data);

        foreach ($results as $result) {
            $data['abandoned_carts'][] = array(
                'cart_id' => $result['cart_id'],
                'email' => $result['email'],
                'discord' => $result['discord'],
                'character_name' => $result['character_name'],
                'character_server' => $result['character_server'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'view' => $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['cart_id'] . $url, true),
                'edit' => $this->url->link('sale/order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['cart_id'] . $url, true)
            );
        }

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_cart_id'])) {
            $url .= '&filter_cart_id=' . $this->request->get['filter_cart_id'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_order'] = $this->url->link('extension/module/custom_abandoned_cart', 'user_token=' . $this->session->data['user_token'] . '&sort=o.cart_id' . $url, true);
        $data['sort_date_added'] = $this->url->link('extension/module/custom_abandoned_cart', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_cart_id'])) {
            $url .= '&filter_cart_id=' . $this->request->get['filter_cart_id'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/custom_abandoned_cart', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

        $data['filter_cart_id'] = $filter_cart_id;
        $data['filter_date_added'] = $filter_date_added;

        $data['sort'] = $sort;
        $data['order'] = $order;

        // API login
        $data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

        // API login
        $this->load->model('user/api');

        $api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

        if ($api_info && $this->user->hasPermission('modify', 'extension/module/custom_abandoned_cart')) {
            $session = new Session($this->config->get('session_engine'), $this->registry);

            $session->start();

            $this->model_user_api->deleteApiSessionBySessonId($session->getId());

            $this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);

            $session->data['api_id'] = $api_info['api_id'];

            $data['api_token'] = $session->getId();
        } else {
            $data['api_token'] = '';
        }

        $data['delete_abandoned_carts'] = str_replace('&amp;', '&', $this->url->link('extension/module/custom_abandoned_cart/deleteAbandonedCarts', 'user_token=' . $this->session->data['user_token'] . $url, true));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/custom_abandoned_cart_list', $data));
    }

    public function deleteAbandonedCarts()
    {
        $this->load->language('extension/module/custom_abandoned_cart');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/custom_abandoned_cart');

        $url = '';

        if (isset($this->request->get['filter_cart_id'])) {
            $url .= '&filter_cart_id=' . $this->request->get['filter_cart_id'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->post['selected']) && $this->validate()) {
            foreach ($this->request->post['selected'] as $order_id) {
                $this->model_extension_module_custom_abandoned_cart->deleteAbandonedCart($order_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');
        }

        $this->response->redirect($this->url->link('extension/module/custom_abandoned_cart/showList', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }

    public function autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('localisation/order_status');

            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 5;
            }

            $filter_data = array(
                'filter_name' => $filter_name,
                'start' => 0,
                'limit' => $limit
            );

            $results = $this->model_localisation_order_status->getOrderStatuses($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'order_status_id' => $result['order_status_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
