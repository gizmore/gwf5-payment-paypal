<?php
final class Module_PaymentPaypal extends GWF_PaymentModule
{
	public function onLoadLanguage() { $this->loadLanguage('lang/paypal'); }
	public function getConfig()
	{
		return array_merge(parent::getConfig(), array(
			GDO_Divider::make('paypal_api_settings'),
			GDO_Checkbox::make('paypal_api_sandbox')->initial('1'),
			GDO_Secret::make('paypal_api_username')->initial('CBusch1980_api1.gmx.de'),
		    GDO_Secret::make('paypal_api_password')->initial('ECL83PUVR4CF2LU3'),
		    GDO_Secret::make('paypal_api_signature')->initial('An5ns1Kso7MWUdW4ErQKJJJ4qi4-AKKoQTrZVr51cIn6b.aMsI-4t2xg'),
			GDO_Divider::make('paypal_proxy_settings'),
			GDO_Checkbox::make('paypal_proxy')->initial('0'),
			GDO_String::make('paypal_proxy_host')->ascii()->caseS()->initial('127.0.0.1'),
			GDO_Int::make('paypal_proxy_port')->unsigned()->min(1)->max(65535)->initial('8080'),
		));
	}
	public function cfgSandbox() { return $this->getConfigValue('paypal_api_sandbox'); }
	public function cfgUsername() { return $this->getConfigVar('paypal_api_username'); }
	public function cfgPasword() { return $this->getConfigVar('paypal_api_password'); }
	public function cfgSignature() { return $this->getConfigVar('paypal_api_signature'); }
	public function cfgApiUri() { return $this->cfgSandbox() ? 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=' : 'https://www.paypal.com/webscr&cmd=_express-checkout&token='; }
	public function cfgEndpoint() { return $this->cfgSandbox() ? 'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp'; }
	public function cfgProxy() { return $this->getConfigValue('paypal_proxy'); }
	public function cfgProxyHost() { return $this->getConfigVar('paypal_proxy_host'); }
	public function cfgProxyPort() { return $this->getConfigVar('paypal_proxy_port'); }
	
	public function includePaypal()
	{
		define('PAYPAL_VERSION', '2.3');
		define('PAYPAL_API_USERNAME', $this->cfgUsername());
		define('PAYPAL_API_PASSWORD', $this->cfgPasword());
		define('PAYPAL_API_SIGNATURE', $this->cfgSignature());
		define('PAYPAL_API_ENDPOINT', $this->cfgEndpoint());
		define('PAYPAL_URL', $this->cfgApiUri());
		define('PAYPAL_USE_PROXY', $this->cfgProxy());
		define('PAYPAL_PROXY_HOST', $this->cfgProxyHost());
		define('PAYPAL_PROXY_PORT', $this->cfgProxyPort());
		$this->includeClass('Paypal_Util');
	}
}
