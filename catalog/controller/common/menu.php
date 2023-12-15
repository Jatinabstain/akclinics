<?php
namespace Opencart\Catalog\Controller\Common;
/**
 * Class Menu
 *
 * @package Opencart\Catalog\Controller\Common
 */
class Menu extends \Opencart\System\Engine\Controller {
	/**
	 * @return string
	 */
	public function index(): string {
		$this->load->language('common/menu');

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = [];

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = [];

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$filter_data = [
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					];

					$children_data[] = [
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'  => $this->url->link('product/category', 'language=' . $this->config->get('config_language') . '&path=' . $category['category_id'] . '_' . $child['category_id'])
					];
				}

				// Level 1
				$data['categories'][] = [
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'language=' . $this->config->get('config_language') . '&path=' . $category['category_id'])
				];
			}
		}
        $data['contact'] = $this->url->link('information/contact', 'language=' . $this->config->get('config_language'));
        $data['informations'] = [];

        $this->load->model('catalog/information');
        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['top']) {
                $external = false;
                $href = $this->url->link('information/information', 'language=' . $this->config->get('config_language') . '&information_id=' . $result['information_id']);
                if ($result['external_url']) {
                    $href = $result['external_url'];
                    $external = true;
                }
                $parent_data = [];
                $parent = $this->model_catalog_information->getInformations($result['information_id']);
                if ($parent) {
                    foreach ($parent as $value) {
                        $external = false;
                        $href = $this->url->link('information/information', 'language=' . $this->config->get('config_language') . '&information_id=' . $value['information_id']);
                        if ($value['external_url']) {
                            $href = $value['external_url'];
                            $external = true;
                        }
                        $parent_data[] = [
                            'information_id' => $value['information_id'],
                            'title' => $value['title'],
                            'href'  => $href,
                            'external'  => $external,
                        ];
                    }
                }
                if ((int) $result['information_id'] === 6) {
                    $this->load->model('catalog/product');
                    $filter_data = [
                        'filter_sub_category' => false,
                    ];
                    $products = $this->model_catalog_product->getProducts($filter_data);
                    $data['products'] = [];
                    if ($products) {
                        foreach ($products as $product) {
                            $parent_data[] = [
                                'product_id'  => $product['product_id'],
                                'title'        => $product['name'],
                                'href'        => $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $product['product_id']),
                                'external'  => false,
                            ];
                        }
                    }
                }
                if ((int) $result['information_id'] === 5 ) {
                    $external = false;
                }
                $data['informations'][] = [
                    'information_id' => $result['information_id'],
                    'title' => $result['title'],
                    'href'  => $href,
                    'external'  => $external,
                    'children'  => $parent_data,
                ];
            }
        }
		return $this->load->view('common/menu', $data);
	}
}
