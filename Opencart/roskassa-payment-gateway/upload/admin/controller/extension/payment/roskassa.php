<?php
class ControllerExtensionPaymentRoskassa extends Controller {
	private $error = array();

	public function index() 
	{
		$this->load->language('extension/payment/roskassa');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) 
		{
			$this->model_setting_setting->editSetting('payment_roskassa', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_payment'] = $this->language->get('text_payment');
		$data['text_success'] = $this->language->get('text_success');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_pay'] = $this->language->get('text_pay');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_roskassa'] = $this->language->get('text_roskassa');
		$data['text_email_subject'] = $this->language->get('text_email_subject');
		$data['text_email_message1'] = $this->language->get('text_email_message1');
		$data['text_email_message2'] = $this->language->get('text_email_message2');
		$data['text_email_message3'] = $this->language->get('text_email_message3');
		$data['text_email_message4'] = $this->language->get('text_email_message4');
		$data['text_email_message5'] = $this->language->get('text_email_message5');
		$data['text_email_message6'] = $this->language->get('text_email_message6');
		
		$data['entry_url'] = $this->language->get('entry_url');
		$data['entry_merchant'] = $this->language->get('entry_merchant');
		$data['entry_security'] = $this->language->get('entry_security');
		$data['entry_order_wait'] = $this->language->get('entry_order_wait');
		$data['entry_order_success'] = $this->language->get('entry_order_success');
		$data['entry_order_fail'] = $this->language->get('entry_order_fail');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_log'] = $this->language->get('entry_log');
		$data['entry_list_ip'] = $this->language->get('entry_list_ip');
		$data['entry_admin_email'] = $this->language->get('entry_admin_email');
		$data['entry_merchant'] = $this->language->get('entry_merchant');
		$data['entry_status_url'] = $this->language->get('entry_status_url');
		$data['entry_success_url'] = $this->language->get('entry_success_url');
		$data['entry_fail_url'] = $this->language->get('entry_fail_url');
		
		$data['error_url'] = $this->language->get('error_url');
		$data['error_permission'] = $this->language->get('error_permission');
		$data['error_merchant'] = $this->language->get('error_merchant');
		$data['error_security'] = $this->language->get('error_security');

		$data['help_url'] = $this->language->get('help_url');
		$data['help_merchant'] = $this->language->get('help_merchant');
		$data['help_security'] = $this->language->get('help_security');
		$data['help_log'] = $this->language->get('help_log');
		$data['help_list_ip'] = $this->language->get('help_list_ip');
		$data['help_admin_email'] = $this->language->get('help_admin_email');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) 
		{
			$data['error_warning'] = $this->error['warning'];
		} 
		else 
		{
			$data['error_warning'] = '';
		}

		if (isset($this->error['url'])) 
		{
			$data['error_url'] = $this->error['url'];
		} 
		else 
		{
			$data['error_url'] = '';
		}
		
		if (isset($this->error['merchant'])) 
		{
			$data['error_merchant'] = $this->error['merchant'];
		} 
		else 
		{
			$data['error_merchant'] = '';
		}

		if (isset($this->error['security'])) 
		{
			$data['error_security'] = $this->error['security'];
		} 
		else 
		{
			$data['error_security'] = '';
		}

