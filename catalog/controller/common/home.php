<?php
namespace Opencart\Catalog\Controller\Common;
/**
 * Class Home
 *
 * @package Opencart\Catalog\Controller\Common
 */
class Home extends \Opencart\System\Engine\Controller {
	/**
	 * @return void
	 */
	public function index(): void {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		$data['blogs'][] = [
		    'name' => '12 Most Powerful Natural DHT Blockers that Stop Hair Loss',
		    'href' => 'https://akclinics.org/blog/12-most-powerful-natural-dht-blockers-that-stop-hair-loss/',
		    'image' => 'https://akclinics.org/blog/wp-content/uploads/2017/09/Natural-DHT-blockers-768x768.png',
		    'author' => 'AK Clinics | August 25, 2022',
        ];
        $data['blogs'][] = [
            'name' => 'Medications & Drugs That Accelerate Hair Loss',
            'href' => 'https://akclinics.org/blog/medications-drugs-that-accelerate-hair-loss/',
            'image' => 'https://akclinics.org/blog/wp-content/uploads/2019/03/medicine-blog.jpg',
            'author' => 'AK Clinics | July 19, 2022',
        ];
        $data['blogs'][] = [
            'name' => 'Antioxidants: The Secret Protector of Your Hair',
            'href' => 'https://akclinics.org/blog/benefits-of-anti-oxidants-for-hair/',
            'image' => 'https://akclinics.org/blog/wp-content/uploads/2018/06/Antioxidants.png',
            'author' => 'AK Clinics | June 25, 2018',
        ];
        $data['blogs'][] = [
            'name' => 'Everything You Need to Know About Hair Loss in Men',
            'href' => 'https://akclinics.org/blog/everything-you-need-to-know-about-hair-loss-in-men/',
            'image' => 'https://akclinics.org/blog/wp-content/uploads/2018/06/hair-loss-treatments.png',
            'author' => 'AK Clinics | June 23, 2018',
        ];

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['hair_test_link'] = $this->url->link('extension/hair_test/module/hair_test');
		$this->response->setOutput($this->load->view('common/home', $data));
	}
}