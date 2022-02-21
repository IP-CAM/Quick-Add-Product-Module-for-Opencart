<?php
class ControllerApi44AddProduct extends Controller {
	public function index() {

		$this->load->language('api44/addProduct');
		$this->load->model('api44/api');

		$json = array();
		$product = array();
		$productI = array();


    if ($this->request->post['api_token'] != null){
		if (isset($this->request->files)) {

			
			if (isset($this->request->get['directory'])) {
				$directory = rtrim(DIR_IMAGE . 'catalog/' . $this->request->get['directory'], '/');
			} else {
				$directory = DIR_IMAGE . 'catalog';
			}

			// Check its a directory
			if (!is_dir($directory) || substr(str_replace('\\', '/', realpath($directory)), 0, strlen(DIR_IMAGE . 'catalog')) != str_replace('\\', '/', DIR_IMAGE . 'catalog')) {
				$json['error'] = $this->language->get('error_directory');
			}

			if (!$json) {
				
					
					$file[] = array(
						'name'     => $this->request->files['file']['name'],
						'type'     => $this->request->files['file']['type'],
						'tmp_name' => $this->request->files['file']['tmp_name'],
						'error'    => $this->request->files['file']['error'],
						'size'     => $this->request->files['file']['size']
					);
					
				


					if (is_file($file[0]['tmp_name'])) {
						// Sanitize the filename
						$filename = basename(html_entity_decode($file[0]['name'], ENT_QUOTES, 'UTF-8'));

						// Validate the filename length
						if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
							$json['error'] = $this->language->get('error_filename');
						}

						// Allowed file extension types
						$allowed = array(
							'jpg',
							'jpeg',
							'gif',
							'png'
						);

						if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
							$json['error'] = $this->language->get('error_filetype');
						}

						// Allowed file mime types
						$allowed = array(
						    'image/*',
						    'image/jpg',
							'image/jpeg',
							'image/pjpeg',
							'image/png',
							'image/x-png',
							'image/gif'
						);

						if (!in_array($file[0]['type'], $allowed)) {
							$json['error'] = $this->language->get('error_filetype');
						}

						if ($file[0]['size'] > $this->config->get('config_file_max_size')) {
							$json['error'] = $this->language->get('error_filesize');
						}

						// Return any upload error
						if ($file[0]['error'] != UPLOAD_ERR_OK) {
							$json['error'] = $this->language->get('error_upload_' . $file['error']);
						}
					} else {
						$json['error'] = $this->language->get('error_upload');
					}

					if (!$json) {
						move_uploaded_file($file[0]['tmp_name'], $directory . '/' . $filename);
					}
				
			}

			
			$product['imagename'] = $filename;
		}
		
		
		//name
		if(isset($this->request->post['product_name'])) {
			$product['product_description']['1']['name'] = $this->request->post['product_name'];
			$product['product_description']['2']['name'] = $this->request->post['product_name'];
		}

		//price
		if(isset($this->request->post['product_price'])) {
			$product['price'] = $this->request->post['product_price'];
		}

		//quantity
		if(isset($this->request->post['product_quantity'])) {
			$product['quantity'] = $this->request->post['product_quantity'];
		}
		
		//model
		if(isset($this->request->post['model'])) {
			$product['model'] = $this->request->post['model'];
		}

		//status
		$product['status'] = false;


		//store		
		$product['product_store'][0] = 0;
		
		//image
		if($product['imagename']) {
			$product['image'] = "catalog/" . $product['imagename'];
		}
		
		// $product['model'] = "apimodel";
		// $product['price'] = "9021437051";
		// $product['product_description']['1']['name'] = "apiName";
		// $product['product_description']['2']['name'] = "apiName";
		// $product['product_description']['1']['tag'] = "apiTag";
		// $product['product_description']['2']['tag'] = "apiTag";
		$authenticated = $this->model_api44_api->checkSession($this->request->post['api_token']);
		
		//if api user authenticated
		if($authenticated != null) {
		    $productI['pID'] = $this->model_api44_api->addProduct($product);
		    // $cproduct = array();
            // $cproduct['customer_id'] = 1;
            // $cproduct['product_id'] = $productI['pID'] ;
            // $cproduct['price'] = $this->request->post['product_price'] ;
            // $cproduct['quantity'] = $this->request->post['product_quantity'] ;
            // $productI['pIDC'] = $this->model_api44_api->addProductToSeller($cproduct);
            // if ($productI['pID'] && $productI['pIDC']) {
    		
    		if ($productI['pID']) {
    			$json['success'] = $this->language->get('text_success');
    			//$json['product_id'] = $productI['pID'];
    		} else {
    			$json['error'] = $this->language->get('error_product');
    		}
		} else {
		    $json['error'] = "api token error";
		}

		
        
    } else {
        //if is set api_token = null
        $json['error'] = "api token is null!";
    }
		
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
