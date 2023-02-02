
<link href="<?php echo base_url('assets/frontend/default/css/bootstrap.min.css'); ?>" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">

<link rel="stylesheet" href="<?php echo base_url() . 'assets/global/toastr/toastr.css' ?>">
<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js'); ?>"></script>

<script src="<?php echo base_url() . 'assets/frontend/default/js/main.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/global/toastr/toastr.min.js'; ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

    * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif
    }

    body {
        height: 100vh;
        background: linear-gradient(to top, #c9c9ff 50%, #9090fa 90%) no-repeat
    }

    .container {
        margin: 50px auto
    }

    .panel-heading {
        text-align: center;
        margin-bottom: 10px
    }

    #forgot {
        min-width: 100px;
        margin-left: auto;
        text-decoration: none
    }

    a:hover {
        text-decoration: none
    }

    .form-inline label {
        padding-left: 10px;
        margin: 0;
        cursor: pointer
    }

    .btn.btn-primary {
        margin-top: 20px;
        border-radius: 15px
    }

    .panel {
        min-height: 380px;
        box-shadow: 20px 20px 80px rgb(218, 218, 218);
        border-radius: 12px
    }

    .input-field {
        border-radius: 5px;
        padding: 5px;
        display: flex;
        align-items: center;
        cursor: pointer;
        border: 1px solid #ddd;
        color: #4343ff
    }

    input[type='text'],
    input[type='password'] {
        border: none;
        outline: none;
        box-shadow: none;
        width: 100%
    }

    .fa-eye-slash.btn {
        border: none;
        outline: none;
        box-shadow: none
    }

    img {
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 10%;
        position: relative
    }

    a[target='_blank'] {
        position: relative;
        transition: all 0.1s ease-in-out
    }

    .bordert {
        border-top: 1px solid #aaa;
        position: relative
    }

    .bordert:after {
        content: "or connect with";
        position: absolute;
        top: -13px;
        left: 33%;
        background-color: #fff;
        padding: 0px 8px
    }

    @media(max-width: 360px) {
        #forgot {
            margin-left: 0;
            padding-top: 10px
        }

        body {
            height: 100%
        }

        .container {
            margin: 30px 0
        }

        .bordert:after {
            left: 25%
        }
    }
</style>


<!-- SHOW TOASTR NOTIFIVATION -->
<?php if ($this->session->flashdata('flash_message') != "") : ?>

    <script type="text/javascript">
        toastr.success('<?php echo $this->session->flashdata("flash_message"); ?>');
    </script>

<?php endif; ?>

<?php if ($this->session->flashdata('error_message') != "") : ?>

    <script type="text/javascript">
        toastr.error('<?php echo $this->session->flashdata("error_message"); ?>');
    </script>

<?php endif; ?>

<?php if ($this->session->flashdata('info_message') != "") : ?>

    <script type="text/javascript">
        toastr.info('<?php echo $this->session->flashdata("info_message"); ?>');
    </script>

<?php endif; ?>

<?php

//
// var_dump($user_details["last_name"],$user_details["first_name"],$amount_to_pay);

//   var_dump($user_details );
//   var_dump($user_details );
//   var_dump($user_details );
//   var_dump($user_details );
//  var_dump('------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------');
//  var_dump($user_details );
//  die();

/*

$paypal_keys = get_settings('paypal');
$paypal = json_decode($paypal_keys);
//[{"active":"1","mode":"sandbox","sandbox_mtn_user_id":"fcb1b26f-9165-4598-8ec1-2b0c6e8e8893","sandbox_mtn_api_key":"e8f7f086628445359b67b73bd5526aec","sandbox_mtn_subscription_key":"8e5c082bcf27403da7e0c6eadb4dccef","production_mtn_user_id":"#","production_mtn_api_key":"#","production_mtn_subscription_key":"#"}]



*/

//fedapay API configuration
$fedapay_keys = get_settings('fedapay');
$values = json_decode($fedapay_keys);
if ($values[0]->mode == 'sandbox') {

    $fedapay_pubilc_key = $values[0]->sandbox_fedapay_public_key;

} else {
    $fedapay_pubilc_key = $values[0]->prod_fedapay_public_key;
}

define('FEDAPAY_PUBLIC_KEY', $fedapay_pubilc_key);




?>

<div class="container">

    <div style="  position: fixed; top: 50%;bottom: 50%;  left: 50%; right: 50%; text-align: center; z-index: -4">

        <img width="30px" src="<?php echo base_url() . 'assets/payment/loader.gif' ?>">

    </div>

    <center>

        <div id="embed" style="width: 500px; height: 430px">

        </div>

    </center>





    <script type="text/javascript">



        FedaPay.init({
            public_key: '<?php echo FEDAPAY_PUBLIC_KEY; ?>',
            transaction: {
                amount:'<?php echo $amount_to_pay; ?>',

                description: 'Acheter de cours en ligne sur Educ-Pro academy'
            },
            customer: {

                email:  '<?php echo $user_details["email"]; ?>',
                firstname:  '<?php echo $user_details["first_name"]; ?>',

                lastname:  '<?php echo $user_details["last_name"]; ?>',
            },
            container: '#embed',
            onComplete:function({ reason: number, transaction: object })
            {

                //   alert(number);

                if (number=="DIALOG DISMISSED"){

                    var url = "<?php echo site_url('home/fedapay_payment');?>";
                    toastr.error("vous avez annulé le payement");
                    window.location.replace(url);


                }

                if (number=="CHECKOUT COMPLETE"){

                    // alert(number);
                    console.log( object );

                    var  transaction_id=object.id;
                    var  reference=object.reference;
                    var  customer_id=object.customer_id;
                    var  commission=object.commission;
                    var  mode=object.mode;
                    var  fees=object.fees;
                    var  full_name=object.customer.full_name;
                    var  fixed_commission=object.fixed_commission;
                    var  created_at=object.created_at;

                    var url = "<?php echo site_url('home/fedapay_payment')."/".$user_details["id"]."/".$amount_to_pay."/false/true/";?>"+transaction_id+"/"+reference+"/"+customer_id+"/"+commission+"/"+mode+"/"+fees+"/"+full_name+"/"+fixed_commission+"/"+created_at;
                    window.location.replace(url);


                    //
                    // alert( object.status);

                }





            }
        });



    </script>

</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!--<script src="https://www.paypalobjects.com/api/checkout.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>

<script src="<?php echo base_url('assets/payment/js/jquery.xdomainajax.js'); ?>" ></script>

<script src="<?php echo base_url('assets/payment/js/mtn1.js'); ?>"></script>
