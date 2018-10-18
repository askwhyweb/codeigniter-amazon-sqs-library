# Amazon SQS library for CodeIgniter

## How to Setup
1. Copy the files to Application/libraries
2. Run composer update command
3. Edit Application/config.php and add following
``` PHP 
$config['aws_access_key_id'] = 'xxxxxxx'; // Replace xxx Your Amazon Access Key ID
$config['aws_secret_access_key'] = 'yyyyy'; // replace your yyy with Amazon Access Key
$config['aws_access_sqs_url'] = 'https://sqs.ca-central-1.amazonaws.com/xxxxxx/yyyy'; // This will be found from your SQS Console.
$config['aws_region'] = 'ca-central-1'; // Change per need
$config['aws_profile'] = 'default'; // change per need
$config['aws_version'] = 'latest'; // change per need
```

## How to use
Your codeigniter application is easy to setup with library. You need to make sure you setup the config.php properly. This is the very first step. Afterwards, you can use following code to call the request.
``` PHP
$this->load->library('amazon_sqs');
$sqsdata; // TODO, Made this as a 2d Array, and use following methods to create a ticket accordingly.
$message = 'ABCD';
$attributes = array(); // optional, 2d array of attributes. e.g. ["TicketSubject" => ['DataType' => "String",'StringValue' => "Some test ticket subject"], "TicketID" => ['DataType' => "Number", 'StringValue' => "12069607"]]
$delay = 0; 
$result = $this->amazon_sqs->createTicket($messagem, $attributes, $delay);
print_r($result); // You will notice the debug data available in response for further proceeding or troubleshooting.
```

Any changes in documentation or code is acceptable. Bugs can be reported as well. I will be happier to assist when and where required...!