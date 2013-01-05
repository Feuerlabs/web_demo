<?php


class Jsonrpc {
	var $server;
	var $client;

	function Jsonrpc() {

	}

	function get_server() {
		if(!isset($this->server)) {
			$this->server = new JSON_RPC_Server();
		}
		return $this->server;
	}

	function get_client() {
		if(!isset($this->client)) {
			$this->client = new JSON_RPC_Client();
		}
		return $this->client;
	}
}

class JSON_RPC_Message {
	var $JSON_RPC_VERSION		= '2.0';
	var $JSON_RPC_ID;	// for backwards compatibility

	var $CONTENT_LENGTH_KEY 	= 'Content-Length';
	var $CONTENT_TYPE_KEY 		= 'Content-Type';
	var $CONNECTION_KEY 		= 'Connection';

	var $content_length;
	var $content_type			= 'application/json';
	var $connection				= 'Close';

	var $error_code				= '';
	var $error_message			= '';

	var $raw_data;
	var $data_object;

	var $parser;
	var $VALUE_MAPPINGS = array();

	function JSON_RPC_Message() {
		$this->parser = new JSON_RPC_Parser();
		$this->JSON_RPC_ID = rand();

		$this->VALUE_MAPPINGS = array(
				$this->CONTENT_LENGTH_KEY	=> 'content_length',
				$this->CONTENT_TYPE_KEY		=> 'content_type',
				$this->CONNECTION_KEY		=> 'connection',
			);
	}

	function create_header($key, $value) {
		return "$key: $value\r\n";
	}
	function parse_header($header) {
		if(preg_match('/(.+):\s+(.+)/', $header, $matches)) {
			return array($matches[1],$matches[2]);
		}
		return false;
	}
}

class JSON_RPC_Request extends JSON_RPC_Message {
	var $url;
	var $remote_method;

	function JSON_RPC_Request() {
		parent::JSON_RPC_Message();
	}

	function create_request() {
		$req = '';

		$data = array();
		$data['json-rpc']	= $this->JSON_RPC_VERSION;
		$data['id']		= $this->JSON_RPC_ID;
		$data['method']		= $this->remote_method;
		if(isset($this->data_object)) {
		    $data['params'] = $this->data_object;
		}
		else
		{
		    $data[ 'params' ] = array();
		}

		$this->raw_data = $this->parser->encode($data);
		return $this->raw_data;
	}
}

class JSON_RPC_Response extends JSON_RPC_Message {
	var $ERROR_CODES = array(
			'invalid_json'			=> 1,
			'response_not_ok'		=> 2,
			'response_malformed'	=> 3,
		);
	var $ERROR_MESSAGES = array(
			'invalid_json' 			=> 'The server responded with an invalid JSON object',
			'response_not_ok' 		=> '...',
			'response_malformed' 	=> 'The server responded with a malformed HTTP request'
		);

	var $SERVER_KEY = 'Server';
	var $CACHE_CONTROL_KEY = 'Cache-Control';

	var $server;
	var $cache_control;

	var $http_version;
	var $response_code;

	var $error_code = '';
	var $error_message = '';

	function JSON_RPC_Response($message = '', $error_code = '', $error_message = '') {
		parent::JSON_RPC_Message();

		$this->raw_data = $message;
		$this->error_code = $error_code;
		$this->error_message = $error_message;

		$this->VALUE_MAPPINGS[$this->SERVER_KEY]		= 'server';
		$this->VALUE_MAPPINGS[$this->CACHE_CONTROL_KEY]	= 'cache_control';
	}

	function has_errors() {
		return (strlen($this->error_code) > 0);
	}

