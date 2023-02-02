<!DOCTYPE html>
<html lang="en">
<head>
    <title>Paypal | <?php echo get_settings('system_name'); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo base_url('assets/frontend/default/css/bootstrap.min.css'); ?>" rel="stylesheet">

<!--   <link href="<?php echo base_url('assets/payment/css/mtn.css'); ?>" rel="stylesheet"> -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">

    <link name="favicon" type="image/x-icon"
          href="<?php echo base_url('uploads/system/' . get_frontend_settings('favicon')); ?>" rel="shortcut icon"/>

    <link rel="stylesheet" href="<?php echo base_url() . 'assets/global/toastr/toastr.css' ?>">
    <script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js'); ?>"></script>
    <script src="<?php echo base_url() . 'assets/frontend/default/js/main.js'; ?>"></script>
    <script src="<?php echo base_url() . 'assets/global/toastr/toastr.min.js'; ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="https://paydunya.com/assets/psr/css/psr.paydunya.min.css">


    <style type="text/css">
      
      body{
          background: #424770;
      }
    </style>

</head>

<body>
  
  <div style="  bottom: 0px;  left: 50%; right: 50%; text-align: center; z-index: -4">

    <img width="60px" src="<?php echo base_url() . 'assets/payment/loader.gif' ?>">
    
  </div>

  <?php


  // Include Stripe PHP library
  require APPPATH.'libraries/Paydunya/paydunya.php';


  // Paydunya API configuration
  $paydunya_keys = get_settings('paydunya');
  $values = json_decode($paydunya_keys);




  if ($values[0]->mode == 'sandbox') {
      $mode = "test";
      $MasterKey = $values[0]->MasterKey;
      $PublicKey= $values[0]->sandbox_PublicKey;
      $PrivateKey = $values[0]->sandbox_PrivateKey;
      $Token = $values[0]->sandbox_Token;
  } else {
      $mode = "live";
      $MasterKey = $values[0]->MasterKey;
      $PublicKey= $values[0]->production_PublicKey;
      $PrivateKey = $values[0]->production_PrivateKey;
      $Token = $values[0]->production_Token;
  }


  define('MASTERKEY', $MasterKey);
  define('PUBLICKEY', $PublicKey);
  define('PRIVATEKEY', $PrivateKey);
  define('TOKEN', $Token);
  define('MODE', $mode);



    //   var_dump(MASTERKEY,PUBLICKEY,PRIVATEKEY,TOKEN,MODE);
    //
    //  die();



  Paydunya_Setup::setMasterKey(MASTERKEY);

  Paydunya_Setup::setPublicKey(PUBLICKEY);
  Paydunya_Setup::setPrivateKey(PRIVATEKEY);
  Paydunya_Setup::setToken(TOKEN);
  Paydunya_Setup::setMode(MODE); // Optionnel. Utilisez cette option pour les paiements tests.

  //Configuration des informations de votre service/entreprise
  Paydunya_Checkout_Store::setName("Educ-pro Academy"); // Seul le nom est requis
  Paydunya_Checkout_Store::setTagline("La formation a tous ");
  Paydunya_Checkout_Store::setPhoneNumber("+229 99 54 38 41");
  Paydunya_Checkout_Store::setPostalAddress("Benin /Abomey-calavi - Etablissement TopTic-Solution");
  Paydunya_Checkout_Store::setWebsiteUrl("http://educproacademy.com/");
  Paydunya_Checkout_Store::setLogoUrl("http://educproacademy.com/uploads/system/14c4f36143b4b09cbc320d7c95a50ee7.png");


  Paydunya_Checkout_Store::setCallbackUrl(site_url('home/paydunya_paymentStatus'));



  $invoice = new Paydunya_Checkout_Invoice();


  $actual_price = 0;
  $total_price = 0;
  foreach ($this->session->userdata('cart_items') as $cart_item) {
      $course_details = $this->crud_model->get_course_by_id($cart_item)->row_array();

      $invoice->addItem($course_details['title'], 1, $course_details['price'], $course_details['price'], $course_details['short_description']);

//    echo $course_details['price'];
      $total_price  += $course_details['price'];


  }

