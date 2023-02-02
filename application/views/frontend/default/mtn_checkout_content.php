
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
$paypal_keys = get_settings('paypal');
$paypal = json_decode($paypal_keys);
//[{"active":"1","mode":"sandbox","sandbox_mtn_user_id":"fcb1b26f-9165-4598-8ec1-2b0c6e8e8893","sandbox_mtn_api_key":"e8f7f086628445359b67b73bd5526aec","sandbox_mtn_subscription_key":"8e5c082bcf27403da7e0c6eadb4dccef","production_mtn_user_id":"#","production_mtn_api_key":"#","production_mtn_subscription_key":"#"}]

//MTN API configuration
$mtn_keys = get_settings('mtn');
$values = json_decode($mtn_keys);
if ($values[0]->mode == 'sandbox') {
    $subscription_Key = $values[0]->sandbox_mtn_subscription_key;
    $mtn_user_id = $values[0]->sandbox_mtn_user_id;
    $mtn_api_key = $values[0]->sandbox_mtn_api_key;
} else {
    $subscription_Key = $values[0]->sandbox_mtn_subscription_key;
    $mtn_user_id = $values[0]->sandbox_mtn_user_id;
    $mtn_api_key = $values[0]->sandbox_mtn_api_key;
}

define('MTN_API_KEY', $mtn_api_key);
define('MTN_USE_ID', $mtn_user_id);
define('MTN_SUBSCRIPTION_KEY', $subscription_Key);


?>

<div class="container">

    <div class="row">
        <div class="offset-md-2 col-lg-5 col-md-7 offset-lg-4 offset-md-3 login-form1 ">
            <div class="panel border bg-white">

                <div class="panel-heading">
                    <br>
                    <img   class="payment-gateway-icon" src="<?php echo base_url('assets/payment/mtn.png'); ?>">
                    <img alt="Progress" src="<?php echo base_url('assets/frontend/default/img/loader.gif'); ?>"
                         id="imgProg" visible="true" />
                    <h3 class="pt-3 font-weight-bold">

<!--                        <div class="title">--><?php //echo site_phrase('login'); ?><!--</div>-->
                    </h3>

                    <strong><?php echo site_phrase('student_name'); ?>
                        | <?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?></strong> <br>
                    <strong><?php echo site_phrase('amount_to_pay'); ?> | <?php echo currency($amount_to_pay); ?></strong> <br>




                </div>

                <div class="panel-body p-3">

                    <form method="post" action="<?php echo site_url('home/mtn_payment_traite'); ?>">
                        <div class="form-group py-2">
                            <!--         <label for="login-email"><span class="input-field-icon"><i class="fas fa-envelope"></i></span> --><?php //echo site_phrase('email'); ?><!--:</label>-->
                            <div class="select-field">
                                <span class="far fa-user p-2"></span>
                                <label  >Choisissez votre opérateur</label>
                                <select  id="operateur" name="operateur" class="custom-select">
                                    <option value="bj">Mtn Bénin</option>
                                </select>
<!--                                <input type="text" name = "email"  placeholder="--><?php //echo site_phrase('email'); ?><!--" required>-->
                            </div>

                            <div class="form-group">
                                <label for="phone_number">Numéro de téléphone</label>
                                <div class="input-group" >
                                    <input type="text" hidden id="amount" name="amount" value="<?php echo $amount_to_pay; ?>">
                                    <input type="text" hidden   name="user_id" value="<?php echo $user_details['id'] ; ?>">
                                    <input id="phone" name="phone" required="" value="" type="number"
                                           autocomplete="none"   placeholder="Votre numéro de téléphone" style="width: 180%" class="form-control ">
                                </div>
                            </div>


                            <div class="modal-footer">
                                <div  class="pay-btn-container">


                                    <button    id="btn_payer"  type="submit" class="btn btn-success d-flex">
                                        <?php echo site_phrase('btn_pay'); ?> | <?php echo currency($amount_to_pay); ?>
                                    </button>

                                </div>
                            </div>

                        </div>

                    </form>
                </div>


            </div>


        </div>

    </div>


</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!--<script src="https://www.paypalobjects.com/api/checkout.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>

<!--<script src="--><?php //echo base_url('assets/payment/js/jquery.xdomainajax.js'); ?><!--" ></script>-->

<script src="<?php echo base_url('assets/payment/js/mtn1.js'); ?>"></script>


