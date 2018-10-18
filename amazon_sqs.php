<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
		
use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

class amazon_sqs{
	/**
	 *
	 *	@author: Farhan Islam <farhan@orvisoft.com>
	 *	@dated : 03,Oct,2018
	 *	@purpose: To execute SQS and several relevant use cases.
	 *
	 **/
	
	protected $_ci, $client, $queueUrl;
	/**
	  *
	  *	@param, $profile = default, $version = latest, $region = region, $queryUrl = full url i.e. https://sqs.ca-central-1.amazonaws.com/xxxxxx/yyyy (can be taken from SQS console)
	  *
	  **/
	function __construct($profile='', $version='', $region='', $queueUrl = ""){
		$this->_ci = & get_instance();
		if($profile == ''){
			$profile = $this->_ci->config->item('aws_profile');
		}
		if($version == ''){
			$version = $this->_ci->config->item('aws_version');
		}
		if($region == ''){
			$region = $this->_ci->config->item('aws_region');
		}
		if($queryUrl == ''){
			$queryUrl = $this->_ci->config->item('aws_access_sqs_url');
		}
		$this->client = SqsClient::factory([
			'profile' => $profile,
			'version' => $version,
			'region'  => $region,
			'credentials' => [
				'key'    => $this->_ci->config->item('aws_access_key_id'),
				'secret' => $this->_ci->config->item('aws_secret_access_key'),
			]
		]);
		
		$this->queueUrl = $queueUrl;
	}
	
	/**
	 *	@ param samples as below.
	 *		$message = Message required to post. e.g. Test message.
	 *		$attributes = 2d array of attributes. e.g. ["TicketSubject" => ['DataType' => "String",'StringValue' => "Some test ticket subject"], "TicketID" => ['DataType' => "Number", 'StringValue' => "12069607"]]
	 *		$delay = in seconds e.g. 0
	 **/
	function createTicket($message, $attributes= array(), $delay=0){
		$params = [
			'DelaySeconds' => $delay,
			'MessageAttributes' => $attributes, //["TicketSubject" => ['DataType' => "String",'StringValue' => "Some test ticket subject"], "TicketID" => ['DataType' => "Number", 'StringValue' => "12069607"]],
			'MessageBody' => $message, //"Some Lorem Ipsum Text",
			'QueueUrl' => $this->queueUrl
		];
		try {
			/**/ // Add message on SQS
			$result = $this->client->sendMessage($params);
			return ['success'=>1, 'result'=>$result];
			/**/
		} catch (AwsException $e) {
			return ['success'=>0, 'result'=>$e->getMessage()];
		}
	}
	
	/**
	 *
	 *	@params as below
	 *	$MessageAttributeNames = filter by attributes. e.g ['TicketID' => 12069607]
	 *	$AttributeNames = attribute selection
	 *	$MaxNumberOfMessages = between 1-10
	 *	$WaitTimeSeconds = 0 for short polling, and for long polling per demand. Reference: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/sqs-examples-enable-long-polling.html
	 *
	 **/
	function getTickets($MessageAttributeNames = ['All'], $AttributeNames = ['SentTimestamp'], $MaxNumberOfMessages = 10, $WaitTimeSeconds = 0){
		try {
			$result = $this->client->receiveMessage(array(
				'AttributeNames' => $AttributeNames, //['SentTimestamp'],
				'MaxNumberOfMessages' => $MaxNumberOfMessages, //10,
				'MessageAttributeNames' => $MessageAttributeNames, //['All'], //['TicketID' => 12069607], //['All'],
				'QueueUrl' => $this->queueUrl, // REQUIRED
				'WaitTimeSeconds' => $WaitTimeSeconds, //0,
			));
			if (count($result->get('Messages')) > 0) {
				return ['success'=>1 ,'result'=>$result->get('Messages')];
			} else {
				return ['success'=>0, 'result'=>'', 'message'=>'No messages in que.'];
			}
		} catch (AwsException $e) {
			return ['success'=>0, 'result'=>$e->getMessage()];
		}
	}
	
	/**
	 *
	 *	Experimental. Not required to be used.
	 *
	 **/
	function delTicket(){
		/**
	   $result = $this->client->deleteMessage([
			'QueueUrl' => $queueUrl, // REQUIRED
			'ReceiptHandle' => $result->get('Messages')[0]['ReceiptHandle'] // REQUIRED
		]); /**/
	}
	
}