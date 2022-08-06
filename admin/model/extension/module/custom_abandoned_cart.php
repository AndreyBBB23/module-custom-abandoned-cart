<?php

class ModelExtensionModuleCustomAbandonedCart extends Model
{
    public function install()
    {
        $this->db->query("
			CREATE TABLE `" . DB_PREFIX . "custom_abandoned_cart` (
				`abandoned_cart_id` INT(11) NOT NULL AUTO_INCREMENT,
				`cart_id` int(11) NOT NULL,
				`email` varchar(96) NOT NULL,
				`discord` varchar(32) NOT NULL,
				`character_name` varchar(32) NOT NULL,
				`character_server` varchar(32) NOT NULL,
				`send_email` int DEFAULT 0,
				`user_session_id` varchar(32) NOT NULL,
				`date_added` datetime NOT NULL,
				PRIMARY KEY (`abandoned_cart_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
    }

    public function uninstall()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "custom_abandoned_cart`");
    }

    public function getAbandonedCarts($data = array())
    {
        $sql = "SELECT cac.cart_id, cac.email, cac.discord, cac.character_name, cac.character_server, cac.date_added FROM `" . DB_PREFIX . "custom_abandoned_cart` cac";

        if (!empty($data['filter_cart_id'])) {
            $sql .= " WHERE cac.cart_id = '" . (int)$data['filter_cart_id'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $operator = !empty($data['filter_cart_id']) ? 'AND' : 'WHERE';
            $sql .= " $operator DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        $sort_data = array(
            'cac.cart_id',
            'cac.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY cac.cart_id";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalAbandonedCarts($data = array())
    {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "custom_abandoned_cart`";

        if (!empty($data['filter_cart_id'])) {
            $sql .= " WHERE cart_id = '" . (int)$data['filter_cart_id'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $operator = !empty($data['filter_cart_id']) ? 'AND' : 'WHERE';
            $sql .= " $operator DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function deleteAbandonedCart($cart_id)
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "custom_abandoned_cart` WHERE cart_id = '" . (int)$cart_id . "'");
    }
}
