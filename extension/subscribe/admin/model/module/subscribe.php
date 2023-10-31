<?php
namespace Opencart\Admin\Model\Extension\Subscribe\module;
class Subscribe extends \Opencart\System\Engine\Model
{

    public function install()
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'subscribers` (
			`subscriber_id` int(11) NOT NULL AUTO_INCREMENT,
			`status` tinyint(1) NOT NULL,
			`email` varchar(255) NULL,
			`ip_address` varchar(255) NULL,
			`user_agent` TEXT NULL,
			`date_added` datetime NOT NULL,
			`date_modified` datetime NOT NULL,
			PRIMARY KEY (`subscriber_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		');

        $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'callback_contact` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(255) NULL,
			`age` varchar(255) NULL,
			`email` varchar(255) NULL,
			`phone` varchar(255) NULL,
			`ip_address` varchar(255) NULL,
			`user_agent` TEXT NULL,
			`date_added` datetime NOT NULL,
			`date_modified` datetime NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		');
    }

    public function uninstall()
    {
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getSubscribers(array $data = []): array {
        $sql = "SELECT * FROM `" . DB_PREFIX . "subscribers`";
        $sort_data = [
            'email',
            'date_added',
            'status',
        ];
        if (isset($data['sort']) && in_array($data['sort'], $sort_data, true)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY `sort_order`";
        }

        if (isset($data['order']) && ($data['order'] === 'DESC')) {
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
        return $this->db->query($sql)->rows;
    }

    /**
     * @return int
     */
    public function getTotalSubscribers(): int
    {
        $query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "subscribers`");

        return (int)$query->row['total'];
    }

    public function deleteSubscriber(int $subscriber_id): void
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "subscribers` WHERE `subscriber_id` = '" . (int)$subscriber_id . "'");
    }
}
