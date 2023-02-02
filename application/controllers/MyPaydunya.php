<?php

defined('BASEPATH') or exit('No direct script access allowed');

// require('../libraries/Paydunya/Paydunya.php');
class MyPaydunya extends   CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
     $this->load->database();
    $this->load->library('session');
    $this->load->library('paydunya');
    // $this->load->library('stripe');
        /*cache control*/
   //     $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
   //     $this->output->set_header('Pragma: no-cache');

        // CHECK CUSTOM SESSION DATA
   //     $this->session_data();
    }


    public function checkout(){

//        echo "ekosd,";

//        Paydunya_Setup::setMasterKey("wQzk9ZwR-Qq9m-0hD0-zpud-je5coGC3FHKW");

    echo   $this->input->post('total_price_of_checking_out');

    }




}