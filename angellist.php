<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Angellist Api Class
 *
 * Work with the api of angellist
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @subpackage      Api
 * @author        	Pastorinni Ochoa ascci.gcc@gmail.com | @momiaalpastor
 * @license         GNU GLPv3
 * @link	    	https://github.com/asccigcc
 * @version 0.0.1
 * @todo Need to work more in the funtions
 */
class angellist {
    /**
     * @var object CI instance
     */
    protected $_ci;
    /**
     * @var string $access_token value
     */
    private $access_token;
    /**
     * @desc this have a default value, but you can change if angellist url is not more valid
     * @var string $api_url = default
     */
    public $api_url = 'https://api.angel.co/1/';
    /**
     * @desc this have a default value, but you can change if angellist url is not more valid
     * @var string auth_url = default
     */
    public $auth_url = 'https://angel.co/api/oauth/token';
    /**
     * @link http://angel.co/api
     * @var string client_id
     */
    public $client_id = 'YOURCLIENTID';
    /**
     * @var string client_secret
     */
    public $client_secret = 'YOURCLIENTSECRET';
    /**
     * @desc this is the end_points of conections, we can add more endpoint in near future
     * @var array mixed $endpoints
     */
    public $endpoints = array(
                            'me', //@example : https://api.angel.co/1/me
                            'feed', //@example : https://api.angel.co/1/feed
                            'startups', //@example : https://api.angel.co/1/startups/<id_startup>
                            'startups/search', //@example : https://api.angel.co/1/startups/search?domain=<startup_name>
                            'tags', //@example : https://api.angel.co/1/tags/<id_tag>/startups read doc because this have many options
                            'startup_roles', //@example : https://api.angel.co/1/startup_roles?startup_id=<id_startup> or https://api.angel.co/1/startup_roles?user_id=<id_user>
                            'status_updates', //@desc check the functions to send message in this class
                            'users' // @example : https://api.angel.co/1/users/<id_user> or https://api.angel.co/1/users/search?slug=joshuaxls
                              );
    
    function __construct($url = '')
    {
        $this->_ci =& get_instance();
        $this->_ci->load->library(array('form_validation','curl'));
        $this->_ci->load->helper(array('form','url'));
        log_message('debug', 'Angellist Class Initialized');  
    }
    /**
     * @desc This get the auth token 
     * @return boolean if success return true if not false
     */
    public function oauthToken(){
        
        $code = $this->_ci->input->get('code');
        if($code){
            $this->_ci->curl->create('https://angel.co/api/oauth/token');  
            $this->_ci->curl->post(array(  
                             'client_id' => $this->client_id,
                             'client_secret' => $this->client_secret,
                             'code' => $code,
                             'grant_type' => 'authorization_code'
                            ));
            // will return a object and need to extract the access token
            $result = json_decode($this->_ci->curl->execute());
            $this->access_token = $result->access_token;
            return TRUE;
        }
        log_message('error',"could't conect to api");
        return FALSE;
    }
    /**
     * @desc Get all data for current user logged
     * @return object
     * @todo Check if functionally
     */
    public function meDatas(){
        $result = $this->_ci->curl->simple_get('https://api.angel.co/1/me?access_token='.$this->access_token);
        return json_decode($result);
    }
    /**
     * @desc The user who's startup relationships you want to view. 
     * @example https://api.angel.co/1/startup_roles?user_id=62526
     * @param int $id_user
     * @return object
     */
    public function meRoleStartup($id_user){
        $result = $this->_ci->curl->simple_get('https://api.angel.co/1/startup_roles?user_id='.$id_user);
        return json_decode($result,true);
    }
    /**
     * @desc The startup who's user relationships you want to view.
     * @param int $startup_id
     * @return object
     */
    public function userRoleStartup($startup_id ){
        $result = $this->_ci->curl->simple_get('https://api.angel.co/1/startup_roles?startup_id ='.$startup_id );
        return json_decode($result,true);
    }
    /**
     * @desc Get the startup datas | only per startup
     * @example : https://api.angel.co/1/startups/6702
     * @param integer $startup_id
     * @return object
     */
    public function startups($startup_id){
        $result = $this->_ci->curl->simple_get('https://api.angel.co/1/startups/'.$startup_id );
        return json_decode($result,true);
    }
    /**
     * @desc Get all updates for user
     * @example https://api.angel.co/1/status_updates?user_id=6702
     * @param integer $user_id
     * @return object
     */
    public function getStatusUser($user_id){
        $result = $this->_ci->curl->simple_get('https://api.angel.co/1/status_updates?user_id='.$user_id );
        return json_decode($result,true);
    }
    /**
     * @desc Get all updates for startup
     * @example
     * @paramt integer $startup_id
     * @return Object
     */
    public function getStatusStartup($startup_id){
        $result = $this->_ci->curl->simple_get('https://api.angel.co/1/status_updates?startup_id='.$startup_id );
        return json_decode($result,true);
    }
    /**
     * @desc Post a message n the wall, if not specified the startup, this post in user wall
     * @param $startup_id
     * @param $message
     * @return status
     */
    public function postMessage($startup_id = FALSE, $message){
        $data = array();
        $data['message'] = $message;
        ($startup_id != FALSE) ? $data['startup_id'] = $startup_id : NULL;
        $this->_ci->curl->create('https://api.angel.co/1/status_updates');  
        $this->_ci->curl->post($data);
        // will return a object and need to extract the access token
        $result = json_decode($this->_ci->curl->execute());
    }
    
    
}