<?php

require_once('plugins/ext/payment_modules/stripe/stripe-php-7.14.2/init.php');

class stripe
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_STRIPE_TITLE;
        $this->site = 'https://stripe.com';
        $this->api = 'https://stripe.com/docs/payments/checkout/one-time';
        $this->version = '1.0';
        $this->js = '<script src="https://js.stripe.com/v3/"></script>';
    }

    public function configuration()
    {
        $cfg = [];


        $cfg[] = [
            'key' => 'publishable_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_STRIPE_PUBLISHABLE_KEY,
            'params' => ['class' => 'form-control input-xlarge required'],
        ];

        $cfg[] = [
            'key' => 'secret_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_STRIPE_SECRET_KEY,
            'description' => TEXT_MODULE_STRIPE_SECRET_KEY_INFO,
            'params' => ['class' => 'form-control input-xlarge required'],
        ];

        $cfg[] = [
            'key' => 'endpoint_secret_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_STRIPE_ENDPOINT_SECRET_KEY,
            'description' => sprintf(
                TEXT_MODULE_STRIPE_ENDPOINT_SECRET_KEY_INFO,
                url_for_file('api/ipn.php?module_id=' . (int)$_GET['id'])
            ),
            'params' => ['class' => 'form-control input-xlarge required'],
        ];


        $cfg[] = [
            'key' => 'currency',
            'type' => 'input',
            'default' => 'USD',
            'title' => TEXT_MODULE_STRIPE_CURRENCY,
            'description' => TEXT_MODULE_STRIPE_CURRENCY_INFO,
            'params' => ['class' => 'form-control input-small required'],
        ];
        $cfg[] = [
            'key' => 'locale',
            'type' => 'input',
            'default' => 'en',
            'title' => TEXT_LANGUAGE,
            'description' => TEXT_MODULE_STRIPE_LANGUAGE_INFO,
            'params' => ['class' => 'form-control input-small required']
        ];

        $cfg[] = [
            'key' => 'custom_title',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_CUSTOM_TITLE,
            'description' => TEXT_DEFAULT . ' "' . $this->title . '".',
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[] = [
            'key' => 'item_name',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_PURPOSE_OF_PAYMENT,
            'description' => TEXT_ENTER_TEXT_PATTERN_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'amount',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_PAYMENT_TOTAL,
            'description' => TEXT_MODULE_PAYMENT_TOTAL_INFO,
            'params' => ['class' => 'form-control input-small required number'],
        ];

        return $cfg;
    }

    function confirmation($module_id, $process_id)
    {
        global $app_path, $current_item_id, $current_entity_id, $app_redirect_to, $alerts;

        $html = '';

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $item_info_query = db_query(
            "select e.* " . fieldtype_formula::prepare_query_select(
                $current_entity_id,
                ''
            ) . " from app_entity_" . $current_entity_id . " e  where e.id='" . $current_item_id . "'"
        );
        if ($item_info = db_fetch_array($item_info_query)) {
            $amount = $item_info['field_' . $cfg['amount']];

            $fieldtype_text_pattern = new fieldtype_text_pattern();

            $item_name = $fieldtype_text_pattern->output_singe_text($cfg['item_name'], $current_entity_id, $item_info);

            // Set your secret key: remember to change this to your live secret key in production
            // See your keys here: https://dashboard.stripe.com/account/apikeys
            \Stripe\Stripe::setApiKey($cfg['secret_key']);

            try {
                $currency = strtolower(trim($cfg['currency']));

                if (in_array($currency, ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw'])) {
                    $amount = ceil($amount);
                    $use_amount = number_format($amount, 0, '', '');
                } else {
                    $use_amount = number_format(($amount * 100), 0, '', '');
                }

                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [
                        [
                            'name' => $item_name,
                            'amount' => $use_amount,
                            'currency' => $currency,
                            'quantity' => 1,
                        ]
                    ],
                    'client_reference_id' => $process_id . '_' . $current_item_id,
                    'locale' => $cfg['locale'],
                    'success_url' => url_for('items/info', 'path=' . $app_path),
                    'cancel_url' => url_for('items/info', 'path=' . $app_path),
                ]);
            } catch (Exception $e) {
                $alerts->add($this->title . ' ' . TEXT_ERROR . ' ' . $e->getMessage(), 'error');
                echo $alerts->output();
                exit();
            }

            //print_rr($session);
            //echo $session['id'];

            $html .= '									
					<form name="payment_confirmation" id="payment_confirmation"  method="post">
						<p class="to-pay">' . TEXT_EXT_TO_PAY . ': ' . $amount . ' ' . $cfg['currency'] . '</p>'
                . submit_tag(TEXT_EXT_BUTTON_PAY, ['class' => 'btn btn-primary btn-pay']) . '
					</form>
																									
					<script>
						$(function(){
						
							var stripe = Stripe("' . $cfg['publishable_key'] . '");
									
							$("#payment_confirmation").submit(function(){
									stripe.redirectToCheckout({
									  // Make the id field from the Checkout Session creation API response
									  // available to this file, so you can provide it as parameter here
									  // instead of the {{CHECKOUT_SESSION_ID}} placeholder.
									  sessionId: "' . $session['id'] . '"
									}).then(function (result) {
									  // If `redirectToCheckout` fails due to a browser or network
									  // error, display the localized error message to your customer
									  // using `result.error.message`.
									  alert(result.error.message)
									});
	  		
									return false;
							})	
								  		
						})
					</script>
					
					';
        }

        return $html;
    }

    function ipn($module_id)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey($cfg['secret_key']);

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $cfg['endpoint_secret_key'];

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the checkout.session.completed event
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;

            // Fulfill the purchase...
            //handle_checkout_session($session);

            $info = explode('_', $session->client_reference_id);

            $process_info_query = db_query("select * from app_ext_processes where id='" . $info[0] . "'");
            if ($app_process_info = db_fetch_array($process_info_query) and $session->payment_intent != null) {
                $current_entity_id = $app_process_info['entities_id'];
                $current_item_id = $info[1];

                $item_info_query = db_query(
                    "select e.* from app_entity_" . $current_entity_id . " e  where e.id='" . $current_item_id . "'"
                );
                if ($item_info = db_fetch_array($item_info_query)) {
                    $amount = $session->display_items[0]->amount;
                    $currency = $session->display_items[0]->currency;

                    if (!in_array($currency, ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw'])) {
                        $amount = $amount / 100;
                    }

                    $comment = '<b>' . TEXT_EXT_PAYMENT_NOTIFICATION . '</b><br>' .
                        TEXT_EXT_PAYMENT_MODULE . ': ' . $this->title . '<br>' .
                        TEXT_MODULE_PAYMENT_TOTAL . ': ' . number_format($amount, 2, '.', '') . ' ' . strtoupper(
                            $currency
                        ) . '<br>' .
                        TEXT_MODULE_TRANSACTION_ID . ': ' . $session->payment_intent . '<br>' .
                        TEXT_MODULE_PAYMENT_STATUS . ': ' . modules::status_label('Completed', 'Completed');

                    $sql_data = [
                        'description' => $comment,
                        'entities_id' => $current_entity_id,
                        'items_id' => $current_item_id,
                        'date_added' => time(),
                        'created_by' => 0,

                    ];

                    db_perform('app_comments', $sql_data);

                    //run process
                    $processes = new processes($current_entity_id);
                    $processes->items_id = $current_item_id;
                    $processes->run($app_process_info, false, true);
                }
            }
        }

        http_response_code(200);
    }

}