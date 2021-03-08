<?php

namespace FluentFormPro\Integrations\Sendinblue;

class SendinblueApi
{
    protected $apiKey = null;

    private $apiUrl = "https://api.sendinblue.com/v3/";

    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    public function make_request($path, $data = array(), $method = 'POST')
    {
       
      
        $args =  array(
            'method'  => $method,
            'headers' => array(
                'accept'=> 'application/json',
                'content-type' => 'application/json',
                'api-key'=> $this->apiKey
            )
        );

        if(!empty($data)){
            $data["updateEnabled"] = false;
            $args['body'] = json_encode($data);
        }
  
        $apiUrl = $this->apiUrl . $path;
  
        if($method == 'POST') {
            $response = wp_remote_post($apiUrl, $args);
        } else if($method == 'GET') {
            $response = wp_remote_get($apiUrl, $args);
        }else {
            return (new \WP_Error(423, 'Request method could not be found'));
        }

       /* If WP_Error, die. Otherwise, return decoded JSON. */
       if (is_wp_error($response)) {
        return (new \WP_Error(423, $response->get_error_message()));
       }
        return json_decode($response['body'], true);
    }

    /**
     * Test the provided API credentials.
     *
     * @access public
     * @return Array
     */
    public function auth_test()
    {
       return $auth =  $this->make_request('account/', [], 'GET');
    }

    public function getLists()
    {
        $lists =  $this->make_request('contacts/lists', [], 'GET');
    
        if(!empty($lists['error'])) {
            return [];
        }
        return $lists;
    }
    public function attributes()
    {
        $attributes =  $this->make_request('contacts/attributes', [], 'GET');

        if(!empty($lists['error'])) {
            return [];
        }
        return $attributes;  
    }

    public function addContact($data)
    {  
        $response = $this->make_request('contacts/', $data, 'POST');
        if(!empty($response['id'])) {
            return $response;
        }

        return new \WP_Error('error', $response['message']);
    }

}