<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $this->load->database();
        $this->load->library('session');
        // $this->load->library('stripe');
        /*cache control*/
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');

        // CHECK CUSTOM SESSION DATA
        $this->session_data();

        $this->load->model('google_login_model');
    }


    public function clearApp(){

        $this->output->delete_cache();
//     $this->output->clear_all_cache();

        echo "
            <script type=\"text/javascript\">
           
            alert('bassarou php JS');
            </script>
        ";



     var_dump("clean cache");
//        $this->home();
    }

    public function index()
    {
        $this->home();
    }

    public function verification_code()
    {
        if (!$this->session->userdata('register_email')) {
            redirect(site_url('home/sign_up'), 'refresh');
        }
        $page_data['page_name'] = "verification_code";
        $page_data['page_title'] = site_phrase('verification_code');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function home()
    {
        $page_data['page_name'] = "home";
        $page_data['page_title'] = site_phrase('home');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function shopping_cart()
    {
        if (!$this->session->userdata('cart_items')) {
            $this->session->set_userdata('cart_items', array());
        }
        $page_data['page_name'] = "shopping_cart";
        $page_data['page_title'] = site_phrase('shopping_cart');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function courses()
    {
        if (!$this->session->userdata('layout')) {
            $this->session->set_userdata('layout', 'list');
        }
        $layout = $this->session->userdata('layout');
        $selected_category_id = "all";
        $selected_price = "all";
        $selected_level = "all";
        $selected_language = "all";
        $selected_rating = "all";
        // Get the category ids
        if (isset($_GET['category']) && !empty($_GET['category'] && $_GET['category'] != "all")) {
            $selected_category_id = $this->crud_model->get_category_id($_GET['category']);
        }

        // Get the selected price
        if (isset($_GET['price']) && !empty($_GET['price'])) {
            $selected_price = $_GET['price'];
        }

        // Get the selected level
        if (isset($_GET['level']) && !empty($_GET['level'])) {
            $selected_level = $_GET['level'];
        }

        // Get the selected language
        if (isset($_GET['language']) && !empty($_GET['language'])) {
            $selected_language = $_GET['language'];
        }

        // Get the selected rating
        if (isset($_GET['rating']) && !empty($_GET['rating'])) {
            $selected_rating = $_GET['rating'];
        }


        if ($selected_category_id == "all" && $selected_price == "all" && $selected_level == 'all' && $selected_language == 'all' && $selected_rating == 'all') {
            if (!addon_status('scorm_course')) {
                $this->db->where('course_type', 'general');
            }
            $this->db->where('status', 'active');
            $total_rows = $this->db->get('course')->num_rows();
            $config = array();
            $config = pagintaion($total_rows, 6);
            $config['base_url']  = site_url('home/courses/');
            $this->pagination->initialize($config);
            if (!addon_status('scorm_course')) {
                $this->db->where('course_type', 'general');
            }
            $this->db->where('status', 'active');
            $page_data['courses'] = $this->db->get('course', $config['per_page'], $this->uri->segment(3))->result_array();
        } else {
            $courses = $this->crud_model->filter_course($selected_category_id, $selected_price, $selected_level, $selected_language, $selected_rating);
            $page_data['courses'] = $courses;
        }

        $page_data['page_name']  = "courses_page";
        $page_data['page_title'] = site_phrase('courses');
        $page_data['layout']     = $layout;
        $page_data['selected_category_id']     = $selected_category_id;
        $page_data['selected_price']     = $selected_price;
        $page_data['selected_level']     = $selected_level;
        $page_data['selected_language']     = $selected_language;
        $page_data['selected_rating']     = $selected_rating;
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function set_layout_to_session()
    {
        $layout = $this->input->post('layout');
        $this->session->set_userdata('layout', $layout);
    }

    public function course($slug = "", $course_id = "")
    {
        $this->access_denied_courses($course_id);
        $page_data['course_id'] = $course_id;
        $page_data['page_name'] = "course_page";
        $page_data['page_title'] = site_phrase('course');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function instructor_page($instructor_id = "")
    {
        $page_data['page_name'] = "instructor_page";
        $page_data['page_title'] = site_phrase('instructor_page');
        $page_data['instructor_id'] = $instructor_id;
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function my_courses()
    {
        if ($this->session->userdata('user_login') != true) {
            redirect(site_url('home'), 'refresh');
        }

        $page_data['page_name'] = "my_courses";
        $page_data['page_title'] = site_phrase("my_courses");
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function my_messages($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('user_login') != true) {
            redirect(site_url('home'), 'refresh');
        }
        if ($param1 == 'read_message') {
            $page_data['message_thread_code'] = $param2;
        } elseif ($param1 == 'send_new') {
            $message_thread_code = $this->crud_model->send_new_private_message();
            $this->session->set_flashdata('flash_message', site_phrase('message_sent'));
            redirect(site_url('home/my_messages/read_message/' . $message_thread_code), 'refresh');
        } elseif ($param1 == 'send_reply') {
            $this->crud_model->send_reply_message($param2); //$param2 = message_thread_code
            $this->session->set_flashdata('flash_message', site_phrase('message_sent'));
            redirect(site_url('home/my_messages/read_message/' . $param2), 'refresh');
        }
        $page_data['page_name'] = "my_messages";
        $page_data['page_title'] = site_phrase('my_messages');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function my_notifications()
    {
        $page_data['page_name'] = "my_notifications";
        $page_data['page_title'] = site_phrase('my_notifications');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function my_wishlist()
    {
        if (!$this->session->userdata('cart_items')) {
            $this->session->set_userdata('cart_items', array());
        }
        $my_courses = $this->crud_model->get_courses_by_wishlists();
        $page_data['my_courses'] = $my_courses;
        $page_data['page_name'] = "my_wishlist";
        $page_data['page_title'] = site_phrase('my_wishlist');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function purchase_history()
    {
        if ($this->session->userdata('user_login') != true) {
            redirect(site_url('home'), 'refresh');
        }

        $total_rows = $this->crud_model->purchase_history($this->session->userdata('user_id'))->num_rows();
        $config = array();
        $config = pagintaion($total_rows, 10);
        $config['base_url']  = site_url('home/purchase_history');
        $this->pagination->initialize($config);
        $page_data['per_page']   = $config['per_page'];

        if (addon_status('offline_payment') == 1) :
            $this->load->model('addons/offline_payment_model');
            $page_data['pending_offline_payment_history'] = $this->offline_payment_model->pending_offline_payment($this->session->userdata('user_id'))->result_array();
        endif;

        $page_data['page_name']  = "purchase_history";
        $page_data['page_title'] = site_phrase('purchase_history');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function profile($param1 = "")
    {
        if ($this->session->userdata('user_login') != true) {
            redirect(site_url('home'), 'refresh');
        }

        if ($param1 == 'user_profile') {
            $page_data['page_name'] = "user_profile";
            $page_data['page_title'] = site_phrase('user_profile');
        } elseif ($param1 == 'user_credentials') {
            $page_data['page_name'] = "user_credentials";
            $page_data['page_title'] = site_phrase('credentials');
        } elseif ($param1 == 'user_photo') {
            $page_data['page_name'] = "update_user_photo";
            $page_data['page_title'] = site_phrase('update_user_photo');
        }
        $page_data['user_details'] = $this->user_model->get_user($this->session->userdata('user_id'));
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function update_profile($param1 = "")
    {
        if ($param1 == 'update_basics') {
            $this->user_model->edit_user($this->session->userdata('user_id'));
            redirect(site_url('home/profile/user_profile'), 'refresh');
        } elseif ($param1 == "update_credentials") {
            $this->user_model->update_account_settings($this->session->userdata('user_id'));
            redirect(site_url('home/profile/user_credentials'), 'refresh');
        } elseif ($param1 == "update_photo") {
            if (isset($_FILES['user_image']) && $_FILES['user_image']['name'] != "") {
                unlink('uploads/user_image/' . $this->db->get_where('users', array('id' => $this->session->userdata('user_id')))->row('image') . '.jpg');
                $data['image'] = md5(rand(10000, 10000000));
                $this->db->where('id', $this->session->userdata('user_id'));
                $this->db->update('users', $data);
                $this->user_model->upload_user_image($data['image']);
            }
            $this->session->set_flashdata('flash_message', site_phrase('updated_successfully'));
            redirect(site_url('home/profile/user_photo'), 'refresh');
        }
    }

    public function handleWishList($return_number = "")
    {
        if ($this->session->userdata('user_login') != 1) {
            echo false;
        } else {
            if (isset($_POST['course_id'])) {
                $course_id = $this->input->post('course_id');
                $this->crud_model->handleWishList($course_id);
            }
            if ($return_number == 'true') {
                echo sizeof($this->crud_model->getWishLists());
            } else {
                $this->load->view('frontend/' . get_frontend_settings('theme') . '/wishlist_items');
            }
        }
    }
    public function handleCartItems($return_number = "")
    {
        if (!$this->session->userdata('cart_items')) {
            $this->session->set_userdata('cart_items', array());
        }

        $course_id = $this->input->post('course_id');
        $previous_cart_items = $this->session->userdata('cart_items');
        if (in_array($course_id, $previous_cart_items)) {
            $key = array_search($course_id, $previous_cart_items);
            unset($previous_cart_items[$key]);
        } else {
            array_push($previous_cart_items, $course_id);
        }

        $this->session->set_userdata('cart_items', $previous_cart_items);
        if ($return_number == 'true') {
            echo sizeof($previous_cart_items);
        } else {
            $this->load->view('frontend/' . get_frontend_settings('theme') . '/cart_items');
        }
    }

    public function handleCartItemForBuyNowButton()
    {
        if (!$this->session->userdata('cart_items')) {
            $this->session->set_userdata('cart_items', array());
        }

        $course_id = $this->input->post('course_id');
        $previous_cart_items = $this->session->userdata('cart_items');
        if (!in_array($course_id, $previous_cart_items)) {
            array_push($previous_cart_items, $course_id);
        }
        $this->session->set_userdata('cart_items', $previous_cart_items);
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/cart_items');
    }

    public function refreshWishList()
    {
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/wishlist_items');
    }

    public function refreshShoppingCart()
    {
        $page_data['coupon_code'] = $this->input->post('couponCode');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/shopping_cart_inner_view', $page_data);
    }

    public function isLoggedIn()
    {
        if ($this->session->userdata('user_login') == 1)
            echo true;
        else
            echo false;
    }

    //choose payment gateway
    public function payment()
    {


        if ($this->session->userdata('user_login') != 1)
            redirect('login', 'refresh');

        $page_data['total_price_of_checking_out'] = $this->session->userdata('total_price_of_checking_out');
        $page_data['page_title'] = site_phrase("payment_gateway");

        $this->load->view('payment/index', $page_data);
    }


    // SHOW MTN CHECKOUT PAGE
    public function mtn_checkout($payment_request = "only_for_mobile",$pending="non")
    {

//        if ($pending =="oui"){
//            var_dump($pending);
//            die();
//        }
        if ($this->session->userdata('user_login') != 1 && $payment_request != 'true')
            redirect('home', 'refresh');

        //echo in view or controller
        $this->session->flashdata('flash_message');

        //checking price
        if ($this->session->userdata('total_price_of_checking_out') == $this->input->post('total_price_of_checking_out')) :
            $total_price_of_checking_out = $this->input->post('total_price_of_checking_out');
        else :
            $total_price_of_checking_out = $this->session->userdata('total_price_of_checking_out');
        endif;
        $page_data['payment_request'] = $payment_request;
        $page_data['user_details']    = $this->user_model->get_user($this->session->userdata('user_id'))->row_array();
        $page_data['amount_to_pay']   = $total_price_of_checking_out;
        $page_data['page_title'] = site_phrase("mtn_checkout");
        $page_data['page_name'] = site_phrase("mtn_checkout");
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/mtn_checkout', $page_data);
    }
    // MTN CHECKOUT ACTIONS
    public function mtn_payment_traite($user_id = "", $amount_paid = "",  $tatus="", $payment_request_mobile = "")
    {
        $this->load->library('PHPRequests');
       // Stripe API configuration
        $mtn_keys = get_settings('mtn');
        $values = json_decode($mtn_keys);
        if ($values[0]->mode == 'sandbox') {
            $subscription_Key = $values[0]->sandbox_mtn_subscription_key;
            $mtn_user_id = $values[0]->sandbox_mtn_user_id;
            $mtn_api_key = $values[0]->sandbox_mtn_api_key;
            $mode="sandbox";
        } else {
            $subscription_Key = $values[0]->production_mtn_subscription_key;
            $mtn_user_id = $values[0]->production_mtn_user_id;
            $mtn_api_key = $values[0]->production_mtn_api_key;
            $mode="mtnbenin";
        }

        define('MODE', $mode);
        define('MTN_API_KEY', $mtn_api_key);
        define('MTN_USE_ID', $mtn_user_id);
        define('MTN_SUBSCRIPTION_KEY', $subscription_Key);

        $phone='';

        switch ($this->input->post('operateur')){

            case "bj":
                $phone="229".$this->input->post('phone');
                break;

            case "ci":
                $phone='225'.$this->input->post('phone');
                break;

        }


        $token =gen_token(MTN_SUBSCRIPTION_KEY,MTN_USE_ID,MTN_API_KEY);


//        var_dump( $token, $values);
//        die();

        if ($token !=null){

            $dataResponse=  $rep1= send_request_to_pay($token,MTN_SUBSCRIPTION_KEY,$this->input->post('amount'),$phone,MODE);

            $data2["status"]=isset($dataResponse->status) ? $dataResponse->status : "";
            $data2['amount'] =isset($dataResponse->amount) ? $dataResponse->amount : "";
            $data2['currency'] =isset($dataResponse->currency) ? $dataResponse->currency :"";
            $data2['financialTransactionId']  = isset($dataResponse->financialTransactionId ) ?  $dataResponse->financialTransactionId : "";
            $data2['payeeNote'] = isset($dataResponse->payeeNote) ? $dataResponse->payeeNote : "null";
            $data2['payer']=isset($dataResponse->partyId) ? $dataResponse->partyId : "";
            $data2['externalId']=isset($dataResponse->externalId) ? $dataResponse->externalId :"" ;
            $data2['payerMessage']  =isset($dataResponse->payerMessage) ? $dataResponse->payerMessage :"";
            $data2['reason']  =isset($dataResponse->reason) ? $dataResponse->reason :"null";
            $data2['user_id']=$this->input->post('user_id');
            $data2['date']= date("d/m/Y");

            $this->db->insert('mtn_history',  $data2);

 

          if ($rep1 !=null && $rep1['response']->status_code==202 ){

              sleep(7);

              $rep2= get_request_to_pay_status($token,MTN_SUBSCRIPTION_KEY,$rep1['X_Reference_Id'],MODE);

              if ($rep2 !=null ){

                  if ($rep2->status=="SUCCESSFUL"){
                      redirect('home/mtn_payment/'. $data2['user_id'].'/'.$data2['amount'], 'refresh');
                  }elseif ($rep2->status=="FAILED") {
                      $this->session->set_flashdata('info_message', site_phrase('Probleme_inattendu_veuillez_reprendre'));
                      redirect('home/mtn_checkout/' , 'refresh');

                  }elseif ($rep2->status=="REJECTED"){
                      $this->session->set_flashdata('info_message', site_phrase('approbation_refuser'));
                      redirect('home/mtn_checkout/' , 'refresh');

                  }elseif ($rep2->status=="TIMEOUT"){
                      $this->session->set_flashdata('info_message', site_phrase('temps_expire'));
                      redirect('home/mtn_checkout/' , 'refresh');

                  }elseif ($rep2->status=="PENDING"){

                      sleep(15);
                      $rep3= get_request_to_pay_status($token,MTN_SUBSCRIPTION_KEY,$rep1['X_Reference_Id'],MODE);
                      if ($rep3 !=null ){

                          if ($rep3->status=="SUCCESSFUL"){
                              redirect('home/mtn_payment/'. $data2['user_id'].'/'.$data2['amount'], 'refresh');

                          }elseif ($rep3->status=="FAILED") {
                              $this->session->set_flashdata('info_message', site_phrase('Probleme_inattendu_veuillez_reprendre'));
                              redirect('home/mtn_checkout/' , 'refresh');

                          }elseif ($rep3->status=="REJECTED"){
                              $this->session->set_flashdata('info_message', site_phrase('approbation_refuser'));
                              redirect('home/mtn_checkout/' , 'refresh');

                          }elseif ($rep3->status=="TIMEOUT"){
                              $this->session->set_flashdata('info_message', site_phrase('temps_expire'));
                              redirect('home/mtn_checkout/' , 'refresh');

                          }elseif ($rep3->status=="PENDING"){

                              $this->session->set_flashdata('info_message', site_phrase('temps_expire'));
                              redirect('home/mtn_checkout/' , 'refresh');

                          }


                      }

                  }


              }



          }



        }






        /*
        try
        {
            $headers1 = array('Accept' => 'application/json', "Ocp-Apim-Subscription-Key"=>MTN_SUBSCRIPTION_KEY, );
            $options1 = array('auth' => array(MTN_USE_ID, MTN_API_KEY));
            $response= Requests::post("https://sandbox.momodeveloper.mtn.com/collection/token/",$headers1,array(),  $options1);
            $token = json_decode($response->body)->access_token;

            try{
                $X_Reference_Id = $this->guidv4();
                $headers2 = array(
                    "Authorization" => "Bearer ".$token,
                    "X-Reference-Id"=> $X_Reference_Id ,
                    "X-Target-Environment" => "sandbox",
                    "Content-Type" => "application/json",
                    "Ocp-Apim-Subscription-Key"=>MTN_SUBSCRIPTION_KEY,

                );
                $data = json_encode([
                    "amount"=>$this->input->post('amount'),
                    "currency"=> "EUR",
                    "externalId"=> "112",
                    "payer"=>[
                        "partyIdType"=> "MSISDN",
                        "partyId"=>$phone,
                    ],
                    "payerMessage"=>  "Payement de cours sur Educ-pro academy",
                    "payeeNote"=>  "payer note",

                ]);
                $response2=Requests::post("https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay", $headers2,$data, []);

                try{
                    $headers3 = array('Accept' => 'application/json',
                        "Ocp-Apim-Subscription-Key"=>MTN_SUBSCRIPTION_KEY,
                        'X-Target-Environment' => 'sandbox',
                        'Authorization' => 'Bearer '.$token,
                    );


                    $response3= Requests::get('https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay/'.$X_Reference_Id, $headers3  );

                    $dataResponse= json_decode($response3->body);

//                      var_dump($dataResponse);
//                     die();


                    $data2["status"]=isset($dataResponse->status) ? $dataResponse->status : "";
//                    $data2['amount'] =isset($dataResponse->amount) ? $dataResponse->amount : "";
//                    $data2['currency'] =isset($dataResponse->currency) ? $dataResponse->currency :"";
//                    $data2['financialTransactionId']  = isset($dataResponse->financialTransactionId ) ?  $dataResponse->financialTransactionId : "";
//                    $data2['payeeNote'] = isset($dataResponse->payeeNote) ? $dataResponse->payeeNote : "null";
//                    $data2['payer']=isset($dataResponse->partyId) ? $dataResponse->partyId : "";
//                    $data2['externalId']=isset($dataResponse->externalId) ? $dataResponse->externalId :"" ;
//                    $data2['payerMessage']  =isset($dataResponse->payerMessage) ? $dataResponse->payerMessage :"";
//                    $data2['reason']  =isset($dataResponse->reason) ? $dataResponse->reason :"null";
//
//                    $data2['user_id']=$this->input->post('user_id');
//                    $data2['date']= date("d/m/Y");
//                    $this->db->insert('mtn_history',  $data2);



                    if (isset($data2['status']) && $data2['status'] !="FAILED"){



                        if ( $data2['status']=="SUCCESSFUL"){

                            redirect('home/mtn_payment/'. $data2['user_id'].'/'.$data2['amount'], 'refresh');

                        }

                        if ( $data2['status']=="PENDING"){
                            sleep(15);


                            $this->session->set_flashdata('info_message', site_phrase('requette_en_attente'));
                            redirect('home/mtn_checkout/e/oui' , 'refresh');
                        }


                    }else{

                        if (isset($data2['reason'])){

                        switch ( $data2['reason']){

                            case "APPROVAL_REJECTED":
                                $this->session->set_flashdata('info_message', site_phrase('approbation_refuser'));
                                redirect('home/mtn_checkout/' , 'refresh');
                                break;
                            case "EXPIRED":
                                $this->session->set_flashdata('info_message', site_phrase('temps_expire'));
                                redirect('home/mtn_checkout/' , 'refresh');
                                break;

                            default:
                                $this->session->set_flashdata('info_message', site_phrase('Probleme_inattendu_veuillez_reprendre'));
                                redirect('home/mtn_checkout/' , 'refresh');



                        }

                        }
                    }


                    if (isset($dataResponse->code) && $dataResponse->code=="RESOURCE_NOT_FOUND"){

                        $this->session->set_flashdata('info_message', site_phrase('session_time_out_probleme_reseau'));
                        redirect('home/mtn_checkout/' , 'refresh');

                    }


            //   var_dump($dataResponse);

                }
                catch (HttpException $ex3)
                {
                    echo $ex3;
                    $this->session->set_flashdata('info_message', site_phrase('Probleme_inattendu_veuillez_reprendre'));
                    redirect('home/mtn_checkout/' , 'refresh');
                }

            }
            catch (HttpException $ex2)
            {
                echo $ex2;
                $this->session->set_flashdata('info_message', site_phrase('Probleme_inattendu_veuillez_reprendre'));
                redirect('home/mtn_checkout/' , 'refresh');
            }

        }
        catch (HttpException $ex1)
        {
            echo $ex1;
            $this->session->set_flashdata('info_message', site_phrase('Probleme_inattendu_veuillez_reprendre'));
            redirect('home/mtn_checkout/' , 'refresh');
        }


        */

    }

// MTN MOMO callback
    public function mtn_momo_calback(){


echo "test mtn_momo_calback Educ-pro";

        /*
                   $data2["status"]=isset($dataResponse->status) ? $dataResponse->status : "";
                   $data2['amount'] =isset($dataResponse->amount) ? $dataResponse->amount : "";
                   $data2['currency'] =isset($dataResponse->currency) ? $dataResponse->currency :"";
                   $data2['financialTransactionId']  = isset($dataResponse->financialTransactionId ) ?  $dataResponse->financialTransactionId : "";
                   $data2['payeeNote'] = isset($dataResponse->payeeNote) ? $dataResponse->payeeNote : "null";
                   $data2['payer']=isset($dataResponse->partyId) ? $dataResponse->partyId : "";
                   $data2['externalId']=isset($dataResponse->externalId) ? $dataResponse->externalId :"" ;
                   $data2['payerMessage']  =isset($dataResponse->payerMessage) ? $dataResponse->payerMessage :"";
                   $data2['reason']  =isset($dataResponse->reason) ? $dataResponse->reason :"null";

                   $data2['user_id']=$this->input->post('user_id');
                   $data2['date']= date("d/m/Y");
                   $this->db->insert('mtn_history',  $data2);



                   if (isset($data2['status']) && $data2['status'] !="FAILED"){


                       redirect('home/mtn_payment/'. $data2['user_id'].'/'.$data2['amount'], 'refresh');

                   }else{

                       if (isset($data2['reason'])){



                       switch ( $data2['reason']){

                           case "APPROVAL_REJECTED":
                               $this->session->set_flashdata('info_message', site_phrase('approbation_refuser'));
                               redirect('home/mtn_checkout/' , 'refresh');
                               break;
                           case "EXPIRED":
                               $this->session->set_flashdata('info_message', site_phrase('temps_expire'));
                               redirect('home/mtn_checkout/' , 'refresh');
                               break;

                           default:
                               $this->session->set_flashdata('info_message', site_phrase('Probleme_inattendu_veuillez_reprendre'));
                               redirect('home/mtn_checkout/' , 'refresh');



                       }

                       }
                   }


                   if (isset($dataResponse->code) && $dataResponse->code=="RESOURCE_NOT_FOUND"){

                       $this->session->set_flashdata('info_message', site_phrase('session_time_out_probleme_reseau'));
                       redirect('home/mtn_checkout/' , 'refresh');

                   }

                   */

    }

 // MTN CHECKOUT ACTIONS
    public function mtn_payment($user_id = "", $amount_paid = "",  $tatus="", $payment_request_mobile = "")
    {


        $this->crud_model->enrol_student($user_id);
        $this->crud_model->course_purchase($user_id, 'mtn', $amount_paid);

        try{
            $this->email_model->course_purchase_notification($user_id, 'mtn', $amount_paid);
        }catch (Exception $e){
        echo $e;
    }

        $this->session->set_flashdata('flash_message', site_phrase('payment_successfully_done'));

        $this->session->set_userdata('cart_items', array());
        redirect('home/my_courses', 'refresh');

    }

    public function save_mtm_history($user_id=""){


        $data['status']=$this->input->post('status');
        $data['amount'] =$this->input->post('amount');
        $data['currency'] =$this->input->post('currency');
        $data['financialTransactionId']  =$this->input->post('financialTransactionId');
        $data['payeeNote'] =$this->input->post('payeeNote');
        $data['payer']=$this->input->post('payer');
        $data['externalId']=$this->input->post('externalId');
        $data['payerMessage']  =$this->input->post('payerMessage');
        $data['reason']  =$this->input->post('reason');

        $data['user_id']=$this->input->post('user_id');
        $this->db->insert('mtn_history', $data);

        echo $data['status'];
    }


    // SHOW FEDAPAY  CHECKOUT PAGE
    public function fedapay_checkout($payment_request = "only_for_mobile")
    {
        if ($this->session->userdata('user_login') != 1 && $payment_request != 'true')
            redirect('home', 'refresh');

        //checking price
        if ($this->session->userdata('total_price_of_checking_out') == $this->input->post('total_price_of_checking_out')) :
            $total_price_of_checking_out = $this->input->post('total_price_of_checking_out');
        else :
            $total_price_of_checking_out = $this->session->userdata('total_price_of_checking_out');
        endif;
        $page_data['payment_request'] = $payment_request;
        $page_data['user_details']    = $this->user_model->get_user($this->session->userdata('user_id'))->row_array();
        $page_data['amount_to_pay']   = $total_price_of_checking_out;
        $page_data['page_title'] = site_phrase("fedapay_checkout");
        $page_data['page_name'] = site_phrase("fedapay_checkout");
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/fedapay_checkout', $page_data);
    }

    // PAYPAL CHECKOUT ACTIONS
    public function fedapay_payment($user_id = "", $amount_paid = "", $payment_request_mobile = "",$status=false,
                                    $feda_transaction_id="", $feda_reference="",$customer_id="",$commission="",
                                    $mode="",$fees="",$full_name="",$fixed_commission="",$created_at="")
      {



        //THIS IS HOW I CHECKED THE PAYPAL PAYMENT STATUS
          if (!$status) {

            $this->session->set_flashdata('error_message', site_phrase('an_error_occurred_during_payment'));
            redirect('home/shopping_cart', 'refresh');
        }

          if ($feda_transaction_id !="" && $feda_reference != ""){

              $data['feda_transaction_id']=$feda_transaction_id;
              $data['feda_reference']=$feda_reference;
              $data['customer_id']=$customer_id;
              $data['commission']=$commission;
              $data['mode']=$mode;
              $data['full_name']=$full_name;
              $data['fees']=$fees;
              $data['fixed_commission']=$fixed_commission;
              $data['created_at']=$created_at;

              $this->session->set_userdata('transaction_info',  $data);

              $this->db->insert('fedapay_history',  array("data"=>json_encode($data)));
          }



     $this->crud_model->enrol_student($user_id);
       $this->crud_model->course_purchase($user_id, 'fedapay', $amount_paid);
      $this->email_model->course_purchase_notification($user_id, 'fedapay', $amount_paid);

      $this->session->set_flashdata('flash_message', site_phrase('payment_successfully_done'));


        if ($payment_request_mobile == 'true') :
            $course_id = $this->session->userdata('cart_items');
            redirect('home/payment_success_mobile/' . $course_id[0] . '/' . $user_id . '/paid', 'refresh');
        else :
            $this->session->set_userdata('cart_items', array());
            redirect('home/my_courses', 'refresh');
        endif;
    }







    // SHOW PAYPAL CHECKOUT PAGE
    public function paypal_checkout($payment_request = "only_for_mobile")
    {
        if ($this->session->userdata('user_login') != 1 && $payment_request != 'true')
            redirect('home', 'refresh');

        //checking price
        if ($this->session->userdata('total_price_of_checking_out') == $this->input->post('total_price_of_checking_out')) :
            $total_price_of_checking_out = $this->input->post('total_price_of_checking_out');
        else :
            $total_price_of_checking_out = $this->session->userdata('total_price_of_checking_out');
        endif;
        $page_data['payment_request'] = $payment_request;
        $page_data['user_details']    = $this->user_model->get_user($this->session->userdata('user_id'))->row_array();
        $page_data['amount_to_pay']   = $total_price_of_checking_out;
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/paypal_checkout', $page_data);
    }

    // PAYPAL CHECKOUT ACTIONS
    public function paypal_payment($user_id = "", $amount_paid = "", $paymentID = "", $paymentToken = "", $payerID = "", $payment_request_mobile = "")
    {
        $paypal_keys = get_settings('paypal');
        $paypal = json_decode($paypal_keys);

        if ($paypal[0]->mode == 'sandbox') {
            $paypalClientID = $paypal[0]->sandbox_client_id;
            $paypalSecret   = $paypal[0]->sandbox_secret_key;
        } else {
            $paypalClientID = $paypal[0]->production_client_id;
            $paypalSecret   = $paypal[0]->production_secret_key;
        }

        //THIS IS HOW I CHECKED THE PAYPAL PAYMENT STATUS
        $status = $this->payment_model->paypal_payment($paymentID, $paymentToken, $payerID, $paypalClientID, $paypalSecret);
        if (!$status) {
            $this->session->set_flashdata('error_message', site_phrase('an_error_occurred_during_payment'));
            redirect('home/shopping_cart', 'refresh');
        }
        $this->crud_model->enrol_student($user_id);
        $this->crud_model->course_purchase($user_id, 'paypal', $amount_paid);
        $this->email_model->course_purchase_notification($user_id, 'paypal', $amount_paid);
        $this->session->set_flashdata('flash_message', site_phrase('payment_successfully_done'));
        if ($payment_request_mobile == 'true') :
            $course_id = $this->session->userdata('cart_items');
            redirect('home/payment_success_mobile/' . $course_id[0] . '/' . $user_id . '/paid', 'refresh');
        else :
            $this->session->set_userdata('cart_items', array());
            redirect('home/my_courses', 'refresh');
        endif;
    }

    // SHOW STRIPE CHECKOUT PAGE
    public function stripe_checkout($payment_request = "only_for_mobile")
    {
        if ($this->session->userdata('user_login') != 1 && $payment_request != 'true')
            redirect('home', 'refresh');

        //checking price
        $total_price_of_checking_out = $this->session->userdata('total_price_of_checking_out');
        $page_data['payment_request'] = $payment_request;
        $page_data['user_details']    = $this->user_model->get_user($this->session->userdata('user_id'))->row_array();
        $page_data['amount_to_pay']   = $total_price_of_checking_out;
        $this->load->view('payment/stripe/stripe_checkout', $page_data);
    }

    // STRIPE CHECKOUT ACTIONS
    public function stripe_payment($user_id = "", $payment_request_mobile = "", $session_id = "")
    {
        //THIS IS HOW I CHECKED THE STRIPE PAYMENT STATUS
        $response = $this->payment_model->stripe_payment($user_id, $session_id);

        if ($response['payment_status'] === 'succeeded') {
            // STUDENT ENROLMENT OPERATIONS AFTER A SUCCESSFUL PAYMENT
            $check_duplicate = $this->crud_model->check_duplicate_payment_for_stripe($response['transaction_id'], $session_id);
            if ($check_duplicate == false) :
                $this->crud_model->enrol_student($user_id);
                $this->crud_model->course_purchase($user_id, 'stripe', $response['paid_amount'], $response['transaction_id'], $session_id);
                $this->email_model->course_purchase_notification($user_id, 'stripe', $response['paid_amount']);
            else :
                //duplicate payment
                $this->session->set_flashdata('error_message', site_phrase('session_time_out'));
                redirect('home/shopping_cart', 'refresh');
            endif;

            if ($payment_request_mobile == 'true') :
                $course_id = $this->session->userdata('cart_items');
                $this->session->set_flashdata('flash_message', site_phrase('payment_successfully_done'));
                redirect('home/payment_success_mobile/' . $course_id[0] . '/' . $user_id . '/paid', 'refresh');
            else :
                $this->session->set_userdata('cart_items', array());
                $this->session->set_flashdata('flash_message', site_phrase('payment_successfully_done'));
                redirect('home/my_courses', 'refresh');
            endif;
        } else {
            if ($payment_request_mobile == 'true') :
                $course_id = $this->session->userdata('cart_items');
                $this->session->set_flashdata('flash_message', $response['status_msg']);
                redirect('home/payment_success_mobile/' . $course_id[0] . '/' . $user_id . '/error', 'refresh');
            else :
                $this->session->set_flashdata('error_message', $response['status_msg']);
                redirect('home/shopping_cart', 'refresh');
            endif;
        }
    }



    // SHOW PAYDUNYA CHECKOUT PAGE
    public function paydunya_checkout($payment_request = "only_for_mobile")
    {
        if ($this->session->userdata('user_login') != 1 && $payment_request != 'true')
            redirect('home', 'refresh');

        //checking price
        if ($this->session->userdata('total_price_of_checking_out') == $this->input->post('total_price_of_checking_out')) :
            $total_price_of_checking_out = $this->input->post('total_price_of_checking_out');
        else :
            $total_price_of_checking_out = $this->session->userdata('total_price_of_checking_out');
        endif;
        $page_data['payment_request'] = $payment_request;
        $page_data['user_details']    = $this->user_model->get_user($this->session->userdata('user_id'))->row_array();
        $page_data['amount_to_pay']   = $total_price_of_checking_out;
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/paydunya_checkout', $page_data);
    }

    public function paydunya_lastCallback(){
        $row = $this->db->select("*")->limit(1)->order_by('id',"DESC")->get("paydunya_callback")->row();

        $data["token"]=$row->token;
        $data["status"]=$row->status;
        echo json_encode( $data);


      }


    public function paydunya_paymentStatus(){

        // Paydunya API configuration
       $paydunya_keys = get_settings('paydunya');
       $values = json_decode($paydunya_keys);
 
           $MasterKey = $values[0]->MasterKey;
          

        // $MasterKey ="FwVgpDNQ-Eer9-Dqfd-Mff2-3Dgf4exQ1iQj";

       try {
           //Prenez votre MasterKey, hashez la et comparez le résultat au hash reçu par IPN
           if($_POST['data']['hash'] === hash('sha512', $MasterKey)) {

               if ($_POST['data']['status'] == "completed") {
                   //Faites vos traitements backoffice ici...


                 $data['date']= date("d/m/Y") ;
                 $data['data']= json_encode($_POST['data']);
                 $data['status']= $_POST['data']['status'];
                 $data['token']= $_POST['data']['invoice']['token'];
                 $this->db->insert('paydunya_callback',  $data);

               
 
                    
               }

           } else {
               die("Cette requête n'a pas été émise par PayDunya");
           }
       } catch(Exception $e) {
           die();
       }

       require APPPATH.'libraries/Paydunya/paydunya.php';
       $invoice = new Paydunya_Checkout_Invoice();
       $token= $this->input->post('token');
       $invoice = new Paydunya_Checkout_Invoice();
       if ($invoice->confirm($token)) {

           // Récupérer le statut du paiement
           // Le statut du paiement peut être soit completed, pending, cancelled
           echo $invoice->getStatus();
       }else{
           echo $invoice->getStatus();
           echo $invoice->response_text;
           echo $invoice->response_code;
       }

        }

    //PAYDUNYA CHECKOUT ACTIONS
    public function paydunya_payment($user_id = "", $amount_paid="",$ref="",$token="",$status="",$payment_request_mobile = "", $session_id = "")
    {


        $data['date']= date("d/m/Y") ;
        $data['ref']= $ref;
        $data['token']= $token;
        $data['status']= $status;
        $data['user_id']= $user_id;
        $this->db->insert('paydunya_history',  $data);



        $this->crud_model->enrol_student($user_id);
        $this->crud_model->course_purchase($user_id, 'paydunya', $amount_paid);

        try{
            $this->email_model->course_purchase_notification($user_id, 'paydunya', $amount_paid);
        }catch (Exception $e){
            echo $e;
        }

        $this->session->set_flashdata('flash_message', site_phrase('payment_successfully_done'));

        $this->session->set_userdata('cart_items', array());
        redirect('home/my_courses', 'refresh');
    }

   //PAYDUNYA paydunya-api
    public function paydunya_api( )
    {


        echo $this->session->userdata('paydunya_data');
    }


    public function lesson($slug = "", $course_id = "", $lesson_id = "")
    {
        if ($this->session->userdata('user_login') != 1) {
            if ($this->session->userdata('admin_login') != 1) {
                redirect('home', 'refresh');
            }
        }

        $course_details = $this->crud_model->get_course_by_id($course_id)->row_array();

        if ($course_details['course_type'] == 'general') {
            $sections = $this->crud_model->get_section('course', $course_id);
            if ($sections->num_rows() > 0) {
                $page_data['sections'] = $sections->result_array();
                if ($lesson_id == "") {
                    $default_section = $sections->row_array();
                    $page_data['section_id'] = $default_section['id'];
                    $lessons = $this->crud_model->get_lessons('section', $default_section['id']);
                    if ($lessons->num_rows() > 0) {
                        $default_lesson = $lessons->row_array();
                        $lesson_id = $default_lesson['id'];
                        $page_data['lesson_id']  = $default_lesson['id'];
                    }
                } else {
                    $page_data['lesson_id']  = $lesson_id;
                    $section_id = $this->db->get_where('lesson', array('id' => $lesson_id))->row()->section_id;
                    $page_data['section_id'] = $section_id;
                }
            } else {
                $page_data['sections'] = array();
            }
        } else if ($course_details['course_type'] == 'scorm') {
            $this->load->model('addons/scorm_model');
            $scorm_course_data = $this->scorm_model->get_scorm_curriculum_by_course_id($course_id);
            $page_data['scorm_curriculum'] = $scorm_course_data->row_array();
        }

        // Check if the lesson contained course is purchased by the user
        if (isset($page_data['lesson_id']) && $page_data['lesson_id'] > 0 && $course_details['course_type'] == 'general') {
            if ($this->session->userdata('role_id') != 1 && $course_details['user_id'] != $this->session->userdata('user_id')) {
                if (!is_purchased($course_id)) {
                    redirect(site_url('home/course/' . slugify($course_details['title']) . '/' . $course_details['id']), 'refresh');
                }
            }
        } else if ($course_details['course_type'] == 'scorm' && $scorm_course_data->num_rows() > 0) {
            if ($this->session->userdata('role_id') != 1 && $course_details['user_id'] != $this->session->userdata('user_id')) {
                if (!is_purchased($course_id)) {
                    redirect(site_url('home/course/' . slugify($course_details['title']) . '/' . $course_details['id']), 'refresh');
                }
            }
        } else {
            if (!is_purchased($course_id)) {
                redirect(site_url('home/course/' . slugify($course_details['title']) . '/' . $course_details['id']), 'refresh');
            }
        }


        $page_data['course_details']  = $course_details;
        $page_data['course_id']  = $course_id;
        $page_data['page_name']  = 'lessons';
        $page_data['page_title'] = $course_details['title'];
        $this->load->view('lessons/index', $page_data);
    }

    public function my_courses_by_category()
    {
        $category_id = $this->input->post('category_id');
        $course_details = $this->crud_model->get_my_courses_by_category_id($category_id)->result_array();
        $page_data['my_courses'] = $course_details;
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/reload_my_courses', $page_data);
    }

    public function search($search_string = "")
    {
        if (isset($_GET['query']) && !empty($_GET['query'])) {
            $search_string = $_GET['query'];
            $page_data['courses'] = $this->crud_model->get_courses_by_search_string($search_string)->result_array();
        } else {
            $this->session->set_flashdata('error_message', site_phrase('no_search_value_found'));
            redirect(site_url(), 'refresh');
        }

        if (!$this->session->userdata('layout')) {
            $this->session->set_userdata('layout', 'list');
        }
        $page_data['layout']     = $this->session->userdata('layout');
        $page_data['page_name'] = 'courses_page';
        $page_data['search_string'] = $search_string;
        $page_data['page_title'] = site_phrase('search_results');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }
    public function my_courses_by_search_string()
    {
        $search_string = $this->input->post('search_string');
        $course_details = $this->crud_model->get_my_courses_by_search_string($search_string)->result_array();
        $page_data['my_courses'] = $course_details;
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/reload_my_courses', $page_data);
    }

    public function get_my_wishlists_by_search_string()
    {
        $search_string = $this->input->post('search_string');
        $course_details = $this->crud_model->get_courses_of_wishlists_by_search_string($search_string);
        $page_data['my_courses'] = $course_details;
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/reload_my_wishlists', $page_data);
    }

    public function reload_my_wishlists()
    {
        $my_courses = $this->crud_model->get_courses_by_wishlists();
        $page_data['my_courses'] = $my_courses;
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/reload_my_wishlists', $page_data);
    }

    public function get_course_details()
    {
        $course_id = $this->input->post('course_id');
        $course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
        echo $course_details['title'];
    }

    public function rate_course()
    {
        $data['review'] = $this->input->post('review');
        $data['ratable_id'] = $this->input->post('course_id');
        $data['ratable_type'] = 'course';
        $data['rating'] = $this->input->post('starRating');
        $data['date_added'] = strtotime(date('D, d-M-Y'));
        $data['user_id'] = $this->session->userdata('user_id');
        $this->crud_model->rate($data);
    }

    public function about_us()
    {
        $page_data['page_name'] = 'about_us';
        $page_data['page_title'] = site_phrase('about_us');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function terms_and_condition()
    {
        $page_data['page_name'] = 'terms_and_condition';
        $page_data['page_title'] = site_phrase('terms_and_condition');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function privacy_policy()
    {
        $page_data['page_name'] = 'privacy_policy';
        $page_data['page_title'] = site_phrase('privacy_policy');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }
    public function cookie_policy()
    {
        $page_data['page_name'] = 'cookie_policy';
        $page_data['page_title'] = site_phrase('cookie_policy');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }


    // Version 1.1
    public function dashboard($param1 = "")
    {
        if ($this->session->userdata('user_login') != 1) {
            redirect('home', 'refresh');
        }

        if ($param1 == "") {
            $page_data['type'] = 'active';
        } else {
            $page_data['type'] = $param1;
        }

        $page_data['page_name']  = 'instructor_dashboard';
        $page_data['page_title'] = site_phrase('instructor_dashboard');
        $page_data['user_id']    = $this->session->userdata('user_id');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function create_course()
    {
        if ($this->session->userdata('user_login') != 1) {
            redirect('home', 'refresh');
        }

        $page_data['page_name'] = 'create_course';
        $page_data['page_title'] = site_phrase('create_course');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function edit_course($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('user_login') != 1) {
            redirect('home', 'refresh');
        }

        if ($param2 == "") {
            $page_data['type']   = 'edit_course';
        } else {
            $page_data['type']   = $param2;
        }
        $page_data['page_name']  = 'manage_course_details';
        $page_data['course_id']  = $param1;
        $page_data['page_title'] = site_phrase('edit_course');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function course_action($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('user_login') != 1) {
            redirect('home', 'refresh');
        }

        if ($param1 == 'create') {
            if (isset($_POST['create_course'])) {
                $this->crud_model->add_course();
                redirect(site_url('home/create_course'), 'refresh');
            } else {
                $this->crud_model->add_course('save_to_draft');
                redirect(site_url('home/create_course'), 'refresh');
            }
        } elseif ($param1 == 'edit') {
            if (isset($_POST['publish'])) {
                $this->crud_model->update_course($param2, 'publish');
                redirect(site_url('home/dashboard'), 'refresh');
            } else {
                $this->crud_model->update_course($param2, 'save_to_draft');
                redirect(site_url('home/dashboard'), 'refresh');
            }
        }
    }


    public function sections($action = "", $course_id = "", $section_id = "")
    {
        if ($this->session->userdata('user_login') != 1) {
            redirect('home', 'refresh');
        }

        if ($action == "add") {
            $this->crud_model->add_section($course_id);
        } elseif ($action == "edit") {
            $this->crud_model->edit_section($section_id);
        } elseif ($action == "delete") {
            $this->crud_model->delete_section($course_id, $section_id);
            $this->session->set_flashdata('flash_message', site_phrase('section_deleted'));
            redirect(site_url("home/edit_course/$course_id/manage_section"), 'refresh');
        } elseif ($action == "serialize_section") {
            $container = array();
            $serialization = json_decode($this->input->post('updatedSerialization'));
            foreach ($serialization as $key) {
                array_push($container, $key->id);
            }
            $json = json_encode($container);
            $this->crud_model->serialize_section($course_id, $json);
        }
        $page_data['course_id'] = $course_id;
        $page_data['course_details'] = $this->crud_model->get_course_by_id($course_id)->row_array();
        return $this->load->view('frontend/' . get_frontend_settings('theme') . '/reload_section', $page_data);
    }

    public function manage_lessons($action = "", $course_id = "", $lesson_id = "")
    {
        if ($this->session->userdata('user_login') != 1) {
            redirect('home', 'refresh');
        }
        if ($action == 'add') {
            $this->crud_model->add_lesson();
            $this->session->set_flashdata('flash_message', site_phrase('lesson_added'));
        } elseif ($action == 'edit') {
            $this->crud_model->edit_lesson($lesson_id);
            $this->session->set_flashdata('flash_message', site_phrase('lesson_updated'));
        } elseif ($action == 'delete') {
            $this->crud_model->delete_lesson($lesson_id);
            $this->session->set_flashdata('flash_message', site_phrase('lesson_deleted'));
        }
        redirect('home/edit_course/' . $course_id . '/manage_lesson');
    }

    public function lesson_editing_form($lesson_id = "", $course_id = "")
    {
        if ($this->session->userdata('user_login') != 1) {
            redirect('home', 'refresh');
        }
        $page_data['type']      = 'manage_lesson';
        $page_data['course_id'] = $course_id;
        $page_data['lesson_id'] = $lesson_id;
        $page_data['page_name']  = 'lesson_edit';
        $page_data['page_title'] = site_phrase('update_lesson');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function download($filename = "")
    {
        $tmp           = explode('.', $filename);
        $fileExtension = strtolower(end($tmp));
        $yourFile = base_url() . 'uploads/lesson_files/' . $filename;
        $file = @fopen($yourFile, "rb");

        header('Content-Description: File Transfer');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($yourFile));
        while (!feof($file)) {
            print(@fread($file, 1024 * 8));
            ob_flush();
            flush();
        }
    }

    // Version 1.3 codes
    public function get_enrolled_to_free_course($course_id)
    {
        if ($this->session->userdata('user_login') == 1) {
            $this->crud_model->enrol_to_free_course($course_id, $this->session->userdata('user_id'));
            redirect(site_url('home/my_courses'), 'refresh');
        } else {
            redirect(site_url('login'), 'refresh');
        }
    }

    // Version 1.4 codes
    public function login()
    {
        include_once APPPATH . "libraries/vendor/autoload.php";
        $google_client = new Google_Client();

        $google_client->setClientId('928731300610-urnrpjjfnjuc1n35ptva6v6768q22bba.apps.googleusercontent.com'); //Define your ClientID

        $google_client->setClientSecret('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'); //Define your Client Secret Key

        $google_client->setRedirectUri('http://localhost/educ-pro-academy/home/login'); //Define your Redirect Uri

        $google_client->addScope('email');

        $google_client->addScope('profile');


        if ($this->session->userdata('admin_login')) {
            redirect(site_url('admin'), 'refresh');
        } elseif ($this->session->userdata('user_login'))
        {
            redirect(site_url('user'), 'refresh');
        }
        $page_data['page_name'] = 'login';
        $page_data['page_title'] = site_phrase('login');

        if(isset($_GET["code"]))
        {
            $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

            if(!isset($token["error"]))
            {
                $google_client->setAccessToken($token['access_token']);

                $this->session->set_userdata('access_token', $token['access_token']);

                $google_service = new Google_Service_Oauth2($google_client);

                $data = $google_service->userinfo->get();

                $current_datetime = date('Y-m-d H:i:s');

                if($this->google_login_model->Is_already_register($data['id']))
                {
                    //update data
                    $user_data = array(
                        'first_name' => $data['given_name'],
                        'last_name'  => $data['family_name'],
                        'email_address' => $data['email'],
                        'profile_picture'=> $data['picture'],
                        'updated_at' => $current_datetime
                    );

                    $this->google_login_model->Update_user_data($user_data, $data['id']);
                }
                else
                {
                    //insert data
                    $user_data = array(
                        'login_oauth_uid' => $data['id'],
                        'first_name'  => $data['given_name'],
                        'last_name'   => $data['family_name'],
                        'email_address'  => $data['email'],
                        'profile_picture' => $data['picture'],
                        'created_at'  => $current_datetime
                    );

                    $this->google_login_model->Insert_user_data($user_data);
                }
                $this->session->set_userdata('user_data', $user_data);
            }
        }

        $login_button = '';
        if(!$this->session->userdata('access_token'))
        {
            $login_button = '<a href="'.$google_client->createAuthUrl().'"><img src="'.base_url().'asset/sign-in-with-google.png" /></a>';
//            $data['login_button'] = $login_button;
//            $this->load->view('google_login', $data);

            $page_data['google_auth_url']=$google_client->createAuthUrl();
        }
        else
        {
//            $this->load->view('google_login', $data);
            $page_data['google_auth_url']=$google_client->createAuthUrl();;
        }


        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function sign_up()
    {
        if ($this->session->userdata('admin_login')) {
            redirect(site_url('admin'), 'refresh');
        } elseif ($this->session->userdata('user_login')) {
            redirect(site_url('user'), 'refresh');
        }
        $page_data['page_name'] = 'sign_up';
        $page_data['page_title'] = site_phrase('sign_up');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function forgot_password()
    {
        if ($this->session->userdata('admin_login')) {
            redirect(site_url('admin'), 'refresh');
        } elseif ($this->session->userdata('user_login')) {
            redirect(site_url('user'), 'refresh');
        }
        $page_data['page_name'] = 'forgot_password';
        $page_data['page_title'] = site_phrase('forgot_password');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function submit_quiz($from = "")
    {
        $submitted_quiz_info = array();
        $container = array();
        $quiz_id = $this->input->post('lesson_id');
        $quiz_questions = $this->crud_model->get_quiz_questions($quiz_id)->result_array();
        $total_correct_answers = 0;
        foreach ($quiz_questions as $quiz_question) {
            $submitted_answer_status = 0;
            $correct_answers = json_decode($quiz_question['correct_answers']);
            $submitted_answers = array();
            foreach ($this->input->post($quiz_question['id']) as $each_submission) {
                if (isset($each_submission)) {
                    array_push($submitted_answers, $each_submission);
                }
            }
            sort($correct_answers);
            sort($submitted_answers);
            if ($correct_answers == $submitted_answers) {
                $submitted_answer_status = 1;
                $total_correct_answers++;
            }
            $container = array(
                "question_id" => $quiz_question['id'],
                'submitted_answer_status' => $submitted_answer_status,
                "submitted_answers" => json_encode($submitted_answers),
                "correct_answers"  => json_encode($correct_answers),
            );
            array_push($submitted_quiz_info, $container);
        }
        $page_data['submitted_quiz_info']   = $submitted_quiz_info;
        $page_data['total_correct_answers'] = $total_correct_answers;
        $page_data['total_questions'] = count($quiz_questions);
        if ($from == 'mobile') {
            $this->load->view('mobile/quiz_result', $page_data);
        } else {
            $this->load->view('lessons/quiz_result', $page_data);
        }
    }

    private function access_denied_courses($course_id)
    {
        $course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
        if ($course_details['status'] == 'draft' && $course_details['user_id'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error_message', site_phrase('you_do_not_have_permission_to_access_this_course'));
            redirect(site_url('home'), 'refresh');
        } elseif ($course_details['status'] == 'pending') {
            if ($course_details['user_id'] != $this->session->userdata('user_id') && $this->session->userdata('role_id') != 1) {
                $this->session->set_flashdata('error_message', site_phrase('you_do_not_have_permission_to_access_this_course'));
                redirect(site_url('home'), 'refresh');
            }
        }
    }

    public function invoice($purchase_history_id = '')
    {
        if ($this->session->userdata('user_login') != 1) {
            redirect('home', 'refresh');
        }
        $purchase_history = $this->crud_model->get_payment_details_by_id($purchase_history_id);
        if ($purchase_history['user_id'] != $this->session->userdata('user_id')) {
            redirect('home', 'refresh');
        }
        $page_data['payment_info'] = $purchase_history;
        $page_data['page_name'] = 'invoice';
        $page_data['page_title'] = 'invoice';
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    /** COURSE COMPARE STARTS */
    public function compare()
    {
        $course_id_1 = (isset($_GET['course-id-1']) && !empty($_GET['course-id-1'])) ? $_GET['course-id-1'] : null;
        $course_id_2 = (isset($_GET['course-id-2']) && !empty($_GET['course-id-2'])) ? $_GET['course-id-2'] : null;
        $course_id_3 = (isset($_GET['course-id-3']) && !empty($_GET['course-id-3'])) ? $_GET['course-id-3'] : null;

        $page_data['page_name'] = 'compare';
        $page_data['page_title'] = site_phrase('course_compare');
        $page_data['courses'] = $this->crud_model->get_courses()->result_array();
        $page_data['course_1_details'] = $course_id_1 ? $this->crud_model->get_course_by_id($course_id_1)->row_array() : array();
        $page_data['course_2_details'] = $course_id_2 ? $this->crud_model->get_course_by_id($course_id_2)->row_array() : array();
        $page_data['course_3_details'] = $course_id_3 ? $this->crud_model->get_course_by_id($course_id_3)->row_array() : array();
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }
    /** COURSE COMPARE ENDS */

    public function page_not_found()
    {
        $page_data['page_name'] = '404';
        $page_data['page_title'] = site_phrase('404_page_not_found');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    // AJAX CALL FUNCTION FOR CHECKING COURSE PROGRESS
    function check_course_progress($course_id)
    {
        echo course_progress($course_id);
    }

    // This is the function for rendering quiz web view for mobile
    public function quiz_mobile_web_view($lesson_id = "")
    {
        $data['lesson_details'] = $this->crud_model->get_lessons('lesson', $lesson_id)->row_array();
        $data['page_name'] = 'quiz';
        $this->load->view('mobile/index', $data);
    }


    // CHECK CUSTOM SESSION DATA
    public function session_data()
    {
        // SESSION DATA FOR CART
        if (!$this->session->userdata('cart_items')) {
            $this->session->set_userdata('cart_items', array());
        }

        // SESSION DATA FOR FRONTEND LANGUAGE
        if (!$this->session->userdata('language')) {
            $this->session->set_userdata('language', get_settings('language'));
        }
    }

    // SETTING FRONTEND LANGUAGE
    public function site_language()
    {
        $selected_language = $this->input->post('language');
        $this->session->set_userdata('language', $selected_language);
        echo true;
    }


    //FOR MOBILE
    public function course_purchase($auth_token = '', $course_id  = '')
    {
        $this->load->model('jwt_model');
        if (empty($auth_token) || $auth_token == "null") {
            $page_data['cart_item'] = $course_id;
            $page_data['user_id'] = '';
            $page_data['is_login_now'] = 0;
            $page_data['enroll_type'] = null;
            $page_data['page_name'] = 'shopping_cart';
            $this->load->view('mobile/index', $page_data);
        } else {

            $logged_in_user_details = json_decode($this->jwt_model->token_data_get($auth_token), true);

            if ($logged_in_user_details['user_id'] > 0) {

                $credential = array('id' => $logged_in_user_details['user_id'], 'status' => 1, 'role_id' => 2);
                $query = $this->db->get_where('users', $credential);
                if ($query->num_rows() > 0) {
                    $row = $query->row();
                    $page_data['cart_item'] = $course_id;
                    $page_data['user_id'] = $row->id;
                    $page_data['is_login_now'] = 1;
                    $page_data['enroll_type'] = null;
                    $page_data['page_name'] = 'shopping_cart';

                    $cart_item = array($course_id);
                    $this->session->set_userdata('cart_items', $cart_item);
                    $this->session->set_userdata('user_login', '1');
                    $this->session->set_userdata('user_id', $row->id);
                    $this->session->set_userdata('role_id', $row->role_id);
                    $this->session->set_userdata('role', get_user_role('user_role', $row->id));
                    $this->session->set_userdata('name', $row->first_name . ' ' . $row->last_name);
                    $this->load->view('mobile/index', $page_data);
                }
            }
        }
    }

    //FOR MOBILE
    public function get_enrolled_to_free_course_mobile($course_id = "", $user_id = "", $get_request = "")
    {
        if ($get_request == "true") {
            $this->crud_model->enrol_to_free_course_mobile($course_id, $user_id);
        }
    }

    //FOR MOBILE
    public function payment_success_mobile($course_id = "", $user_id = "", $enroll_type = "")
    {
        if ($course_id > 0 && $user_id > 0) :
            $page_data['cart_item'] = $course_id;
            $page_data['user_id'] = $user_id;
            $page_data['is_login_now'] = 1;
            $page_data['enroll_type'] = $enroll_type;
            $page_data['page_name'] = 'shopping_cart';

            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('role_id');
            $this->session->unset_userdata('role');
            $this->session->unset_userdata('name');
            $this->session->unset_userdata('user_login');
            $this->session->unset_userdata('cart_items');

            $this->load->view('mobile/index', $page_data);
        endif;
    }

    //FOR MOBILE
    public function payment_gateway_mobile($course_id = "", $user_id = "")
    {
        if ($course_id > 0 && $user_id > 0) :
            $page_data['page_name'] = 'payment_gateway';
            $this->load->view('mobile/index', $page_data);
        endif;
    }
}
