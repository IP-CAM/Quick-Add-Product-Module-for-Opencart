<?php
class ControllerApi44Logout extends Controller {
	public function index() {
	    
	    $this->load->model('api44/api');
		$this->load->language('api44/logout');

		$json = array();
	

        //check user & password
        $log =  $this->model_api44_api->unsetApiToken($this->request->post['api_token']);
        
        if ($log == null){
            $json['error'] = $this->language->get('error');
        } else {
            $json['success'] = $this->language->get('text_success');
        }
        
        
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}