	function parse_response() {
		if(strncmp($this->raw_data, 'HTTP', 4) == 0) {
			preg_match('/^HTTP\/([0-9\.]+) (\d+) /', $this->raw_data, $response);

			$this->http_version  = $response[1];
			$this->response_code = $response[2];

			if($this->response_code != '200') {
				$this->error_code = $this->ERROR_CODES['response_not_ok'];
				$this->error_message = substr($this->raw_data, 0, strpos($this->raw_data, "\n")-1);
				return false;
			}
		} else {
			$this->error_code = $this->ERROR_CODES['response_malformed'];
			$this->error_message = $this->ERROR_MESSAGES['response_malformed'];
			return false;
		}


		$lines = explode("\r\n", $this->raw_data);
		array_shift($lines); // remove first line, as it's not technically a header

		while (($line = array_shift($lines))) {
		    if(strlen($line) < 1) { break; }

		    $header = $this->parse_header($line);

		    //echo $this->VALUE_MAPPINGS[$header[0]];
		    //$k = $this->VALUE_MAPPINGS[$header[0]];
		    //echo $this->$k;

		    if(isset($this->VALUE_MAPPINGS[$header[0]])) {
			$k = $this->VALUE_MAPPINGS[$header[0]];

			$this->$k = $header[1];
		    }

		}
		$data = implode("\r\n", $lines);
		log_message('debug', 'parse_response(2):'.$data);

		$this->data_object = $this->parser->decode($data);

		if(!is_array($this->data_object) || is_null($this->data_object)) {
		    log_message('debug', 'parse_response(3)['.$data.']');
		    $this->error_code = $this->ERROR_CODES['invalid_json'];
		    $this->error_message = $this->ERROR_MESSAGES['invalid_json'];
		    return false;
		}

		return true;
	}
}

class JSON_RPC_Server_Response extends JSON_RPC_Message {
    var $SERVER_KEY = 'Server';

    var $server = 'CodeIgniter JSON RPC Server';

    var $id;
    var $error;

    var $ERROR_CODES = array(
	'bad_call'=>array(
	    'code'=> 000,
	    'name'=>'Bad call',
	    'message'=> 'The procedure call is not valid.'
	    ),
	'parse_error'=>array(
	    'code'=> 000,
	    'name'=> 'Parse error',
	    'message'=> 'An error occurred on the server while parsing the JSON text comprising the procedure call.'
	    ),
	'procedure_not_found'=>array(
	    'code'=> 000,
	    'name'=> 'Procedure not found',
	    'message'=> 'The call is valid but the procedure identified by the call could not be located on the service.'
	    ),
	'service_error'=>array(
	    'code'=> 000,
	    'name'=> 'Service error',
	    'message'=> 'The call is valid, but a general error occurred during the procedure invocation.'
	    )
	);

    function JSON_RPC_Server_Response($data_object = null) {
	parent::JSON_RPC_Message();

	if($data_object != null) {
	    $this->data_object = $data_object;
	}
    }

    function set_error($error) {
	if(is_string($error)) {
	    $this->error = $this->ERROR_CODES[$error];
	} else if(is_array($error)) {
	    $this->error = $error;
	}
    }

    function create_server_response() {
	$data = array();
	$data['version']	= $this->JSON_RPC_VERSION;

	if(isset($this->id)) { $data['id'] = $this->id; }
	if(isset($this->error)) { $data['error'] = $this->error; }
	else { $data['result'] = $this->data_object; }

	$data = $this->parser->encode($data);
	$this->content_length = strlen($data);

	header("HTTP/1.1 200 OK\r\n");
	header($this->create_header($this->SERVER_KEY, $this->server));
	header($this->create_header($this->CONNECTION_KEY, $this->connection));
//		header($this->create_header($this->CONTENT_TYPE_KEY, $this->content_type));
	header($this->create_header($this->CONTENT_LENGTH_KEY, $this->content_length));

	$this->raw_data = $data;

	return $data;
    }
}

class JSON_RPC_Server_Request extends JSON_RPC_Message {
    var $ERROR_CODES = array(
	'invalid_json' => 1
	);
    var $ERROR_MESSAGES = array(
	'invalid_json' => 'The server responded with an invalid JSON object'
	);

    var $error_code = '';
    var $error_message = '';

    function JSON_RPC_Server_Request($message = '', $error_code = '', $error_message = '') {
	parent::JSON_RPC_Message();

	$this->raw_data = $message;
	$this->error_code = $error_code;
	$this->error_message = $error_message;
    }

    function has_errors() {
	return (strlen($this->error_code) > 0);
    }

    function last_error()
    {
	return $this->parser->last_error();
    }

    function parse_response() {
	$this->data_object = $this->parser->decode($this->raw_data);
	log_message('debug', 'parse_response(1):'.print_r($this->data_object, TRUE));

	if(!is_array($this->data_object) || is_null($this->data_object)) {
	    $this->error_code = $this->ERROR_CODES['invalid_json'];
	    $this->error_message = $this->ERROR_MESSAGES['invalid_json'];
	    return false;
	}

	return true;
    }
}

class JSON_RPC_Client {
    var $request;
    var $response;

