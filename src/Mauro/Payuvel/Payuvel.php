<?php namespace Mauro\Payuvel;

use Config;
use PayU;
use SupportedLanguages;
use PayUReports;
use Environment;
use PayUParameters;
use PayUCountries;
use PayUPayments;
use PaymentMethods;
use Payment;

include dirname(__FILE__).'/../../../lib/PayU.php';

class Payuvel {

	public static function factory() {

		PayU::$apiKey = Config::get('payuvel::apiKey'); //Ingrese aquí su propio apiKey.
		PayU::$apiLogin = Config::get('payuvel::apiLogin'); //Ingrese aquí su propio apiLogin.
		PayU::$merchantId = Config::get('payuvel::merchantId'); //Ingrese aquí su Id de Comercio.
		// PayU::$accountId = Config::get('payuvel::accountId'); //Ingrese aquí su Id de Comercio.
		PayU::$language = SupportedLanguages::ES; //Seleccione el idioma.
		PayU::$isTest = Config::get('payuvel::testing'); //Dejarlo True cuando sean pruebas.

		// URL de Pagos
		Environment::setPaymentsCustomUrl("https://stg.api.payulatam.com/payments-api/4.0/service.cgi");
		// URL de Consultas
		Environment::setReportsCustomUrl("https://stg.api.payulatam.com/reports-api/4.0/service.cgi");
		// URL de Suscripciones para Pagos Recurrentes
		Environment::setSubscriptionsCustomUrl("https://stg.api.payulatam.com/payments-api/rest/v4.3/");


	}

	public static function ping() {
		$response = PayUReports::doPing();
	}

	public static function payment( Payment $payment ) {

		$reference = "pg_".printf("%08s", $payment->id);;
		
		$value = $payment->value;

		//para realizar un pago con tarjeta de crédito---------------------------------
		$parameters = array(
			//Ingrese aquí el identificador de la cuenta.
			PayUParameters::ACCOUNT_ID => $payment->accountId,
			//Ingrese aquí el código de referencia.
			PayUParameters::REFERENCE_CODE => $payment->refernceCode,
			//Ingrese aquí la descripción.
			PayUParameters::DESCRIPTION => $payment->description,

			// -- Valores --
			//Ingrese aquí el valor.        
			PayUParameters::VALUE => $payment->value,
			//Ingrese aquí la moneda.
			PayUParameters::CURRENCY => $payment->currency,

			// -- Comprador 
			//Ingrese aquí el nombre del comprador.
			PayUParameters::BUYER_NAME => $payment->buyerName,
			//Ingrese aquí el email del comprador.
			PayUParameters::BUYER_EMAIL => $payment->buyerEmail,
			//Ingrese aquí el teléfono de contacto del comprador.
			PayUParameters::BUYER_CONTACT_PHONE => $payment->buyerPhone,
			//Ingrese aquí el documento de contacto del comprador.
			PayUParameters::BUYER_DNI => $payment->buyerDNI,
			//Ingrese aquí la dirección del comprador.
			PayUParameters::BUYER_STREET => $payment->buyerStreet,
			PayUParameters::BUYER_STREET_2 => $payment->buyerStreet2,
			PayUParameters::BUYER_CITY => $payment->buyerCity,
			PayUParameters::BUYER_STATE => $payment->buyerState,
			PayUParameters::BUYER_COUNTRY => $payment->buyerCountry,
			PayUParameters::BUYER_POSTAL_CODE => $payment->buyerPostalCode,
			PayUParameters::BUYER_PHONE => $payment->buyerPhone,

			// -- pagador --
			//Ingrese aquí el nombre del pagador.
			PayUParameters::PAYER_NAME => $payment->buyerName,
			//Ingrese aquí el email del pagador.
			PayUParameters::PAYER_EMAIL => $payment->buyerEmail,
			//Ingrese aquí el teléfono de contacto del pagador.
			PayUParameters::PAYER_CONTACT_PHONE => $payment->buyerPhone,
			//Ingrese aquí el documento de contacto del pagador.
			PayUParameters::PAYER_DNI => $payment->buyerDNI,
			//Ingrese aquí la dirección del pagador.
			PayUParameters::PAYER_STREET => $payment->buyerStreet,
			PayUParameters::PAYER_STREET_2 => $payment->buyerStreet2,
			PayUParameters::PAYER_CITY => $payment->buyerCity,
			PayUParameters::PAYER_STATE => $payment->buyerState,
			PayUParameters::PAYER_COUNTRY => $payment->buyerCountry,
			PayUParameters::PAYER_POSTAL_CODE => $payment->buyerPostalCode,
			PayUParameters::PAYER_PHONE => $payment->buyerPhone,

			// -- Datos de la tarjeta de crédito -- 
			//Ingrese aquí el número de la tarjeta de crédito
			PayUParameters::CREDIT_CARD_NUMBER => $payment->creditCardNumber,
			//Ingrese aquí la fecha de vencimiento de la tarjeta de crédito
			PayUParameters::CREDIT_CARD_EXPIRATION_DATE => $payment->expirationDate,
			//Ingrese aquí el código de seguridad de la tarjeta de crédito
			PayUParameters::CREDIT_CARD_SECURITY_CODE=> $payment->securityCode,
			//Ingrese aquí el nombre de la tarjeta de crédito
			//PaymentMethods::VISA||PaymentMethods::MASTERCARD||PaymentMethods::AMEX||PaymentMethods::DINERS
			PayUParameters::PAYMENT_METHOD => $payment->paymentMethod,

			//Ingrese aquí el número de cuotas.
			PayUParameters::INSTALLMENTS_NUMBER => "1",
			//Ingrese aquí el nombre del pais.
			PayUParameters::COUNTRY => PayUCountries::AR,

			//Session id del device.
			// PayUParameters::DEVICE_SESSION_ID => "vghs6tvkcle931686k1900o6e1",
			//IP del pagadador
			PayUParameters::IP_ADDRESS => $payment->ipAddress,
			//Cookie de la sesión actual.
			// PayUParameters::PAYER_COOKIE => "pt1t38347bs6jc9ruv2ecpv7o2",
			//Cookie de la sesión actual.        
			PayUParameters::USER_AGENT=>"Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0"
		);
			
		//solicitud de autorización y captura
		$response = PayUPayments::doAuthorizationAndCapture($parameters);

		return $response;

		//  -- podrás obtener las propiedades de la respuesta --
		// if($response){
		// 	$response->transactionResponse->orderId;
		// 	$response->transactionResponse->transactionId;
		// 	$response->transactionResponse->state;
		// 	if($response->transactionResponse->state=="PENDING"){
		// 		$response->transactionResponse->pendingReason;	
		// 	}
		// 	$response->transactionResponse->paymentNetworkResponseCode;
		// 	$response->transactionResponse->paymentNetworkResponseErrorMessage;
		// 	$response->transactionResponse->trazabilityCode;
		// 	$response->transactionResponse->responseCode;
		// 	$response->transactionResponse->responseMessage;   	
		// }




	}


}