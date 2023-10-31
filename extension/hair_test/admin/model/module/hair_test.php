<?php
namespace Opencart\Admin\Model\Extension\HairTest\module;
class HairTest extends \Opencart\System\Engine\Model
{

    public function install()
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'hair_test_category` (
			`category_id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(255) NULL,
			`status` tinyint(1) NULL,
			`sort_order` int(11) NULL,
			`date_added` datetime NOT NULL,
			`date_modified` datetime NOT NULL,
			PRIMARY KEY (`category_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		');
    }

    public function uninstall(): void
    {
		
    }
}
