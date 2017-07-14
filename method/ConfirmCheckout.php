<?php
/**
 * At this point, the buyer has completed in authorizing payment
 * at PayPal.  The script will now call PayPal with the details
 * of the authorization, incuding any shipping information of the
 * buyer.  Remember, the authorization is not a completed transaction
 * at this state - the buyer still needs an additional step to finalize
 * the transaction.
*/
final class PaymentPaypal_ConfirmCheckout extends GWF_MethodPayment
{
	public function isAlwaysTransactional() { return true; }
	
	public function execute()
	{
		$paypaltoken = Common::getGetString("token");
		if ( (!($order = GWF_Order::table()->getById(Common::getGetString('id')))) ||
				($order->getXToken() !== $paypaltoken) )
		{
			return $this->error('err_order');
		}
		
		/* Build a second API request to PayPal, using the token as the
			ID to get the details on the payment authorization
		*/
		$nvpstr = "&TOKEN=".urlencode($paypaltoken);

		/* Make the API call and store the results in an array.  If the
			call was a success, show the authorization details, and provide
			an action to complete the payment.  If failed, show the error
		*/
		$resArray = Paypal_Util::hash_call('GetExpressCheckoutDetails', $nvpstr);

		$ack = strtoupper($resArray["ACK"]);
		if($ack === "SUCCESS")
		{
			$order->saveVar('order_xtoken', serialize($resArray));
			$this->renderOrder($order)->add($this->templateButton());
		}
		else
		{
			return Paypal_Util::paypalError($resArray);
		}
	}
	
	private function templateButton(GWF_Order $order)
	{
		return $this->templatePHP('paybutton.php', ['order' => $order]);
	}
}