//  var_dump($total_price);
//  die();

  $invoice->setDescription("Payement de cours sur Educ-pro Academy");
  $invoice->setTotalAmount($total_price);
  $invoice->setCallbackUrl(site_url('home/paydunya_paymentStatus'));

  $invoice->setCancelUrl(site_url('home/paydunya_paymentStatus'));

  if($invoice->create()) {
  //  header("Location: ".$invoice->getInvoiceUrl());
//      $this->session->set_userdata('cart_items', array());
//  var_dump( $invoice );
//  var_dump( $invoice->token);


      if(MODE =='test'){
          $data= array(
              "success"=>"true",
               "mode" => MODE,
              "token"=> $invoice->token
          );

      }else{

          $data= array(
              "success"=>"true",
              "token"=> $invoice->token
          );

      }

      $this->session->set_userdata('paydunya_data', json_encode($data));



  }else{
      echo $invoice->response_text;

      $this->session->set_flashdata('error_message', site_phrase('Mimimum_checkout_amount_is_200_FCFA'));
      $this->session->set_flashdata('error_message', $invoice->response_text );
      redirect('home/shopping_cart/' , 'refresh');

  }
  ?>

  <div class="package-details">

  
    <!--   <strong><?php echo site_phrase('student_name'); ?>
          | <?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?></strong> <br>
      <strong>
          <?php echo site_phrase('amount_to_pay'); ?> | <?php echo currency($amount_to_pay); ?>
      </strong> -->

      <br>
      <div id="paypal-button" style="margin-top: 20px;"></div>

      <button class="pay" hidden id="paydunyClick" onclick="payWithPaydunya(this)" data-ref="102" data-fullname="<?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?>" data-email="" data-phone=""> te</button>
      <br>


  </div>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>

  <!--<script src="--><?php //echo base_url('assets/payment/js/jquery.xdomainajax.js'); ?><!--" ></script>-->

  <script src="<?php echo base_url('assets/payment/js/mtn1.js'); ?>"></script>




  <script src="https://code.jquery.com/jquery.min.js"></script>

  <script src="https://paydunya.com/assets/psr/js/psr.paydunya.min.js"></script>

  <script>

      function payWithPaydunya(btn) {

          PayDunya.setup({
              selector: $(btn),
              url: "<?php echo site_url('home/paydunya_api'); ?>",
              method: "GET",
              displayMode: PayDunya.DISPLAY_IN_POPUP,
              beforeRequest: function() {
                  console.log("About to get a token and the url");
              },
              onSuccess: function(token) {
                  console.log("Token: " +  token);

                  // alert("Token: " +  token );
              },
              onTerminate: function(ref, token, status) {

                  console.log(ref);
                  console.log(token);
                  console.log(status);

                  //alert(token);

   
                   setTimeout(function (){

                      $.ajax({
                         "url": "<?php  echo site_url('home/paydunya_lastCallback'); ?>",
                         "method": "POST",
                          "dataType": 'json', 
                         
                      
                      }).done(function (data) {

                        //   alert(data.status);

                        if ( data.token == token && data.status=="completed"   ) {

                            var url = "<?php echo site_url('home/paydunya_payment')."/".$user_details['id']."/".$amount_to_pay; ?>/"+ref+ "/"+token+"/"+status;

                          window.location.replace(url);


                        }


                      
                      });

              
                     // console.log("une minute ");

                    }, 50000);




                    setTimeout(function (){

                      $.ajax({
                         "url": "<?php  echo site_url('home/paydunya_lastCallback'); ?>",
                         "method": "POST",
                          "dataType": 'json',

                         
                      
                      }).done(function (data) {

                        //   alert(data.status);

                        if ( data.token == token && data.status=="completed"   ) {

                            var url = "<?php echo site_url('home/paydunya_payment')."/".$user_details['id']."/".$amount_to_pay; ?>/"+ref+ "/"+token+"/"+status;

                          window.location.replace(url);


                        } 


                      
                      });

              
                     // console.log("une minute ");

                    }, 20000);



                       setTimeout(function (){

                      $.ajax({
                         "url": "<?php  echo site_url('home/paydunya_lastCallback'); ?>",
                         "method": "POST",
                          "dataType": 'json', 
                         
                      
                      }).done(function (data) {

                        //   alert(data.status);

                        if ( data.token == token && data.status=="completed"   ) {

                            var url = "<?php echo site_url('home/paydunya_payment')."/".$user_details['id']."/".$amount_to_pay; ?>/"+ref+ "/"+token+"/"+status;

                          window.location.replace(url);


                        }else{

                            var url = "<?php echo site_url('home/shopping_cart') ; ?>";

                               toastr.error("Délai d'attente est expiré veuillez reprendre l'operation SVP!");

                             window.location.replace(url);


                        }


                      
                      });

              
                     // console.log("une minute ");

                    }, 25000);

/*

                  if(status=="completed"){
                      // alert("COMPLETED")
                    // window.location.replace(url);


                  } if(status=="pending"){

                   console.log("pending");

                  

                     


                    // window.location.replace(url);


                  } if(status=="failed"){
                   //   alert("FAILED")
                   //  window.location.replace(url);


                  } if(status=="cancelled"){
                   //   alert("CANCELLED")
                   //  window.location.replace(url);


                  }

                  */




              },
              onError: function (error) {
                  alert("Unknown Error ==> ", error.toString());
              },
              onUnsuccessfulResponse: function (jsonResponse) {
                  console.log("Unsuccessful response ==> " + jsonResponse);
                    var url = "<?php echo site_url('home/paydunya_checkout') ; ?>";
                  window.location.replace(url);


                  // alert("Unsuccessful response ==>  " +  jsonResponse );
              },
              onClose: function(status) {
                  console.log("Close");

                   var url = "<?php echo site_url('home/paydunya_checkout') ; ?>";
                  window.location.replace(url);

                  // alert(status)
              }
          }).requestToken();
      }



      // funtion for delay in seconds
      function sleep(milliseconds) {
          var start = new Date().getTime();
          for (var i = 0; i < 1e7; i++) {
              if ((new Date().getTime() - start) > milliseconds){
                  break;
              }
          }
      }
  </script>

  <script>

      $(function () {

          $( "#paydunyClick" ).click();

      });

  </script>

  <div id="loader_modal"
       style="position: fixed; display: none; width: 100%; height: 100%; top: 0; left: 0; right: 0; bottom: 0; background-color: #42477077; z-index: 1000; color: #fff; text-align: center; padding-top: 100px;">
      Please wait....
  </div>
</body>
</html>