    var $port		= 80;
    var $timeout	= 5;
    var $user = '';
    var $password = '';

    function JSON_RPC_Client() {
	$this->request = new JSON_RPC_Request();
    }
    function server($url) {
	$this->request->url = $url;
    }
    function authentication($user, $password) {
	$this->user = $user;
	$this->password = $password;
    }
    function method($remote_method) {
	$this->request->remote_method = $remote_method;
    }
    function request($request_parameters) {
	$this->request->data_object = $request_parameters;
    }
    function timeout($timeout = 5) {
	$this->timeout = $timeout;
    }
    function send_request() {
		$request = $this->request->create_request();
		log_message('debug', 'send_request(1):'.$this->request->url);
		log_message('debug', 'send_request(2):'.$this->request->raw_data);
		log_message('debug', 'send_request(3):'.$this->user);
		log_message('debug', 'send_request(4):'.$this->password);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;

		if ($this->user != '') {
		    curl_setopt($curl, CURLOPT_USERPWD, $this->user.':'.$this->password);
		}

		curl_setopt($curl, CURLOPT_SSLVERSION,3);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->request->raw_data );
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, $this->request->url);
		$res = curl_exec($curl);
		curl_close($curl);
		log_message('debug', 'send_request(3):'.$res);
		if(!$res) {
		    log_message('debug', 'send_request(4 (fail))');
		    $this->response = new JSON_RPC_Response('', '401', 'Failure');
		    return false;
		}

		$this->response = new JSON_RPC_Response($res);
		return $this->response->parse_response();
	}

	function get_response() {
		return $this->response;
	}

	function get_response_object() {
		return $this->response->data_object;
	}
}

class JSON_RPC_Server {
	var $php_types_to_jsonrpc_types = array(
		'Boolean'=>'bit',
		'Number'=>'num',
		'String'=>'str',
		'Array'=>'arr',
		'Object'=>'obj'
		);

	var $methods = array();
	var $object = false;

	var $service_name 		= 'CodeIgniter JSON RPC Server';
	var $service_sd_version = '1.0';
	var $service_id			= '';
	var $service_version	= '1.0';
	var $service_summary	= 'A JSON RPC Server for CodeIgniter. Written by Nick Husher (nhusher@bear-code.com)';
	var $service_help		= '';
	var $service_address	= '';

	function JSON_RPC_Server() {
		$this->methods['system.describe'] = array(
			'function'=>'this.describe',
			'summary'=>'Display relevant information about the JSON RPC server.',
			'help'=>'http://json-rpc.org',
			'return'=>array('type'=>'obj')
			);

		$CI =& get_instance();
		$CI->load->helper('url');

		$this->service_address = current_url();
		$this->service_id = current_url();
	}

	function define_methods($methods) {

		foreach($methods as $methodName=>$methodProperties) {
			$this->methods[$methodName] = $methodProperties;
		}
	}
	function set_object($object) {
		if(is_object($object)) {
			$this->object =& $object;
		}
	}

	function serve() {
	    $body = &file_get_contents('php://input');

	    $incoming = new JSON_RPC_Server_Request($body);
	    log_message('debug', 'SERVE(1): '.print_r($body, TRUE));
	    log_message('debug', 'SERVE(2): '.print_r($incoming, TRUE));

	    if(!$incoming->parse_response()) {
		log_message('debug', 'PARSE_ERROR(): '.$incoming->last_error());

		$response = $this->send_error('parse_error');
		echo $response->create_server_response();
		return;
	    }

	    $response = $this->_execute($incoming->data_object);

	    echo $response->create_server_response();
	}

	function send_response($object) {
		return new JSON_RPC_Server_Response($object);
	}
	function send_error($error) {
		$r = new JSON_RPC_Server_Response();
		$r->set_error($error);

		return $r;
	}

