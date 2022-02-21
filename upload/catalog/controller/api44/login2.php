<?php
class ControllerApi44Login2 extends Controller {
	public function index() {
	    
	    $this->load->model('api44/api');
		$this->load->language('api44/login');

		$json = $api_info = array();
		$salt['salt'] = "";
		
        //check user & password
        $salt = $this->model_api44_api->getUserSalt($this->request->post['username']);
        if($salt != null){
            $userCheck = $this->model_api44_api->authUser($this->request->post['username'], $this->request->post['password'] , $salt['salt']);
            if($userCheck != null) {
           
            		// Login with API Key
            		if(isset($this->request->post['username'])) {
            		   	$api_info = $this->model_api44_api->login($this->request->post['username'], $this->request->post['key']);
            		} elseif(isset($this->request->post['key'])) {
            			$api_info = $this->model_api44_api->login('Default', $this->request->post['key']);
            		}
            
            		if ($api_info) {
            			// Check if IP is allowed
            			
            			$ip_data = array();
            	
            			$results = $this->model_api44_api->getApiIps($api_info['api_id']);
            	
            			foreach ($results as $result) {
            				$ip_data[] = trim($result['ip']);
            			}
            	
            			if (!in_array($this->request->server['REMOTE_ADDR'], $ip_data)) {
            				$json['error'] = sprintf($this->language->get('error_ip'), $this->request->server['REMOTE_ADDR']);
            			}				
            				
            			if (!$json) {
            				$json['success'] = $this->language->get('text_success');
            				
            				$session = new Session($this->config->get('session_engine'), $this->registry);
            				
            				$session->start();
            				
            				$this->model_api44_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);
            				
            				$session->data['api_id'] = $api_info['api_id'];
            				
            				// Create Token
            				$json['api_token'] = $session->getId();
            			} else {
            				$json['error'] = $json['error'] . $this->language->get('error_app');
            			}
            		} else {
            		    $json['error'] =  $this->language->get('error_app');
            		}
            } else {
                $json['error'] =  $this->language->get('error_cre');
            }
        } else {
            $json['error'] =  $this->language->get('error_cre');
        }
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
