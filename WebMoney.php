<?php
class WebMoney {
	public $settings = array(
		'description' => 'Accept payments via WebMoney.',
	);
	function payment_button($params) {
		global $billic, $db;
		$html = '';
		if (get_config('webmoney_purse') == '') {
			return $html;
		}
		if ($billic->user['verified'] == 0 && get_config('webmoney_require_verification') == 1) {
			return 'verify';
		} else {
			$html.= '<form id=pay name=pay method="POST" action="https://merchant.wmtransfer.com/lmi/payment.asp">' . PHP_EOL;
			$html.= '<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="' . $params['charge'] . '">' . PHP_EOL;
			$html.= '<input type="hidden" name="LMI_PAYMENT_DESC" value="Invoice #' . $params['invoice']['id'] . '">' . PHP_EOL;
			$html.= '<input type="hidden" name="LMI_PAYMENT_NO" value="' . $params['invoice']['id'] . '">' . PHP_EOL;
			$html.= '<input type="hidden" name="LMI_PAYEE_PURSE" value="' . get_config('webmoney_purse') . '">' . PHP_EOL;
			$html.= '<input type="hidden" name="LMI_SIM_MODE" value="0">' . PHP_EOL;
			$html.= '<input type="hidden" name="LMI_RESULT_URL" value="http' . (get_config('billic_ssl') == 1 ? 's' : '') . '://' . get_config('billic_domain') . '/Gateway/WebMoney/">' . PHP_EOL;
			$html.= '<input type="hidden" name="LMI_SUCCESS_URL" value="http' . (get_config('billic_ssl') == 1 ? 's' : '') . '://' . get_config('billic_domain') . '/User/Invoices/ID/' . $params['invoice']['id'] . '/Status/Complete/">' . PHP_EOL;
			$html.= '<input type="hidden" name="LMI_SUCCESS_METHOD" value="1">' . PHP_EOL;
			$html.= '<input type="hidden" name="LMI_PAYMER_EMAIL" value="' . htmlentities($billic->user['email']) . '">' . PHP_EOL;
			$html.= '<input type="submit" class="btn btn-default" value="WebMoney"> - Please sent manual payment of ' . get_config('billic_currency_prefix') . $params['charge'] . get_config('billic_currency_suffix') . ' to ' . get_config('webmoney_purse') . ' and invoice "Invoice #' . $params['invoice']['id'] . '" as the message' . PHP_EOL;
			$html.= '</form>' . PHP_EOL;
		}
		return $html;
	}
	function payment_callback() {
		global $billic, $db;
		return 'Feature not coded yet';
	}
	function settings($array) {
		global $billic, $db;
		if (empty($_POST['update'])) {
			echo '<form method="POST"><input type="hidden" name="billic_ajax_module" value="WebMoney"><table class="table table-striped">';
			echo '<tr><th>Setting</th><th>Value</th></tr>';
			echo '<tr><td>Require Verification</td><td><input type="checkbox" name="webmoney_require_verification" value="1"' . (get_config('webmoney_require_verification') == 1 ? ' checked' : '') . '></td></tr>';
			echo '<tr><td>WebMoney Purse</td><td><input type="text" class="form-control" name="webmoney_purse" value="' . safe(get_config('webmoney_purse')) . '"></td></tr>';
			echo '<tr><td colspan="2" align="center"><input type="submit" class="btn btn-default" name="update" value="Update &raquo;"></td></tr>';
			echo '</table></form>';
		} else {
			if (empty($billic->errors)) {
				set_config('webmoney_require_verification', $_POST['webmoney_require_verification']);
				set_config('webmoney_purse', $_POST['webmoney_purse']);
				$billic->status = 'updated';
			}
		}
	}
}