	function _execute($request_object) {
		// check if the method is defined on the server
		if(!isset($this->methods[$request_object['method']])) {
			return $this->send_error('procedure_not_found');
		}
		$method_definition = $this->methods[$request_object['method']];

		// check if we have a function definition
		if(!isset($method_definition['function'])) {
			return $this->send_error('procedure_not_found');
		}

		$function_name = explode('.',$method_definition['function']);
		$is_system_call = ($function_name[0] == 'this');

		// check if the function/object is callable
		if($is_system_call) {
			if(!isset($function_name[1]) || !is_callable(array($this, $function_name[1]))) {
				$r = $this->send_error('service_error');
//				$r->error['code'] = 001;
				return $r;
			}
		} else {
		    log_message('debug', 'Exosense::EXEC(1): '.$function_name[1]);
		    if(!isset($function_name[1]) ||
		       !is_callable(array($function_name[0], $function_name[1]))) {
			log_message('debug', 'Exosense::EXEC(2): ');
			$r = $this->send_error('service_error');
//			$r->error['code'] = 002;
			return $r;
			}
		}

		// check parameters
		if(isset($request_object['params'])) {
		    log_message('debug', 'Exosense::EXEC(2.1):'.print_r($request_object['params'], TRUE));
		    $parameters = $request_object['params'];
		} else {
			$parameters = array();
		}


		log_message('debug', 'Exosense::EXEC(2.2):'.print_r($method_definition, TRUE));
		if(isset($method_definition['parameters']) && is_array($method_definition['parameters'])) {
			$method_parameters = $method_definition['parameters'];

			log_message('debug', 'Exosense::EXEC(2.3):'.print_r($method_parameters, TRUE));

			for($i = 0; $i < count($method_parameters); $i++) {
				$current_parameter = $method_parameters[$i];
				log_message('debug', 'Exosense::EXEC(3):'.print_r($current_parameter, TRUE));
				if(!isset($current_parameter['name'])) {
					$r = $this->send_error('service_error');
//					$r->error['code'] = 003;
					return $r;
				}

				log_message('debug', 'Exosense::EXEC(4):'.print_r($current_parameter['name'], TRUE));
				if(!isset($parameters[$current_parameter['name']])) {
					return $this->send_error('bad_call');
				}
				log_message('debug', 'Exosense::EXEC(5):'.print_r($parameters[$current_parameter['name']], TRUE));

				if(isset($current_parameter['type']) &&
				   gettype($parameters[$current_parameter['name']]) != $current_parameter['type'])
				{
				    return $this->send_error('bad_call');
				}
			}
		}

		// call the function
		if($is_system_call) {
			$response = $this->$function_name[1]($parameters);
		} else {
			if(is_object($this->object)) {
				$response = $this->object->$function_name[1]($parameters);
			} else {
				$r = $this->send_error('service_error');
//				$r->error['code'] = 003;
				return $r;
			}
		}

		if(isset($request_object['id'])) {
			$response->id = $request_object['id'];
		}

		return $response;
	}

	// system functions
	function describe() {
		$method_property_names = array(
			'parameters'=>'params',
			'summary'=>'summary',
			'help'=>'help',
			'return'=>'return'
			);

		$description = array();

		$description['sdversion']	= $this->service_sd_version;
		$description['name']		= $this->service_name;
		$description['id']			= $this->service_id;
		$description['version']		= $this->service_version;
		$description['summary']		= $this->service_summary;
		$description['help']		= $this->service_help;
		$description['address']		= $this->service_address;


		$description['procs'] = array();
		foreach($this->methods as $method_name=>$method_properties) {
			$method = array();
			$method['name'] = $method_name;

			foreach($method_property_names as $name=>$property_name) {
				if(isset($method_properties[$property_name])) {
					$method[$property_name] = $method_properties[$name];
				} else if($name == 'parameters' || $name == 'return') {
					$method[$property_name] = 'any';
				}
			}

			$description['procs'][] = $method;
		}

		return $this->send_response($description);
	}
}

class JSON_RPC_Parser {
	function encode($val) {
		return json_encode($val);
	}
	function decode($val) {
	    log_message('debug', 'decode():'.print_r($val, TRUE));

	    return json_decode($val, true);
	}

	function last_error() {

	    switch (json_last_error()) {
	    case JSON_ERROR_NONE:
		return'ok';
		break;
	    case JSON_ERROR_DEPTH:
		return 'maximum stack depth exceeded';
		break;
	    case JSON_ERROR_STATE_MISMATCH:
		return 'underflow or the modes mismatch';
		break;
	    case JSON_ERROR_CTRL_CHAR:
		return 'unexpected control character found';
		break;
	    case JSON_ERROR_SYNTAX:
		return 'syntax error, malformed JSON';
		break;

	    case JSON_ERROR_UTF8:
		return 'malformed UTF-8 characters, possibly incorrectly encoded';
		break;

	    default:
		return 'unknown error';
		break;
	    }
	}
}

?>