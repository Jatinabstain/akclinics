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
	
}
