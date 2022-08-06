<?php

class ModelExtensionModuleCustomAbandonedCart extends Model
{
    public function getAbandonedCarts()
    {
        $query = $this->db->query("SELECT cac.`cart_id`, cac.`email`, cac.`discord`, cac.`character_name`, cac.`character_server`, cac.`send_email`, cac.`user_session_id`, cac.`date_added`, o.`order_status_id`, o.`language_id`, o.`total`, o.`currency_code`, o.`currency_value`, o.`date_modified` FROM " . DB_PREFIX . "custom_abandoned_cart cac LEFT JOIN " . DB_PREFIX . "order o ON (cac.`cart_id` = o.`order_id`) WHERE o.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->rows;
    }

    public function updateAbandonedCart($cart_id = 0)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "custom_abandoned_cart SET send_email = 1 WHERE cart_id = '" . (int)$cart_id . "'");
    }

    public function deleteAbandonedCart($cart_id = 0)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "custom_abandoned_cart WHERE cart_id = '" . (int)$cart_id . "'");
    }

    public function getDate()
    {
        $query = $this->db->query("SELECT NOW()");

        return $query->row;
    }
}