		if (isset($this->error['type'])) 
		{
			$data['error_type'] = $this->error['type'];
		} 
		else 
		{
			$data['error_type'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/roskassa', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/roskassa', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_roskassa_url']))
		{
			$data['payment_roskassa_url'] = $this->request->post['payment_roskassa_url'];
		} 
		else
		{
			if (!$this->config->get('payment_roskassa_url'))
			{
				$data['payment_roskassa_url'] = 'https://pay.roskassa.net/';
			}
			else
			{
				$data['payment_roskassa_url'] = $this->config->get('payment_roskassa_url');
			}
		}

		if (isset($this->request->post['payment_roskassa_merchant']))
		{
			$data['payment_roskassa_merchant'] = $this->request->post['payment_roskassa_merchant'];
		} 
		else 
		{
			$data['payment_roskassa_merchant'] = $this->config->get('payment_roskassa_merchant');
		}

		if (isset($this->request->post['payment_roskassa_security']))
		{
			$data['payment_roskassa_security'] = $this->request->post['payment_roskassa_security'];
		} 
		else 
		{
			$data['payment_roskassa_security'] = $this->config->get('payment_roskassa_security');
		}
		if (isset($this->request->post['payment_roskassa_order_wait_id']))
		{
			$data['payment_roskassa_order_wait_id'] = $this->request->post['payment_roskassa_order_wait_id'];
		}
		else 
		{
			if (!$this->config->get('payment_roskassa_order_wait_id'))
			{
				$data['payment_roskassa_order_wait_id'] = 1;
			}
			else
			{
				$data['payment_roskassa_order_wait_id'] = $this->config->get('payment_roskassa_order_wait_id');
			}
		}
        if (isset($this->request->post['payment_roskassa_status']))
        {
            $data['payment_roskassa_status'] = $this->request->post['payment_roskassa_status'];
        }
        else
        {
            if (!$this->config->get('payment_roskassa_status'))
            {
                $data['payment_roskassa_status'] = 1;
            }
            else
            {
                $data['payment_roskassa_status'] = $this->config->get('payment_roskassa_status');
            }
        }
		if (isset($this->request->post['payment_roskassa_order_success_id']))
		{
			$data['payment_roskassa_order_success_id'] = $this->request->post['payment_roskassa_order_success_id'];
		}
		else 
		{
			if (!$this->config->get('payment_roskassa_order_success_id'))
			{
				$data['payment_roskassa_order_success_id'] = 5;
			}
			else
			{
				$data['payment_roskassa_order_success_id'] = $this->config->get('payment_roskassa_order_success_id');
			}
		}
		
		if (isset($this->request->post['payment_roskassa_order_fail_id']))
		{
			$data['payment_roskassa_order_fail_id'] = $this->request->post['payment_roskassa_order_fail_id'];
		}
		else 
		{
			if (!$this->config->get('payment_roskassa_order_fail_id'))
			{
				$data['payment_roskassa_order_fail_id'] = 10;
			}
			else
			{
				$data['payment_roskassa_order_fail_id'] = $this->config->get('payment_roskassa_order_fail_id');
			}
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_roskassa_sort_order']))
		{
			$data['payment_roskassa_sort_order'] = $this->request->post['payment_roskassa_sort_order'];
		}
		else 
		{
			$data['payment_roskassa_sort_order'] = $this->config->get('payment_roskassa_sort_order');
		}
		
		if (isset($this->request->post['payment_roskassa_log_value']))
		{
			$data['payment_roskassa_log_value'] = $this->request->post['payment_roskassa_log_value'];
		} 
		else 
		{
			$data['payment_roskassa_log_value'] = $this->config->get('payment_roskassa_log_value');
		}

		if (isset($this->request->post['payment_roskassa_list_ip']))
		{
			$data['payment_roskassa_list_ip'] = $this->request->post['payment_roskassa_list_ip'];
		}
		else 
		{
            if (!$this->config->get('payment_roskassa_list_ip'))
            {
                $data['payment_roskassa_list_ip'] = '';
            }
            else
            {
                $data['payment_roskassa_list_ip'] = $this->config->get('payment_roskassa_list_ip');
            }
		}
		if (isset($this->request->post['payment_roskassa_admin_email']))
		{
			$data['payment_roskassa_admin_email'] = $this->request->post['payment_roskassa_admin_email'];
		} 
		else 
		{
			$data['payment_roskassa_admin_email'] = $this->config->get('payment_roskassa_admin_email');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/roskassa', $data));
	}

	protected function validate(){
		
		if (!$this->user->hasPermission('modify', 'extension/payment/roskassa'))
		{
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_roskassa_url'])
		{
			$this->error['url'] = $this->language->get('error_url');
		}

		if (!$this->request->post['payment_roskassa_merchant'])
		{
			$this->error['merchant'] = $this->language->get('error_merchant');
		}
		
		if (!$this->request->post['payment_roskassa_security'])
		{
			$this->error['security'] = $this->language->get('error_security');
		}

		return !$this->error;
	}
}