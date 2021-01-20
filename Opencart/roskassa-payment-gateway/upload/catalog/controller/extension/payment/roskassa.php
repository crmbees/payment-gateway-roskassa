<?php
class ControllerExtensionPaymentRoskassa extends Controller
{	
	public function index() 
	{
		$data['button_confirm'] = $this->language->get('button_confirm');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data['action'] = $this->config->get('payment_roskassa_url');
		$data['m_shop'] = $this->config->get('payment_roskassa_merchant');
		$data['m_orderid'] = $this->session->data['order_id'];
		$data['m_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$data['m_curr'] = strtoupper($order_info['currency_code']);

		$data1 = array(
			'shop_id'=>$data['m_shop'],
			'amount'=>$data['m_amount'],
			'currency'=>$data['m_curr'],
			'order_id'=>$data['m_orderid'],
			'test'=>1
		);
		ksort($data1);
		$str = http_build_query($data1);
		$sign = md5($str . $this->config->get('payment_roskassa_security'));
		
		$data['sign'] = $sign;
		
		$this->model_checkout_order->addOrderHistory($data['m_orderid'], $this->config->get('payment_roskassa_order_wait_id'));

		return $this->load->view('extension/payment/roskassa', $data);
	}

	public function status()
	{
		$request = $this->request->request;
		
		if (isset($request["sign"]))
		{
			$err = false;
			$message = '';
			$this->load->language('extension/payment/roskassa');

			// запись логов

			$log_text =
			"--------------------------------------------------------\n" .
			"shop               " . $request['shop_id'] . "\n" .
			"amount             " . $request['amount'] . "\n" .
			"operation id       " . $request['intid'] . "\n" .
			"order id           " . $request['order_id'] . "\n" .
			"currency           " . $request['currency'] . "\n" .
			"sign               " . $request['sign'] . "\n\n";
			
			$log_file = $this->config->get('payment_roskassa_log_value');
			
			if (!empty($log_file))
			{
				file_put_contents($_SERVER['DOCUMENT_ROOT'] . $log_file, $log_text, FILE_APPEND);
			}

			// проверка ip

			$sIP = str_replace(' ', '', $this->config->get('payment_roskassa_list_ip'));
			$valid_ip = true;
			if ( ! empty( $sIP ) ) {

				$ip_filter_arr = explode(',', $sIP);
				$this_ip  = (isset($_SERVER['HTTP_X_REAL_IP'])) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR'];

				foreach ( $ip_filter_arr as $key => $value ) {
					$ip_filter_arr[ $key ] = ip2long( $value );
				}

				if ( ! in_array( ip2long($this_ip), $ip_filter_arr)) {
					$valid_ip = false;
				}

			}

			if (!$valid_ip)
			{
				$message .= $this->language->get('text_email_message4') . "\n" .
				$this->language->get('text_email_message5') . $sIP . "\n" .
				$this->language->get('text_email_message6') . $_SERVER['REMOTE_ADDR'] . "\n";
				$err = true;
			}

			$data = array(
				'shop_id'=>$request['shop_id'],
				'amount'=>$request['amount'],
				'currency'=>$request['currency'],
				'order_id'=>$request['order_id'],
			);

			ksort($data);
			$str = http_build_query($data);
			$sign_hash = md5($str . $this->config->get('payment_roskassa_security'));

			if (!$err)
			{
                // загрузка заказа

				$this->load->model('checkout/order');
				$order = $this->model_checkout_order->getOrder($request['order_id']);

				if (!$order)
				{
					$message .= $this->language->get('text_email_message9') . "\n";
					$err = true;
				}
				else
				{
					$order_curr = ($order['currency_code'] == 'RUR') ? 'RUB' : $order['currency_code'];
					$order_amount = number_format($order['total'], 2, '.', '');
					
					// проверка суммы

					if ($request['amount'] != $order_amount)
					{
						$message .= $this->language->get('text_email_message7') . "\n";
						$err = true;
					}

					// проверка статуса
					
					if (!$err)
					{
						if ($request['sign'] == $sign_hash) {
							if ($order['order_status_id'] !== $this->config->get('payment_roskassa_order_success_id')) {
								$this->model_checkout_order->addOrderHistory($request['order_id'], $this->config->get('payment_roskassa_order_success_id'));
							}
							echo 'YES';
						}else{

							if ($order['order_status_id'] !== $this->config->get('payment_roskassa_order_fail_id')) {

								$message .= $this->language->get('text_email_message2') . "\n";
								$this->model_checkout_order->addOrderHistory($request['order_id'], $this->config->get('payment_roskassa_order_fail_id'));
								$err = true;
							}
						}
					}
				}
			}
			
			if ($err)
			{
				$to = $this->config->get('payment_roskassa_admin_email');

				if (!empty($to))
				{
					$message = $this->language->get('text_email_message1') . "\n\n" . $message . "\n" . $log_text;
					$headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n" . 
					"Content-type: text/plain; charset=utf-8 \r\n";
					mail($to, $this->language->get('text_email_subject'), $message, $headers);
				}
				
				echo $request['order_id'] . '| error |' . $message;
			}
		}
	}

	public function fail() 
	{
		$this->response->redirect($this->url->link('checkout/checkout'));	
		return true;
	}

	public function success() 
	{
		$this->load->model('checkout/order');
		/* set pending status if not payed yet order will be confirmed otherway confirm will do nothing */
		$this->model_checkout_order->addOrderHistory($_REQUEST['order_id'], $this->config->get('payment_roskassa_order_success_id'), 'Order confirmed');

		$this->response->redirect($this->url->link('checkout/success'));
		return true;
	}
}