<script type="text/javascript">
    $(function () {
        var pcode = "bj";


        $("#imgProg").hide();

        // $("#btn_payer").prop( "disabled", true );

        $('#operateur').change(function () {
            var input = document.querySelector("#phone");
            window.intlTelInput(input, {
                initialCountry: $('#operateur').val(),

            });

        });


        $('#btn_payer').click(function () {
            $("#imgProg").show();
        });

        /*
        $("#phone").change(function () {

            var phoneNumber = $('#phone').val();
            switch ($('#operateur').val()) {
                case "bj":

                    if (!( phoneNumber.substring(0,2) == "97" ||
                        phoneNumber.substring(0,2) == "96" ||
                        phoneNumber.substring(0,2) == "91" ||
                        phoneNumber.substring(0,2) == "90" ||
                        phoneNumber.substring(0,2) == "69" ||
                        phoneNumber.substring(0,2) == "67" ||
                        phoneNumber.substring(0,2) == "66" ||
                        phoneNumber.substring(0,2) == "62" ||
                        phoneNumber.substring(0,2) == "61" ||
                        phoneNumber.substring(0,2) == "54" ||
                        phoneNumber.substring(0,2) == "53" ||
                        phoneNumber.substring(0,2) == "52" ||
                        phoneNumber.substring(0,2) == "51" )
                    ){


                        toastr.error(" votre numero MTN  n'exite au benin ");
                        $("#btn_payer").prop( "disabled", true );

                    }
                    if (phoneNumber.length > 8) {
                        toastr.error(' votre numero n\'est valide  ');

                        $("#btn_payer").prop( "disabled", true );
                    }
                    else {
                        $("#btn_payer").prop( "disabled", false );

                    }



                    break;

                case "ci":

                    toastr.error(" service non disponible pour le monment");
                    $("#btn_payer").prop( "disabled", true )
                    break;

            }



        });

*/
        $('#payer').click(function () {

            var isControlle = true;

            var phoneNumber = $('#phone').val();
            var amount = $('#amount').val();


            // switch ($('#operateur').val()) {
            //     case "bj":
            //         if (phoneNumber.length < 8) {
            //             isControlle = false;
            //             toastr.error(' votre numero n\'est valide ');
            //             // alert("votre numero n'est valide \n  ")
            //         } else {
            //             isControlle = true;
            //         }
            //
            //         phoneNumber = "" + phoneNumber;
            //
            //         break;
            //
            //     case "ci":
            //         break;
            //
            // }

            if (isControlle) {

                $("#imgProg").show();
                var Ocp_Apim_Subscription_Key = "<?php echo MTN_SUBSCRIPTION_KEY; ?>";
                var X_Reference_Id = "<?php echo MTN_USE_ID; ?>";
                var ApiKey = "<?php echo MTN_API_KEY; ?>";

                createToken(Ocp_Apim_Subscription_Key, X_Reference_Id, ApiKey)
                    .done(function (response) {
                        var newToken = response.access_token;
                        var newIdRef = getUUcode();
                        console.log(newToken);

                        requestTopay(Ocp_Apim_Subscription_Key, newToken, newIdRef, amount, "EUR", phoneNumber)
                            .done(function (data) {
                                console.log(data);
                                // alert(response2);

                                requestTopayStatus(Ocp_Apim_Subscription_Key, newToken, newIdRef)
                                    .done(function (resutat) {
                                        console.log(resutat);

                                        $.ajax({
                                            "url": "<?php echo site_url('home/save_mtm_history'); ?>",
                                            "method": "POST",
                                            "data": {
                                                amount: resutat.amount,
                                                currency: resutat.currency,
                                                externalId: resutat.externalId,
                                                financialTransactionId: resutat.financialTransactionId,
                                                payeeNote: resutat.payeeNote,
                                                payer: resutat.payer.partyId,
                                                payerMessage: resutat.payerMessage,
                                                status: resutat.status,
                                                reason: resutat.reason,
                                                user_id: "<?php echo $user_details['id']; ?>"
                                            },

                                        }).done(function (d) {


                                            if (resutat.status != "FAILED") {

                                                $("#imgProg").hide();

                                                var url = "<?php echo site_url('home/mtn_payment') . "/" . $user_details['id'] . "/" . $amount_to_pay; ?>";


                                                window.location.replace(url);


                                            } else {
                                                switch (resutat.reason) {
                                                    case "INTERNAL_PROCESSING_ERROR":
                                                        $("#imgProg").hide();
                                                        alert("Probleme inattendu veuillez reprendre ");
                                                        break;
                                                    case "APPROVAL_REJECTED":
                                                        $("#imgProg").hide();
                                                        alert("APPROBATION REFUSÉE");
                                                        break;
                                                    case "EXPIRED":
                                                        $("#imgProg").hide();
                                                        alert("TEMPS EXPIRÉ");
                                                        break;

                                                    default:
                                                        $("#imgProg").hide();
                                                        alert("Probleme inattendu veuillez reprendre ")

                                                }

                                            }


                                        });


                                        //


                                    });

                            }).fail(function (data, textStatus, xhr) {
                            //This shows status code eg. 403
                            console.log("error", data.status);
                            //This shows status message eg. Forbidden
                            console.log("STATUS: " + xhr);

                        });

                    });


            }


        });


    });
</script>


<script>


    var input = document.querySelector("#phone");
    window.intlTelInput(input, {
        initialCountry: "bj",

        // any initialisation options go here
    });


</script>

