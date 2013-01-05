<?php
function exosense_client($model, $method, $arguments)
{
    $client = $model->jsonrpc->get_client();
    $client->server($model->config->item('exosense_url'));
    $client->authentication($model->config->item('exosense_user'), $model->config->item('exosense_password'));
    $client->method($method);
    $client->request($arguments);
    return $client;
}

function exosense_request_reuse($client, $method, $arguments, $provide_callback = TRUE)
{
    $ci =& get_instance();

    if ($provide_callback) {
	$tid = mt_rand(1, 2000000000);
	$arguments['transaction-id'] = "$tid";
	$arguments['notifcation-url'] = $ci->config->item('exosense_notification_url');
	$arguments['timeout'] = $ci->config->item('exosense_timeout');
    }

    $client->method($method);
    $client->request($arguments);
    $res = $client->send_request();

    if (isset($tid))
	return $tid;

    return TRUE;
}

function exosense_request($model, $method, $device_id, $arguments)
{
    $client = exosense_client($model, $method, $arguments);
    return exosense_request_reuse($client, $method, $device_id, $arguments);
}


