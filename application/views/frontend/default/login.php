<?php if(get_frontend_settings('recaptcha_status')): ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

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
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
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
        content: "<?php echo site_phrase('or_connect_with'); ?>";
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

<section class="category-header-area" style="display: none">
    <div class="container-lg">
        <div class="row">
            <div class="col">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('home'); ?>"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item">
                            <a href="#">
                                <?php echo $page_title; ?>
                            </a>
                        </li>
                    </ol>
                </nav>
                <h1 class="category-name">
                    <?php echo site_phrase('registered_user'); ?>
                </h1>
            </div>
        </div>
    </div>
</section>


<?php echo $this->session->userdata('is_instructor'); ?>

<div class="container">

    <div class="row">


        <div class="offset-md-2 col-lg-5 col-md-7 offset-lg-4 offset-md-3 login-form1 ">

            <div class="panel border bg-white">
                <div class="panel-heading">
                    <h3 class="pt-3 font-weight-bold">
                        <div class="title"><?php echo site_phrase('login'); ?></div>
                          </h3>
                    <div class="subtitle"><?php echo site_phrase('provide_your_valid_login_credentials'); ?>.</div>

                </div>
                <div class="panel-body p-3">

                    <form action="<?php echo site_url('login/validate_login/user'); ?>" method="POST">
                        <div class="form-group py-2">
<!--         <label for="login-email"><span class="input-field-icon"><i class="fas fa-envelope"></i></span> --><?php //echo site_phrase('email'); ?><!--:</label>-->
                            <div class="input-field"> <span class="far fa-user p-2"></span>
                                <input type="text" name = "email"  placeholder="<?php echo site_phrase('email'); ?>" required>
                            </div>
                        </div>
                        <div class="form-group py-1 pb-2">
                            <div class="input-field"> <span class="fas fa-lock px-2"></span>
                                <input type="password" name="password" placeholder="<?php echo site_phrase('password'); ?>" required>
<!--                                <button class="btn bg-white text-muted"> <span class="far fa-eye-slash"></span> </button>-->
                            </div>
                        </div>

                        <?php if(get_frontend_settings('recaptcha_status')): ?>
                            <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="<?php echo get_frontend_settings('recaptcha_sitekey'); ?>"></div>
                            </div>
                        <?php endif; ?>

                        <div class="form-inline">
<!--                            <input type="checkbox" name="remember" id="remember">-->
<!--                            <label for="remember" class="text-muted">Remember me</label>-->
                            <a href="javascript::" id="forgot" class="font-weight-bold" onclick="toggoleForm('forgot_password')"><?php echo site_phrase('forgot_password'); ?></a>

<!--                            <a href="#" id="forgot" class="font-weight-bold">Forgot password?</a>-->

                        </div>
<!--                        <div class="btn btn-primary btn-block mt-3">Login</div>-->
                        <input type="submit" value="<?php echo site_phrase('sign_in'); ?>" class="btn btn-primary btn-block mt-3">
                        <div class="text-center pt-4 text-muted">
<!--                            Don't have an account? <a href="#">Sign up</a>-->
                            <?php echo site_phrase('do_not_have_an_account'); ?>? <a href="javascript::" onclick="toggoleForm('registration')"><?php echo site_phrase('sign_up'); ?></a>
                        </div>

                    </form>
                </div>
                <div class="mx-3 my-2 py-2 bordert">
                    <div class="text-center py-3">
<!--                        <a href="https://wwww.facebook.com" target="_blank" class="px-2">-->
<!--                            <img src="https://www.dpreview.com/files/p/articles/4698742202/facebook.jpeg" alt="">-->
<!--                        </a>-->
<!--                        <a href="--><?php //echo $google_auth_url;  ?><!--" target="_blank" class="px-2">-->
<!--                            <img src="https://www.freepnglogos.com/uploads/google-logo-png/google-logo-png-suite-everything-you-need-know-about-google-newest-0.png" alt="">-->
<!--                        </a>-->
<!--                        <a href="https://www.github.com" target="_blank" class="px-2">-->
<!--                            <img src="https://www.freepnglogos.com/uploads/512x512-logo-png/512x512-logo-github-icon-35.png" alt="">-->
<!--                        </a>-->
                    </div>
                </div>
            </div>
        </div>


        <div class="offset-md-2 col-lg-5 col-md-7 offset-lg-4 offset-md-3 register-form1 hidden ">

            <div class="panel border bg-white">

                <div class="panel-heading">
                    <h3 class="pt-3 font-weight-bold">
                        <div class="title"><?php echo site_phrase('registration_form'); ?></div>
                    </h3>
                    <div class="subtitle"><?php echo site_phrase('sign_up_and_start_learning'); ?></div>

                </div>
                <div class="panel-body p-3">

                    <form action="<?php echo site_url('login/register'); ?>"  method="POST"  id="sign_up">

                        <div class="form-group py-2">

                            <div class="input-field"> <span class="far fa-user p-2"></span>
                                <input type="text" name = "first_name" id="first_name" placeholder="<?php echo site_phrase('first_name'); ?>" required>
                            </div>
                        </div>

                        <div class="form-group py-2">

                            <div class="input-field"> <span class="far fa-user p-2"></span>
                                <input type="text"  class="form-control" name = "last_name" id="last_name" placeholder="<?php echo site_phrase('last_name'); ?>" value=""  required>
                            </div>
                        </div>
                        <div class="form-group py-2">
                            <!--         <label for="login-email"><span class="input-field-icon"><i class="fas fa-envelope"></i></span> --><?php //echo site_phrase('email'); ?><!--:</label>-->
                            <div class="input-field"> <span class="far fa-envelope p-2"></span>
                                <input type="text" name = "email"  placeholder="<?php echo site_phrase('email'); ?>" required>
                            </div>
                        </div>
                        <div class="form-group py-1 pb-2">
                            <div class="input-field"> <span class="fas fa-lock px-2"></span>
                                <input type="password" name="password" placeholder="<?php echo site_phrase('password'); ?>" required>
                                <!--                                <button class="btn bg-white text-muted"> <span class="far fa-eye-slash"></span> </button>-->
                            </div>
                        </div>

                        <?php echo site_phrase('sign_up_accept_condition'); ?>

                        <input type="submit" value="<?php echo site_phrase('sign_up'); ?>" class="btn btn-primary btn-block mt-3">
                        <div class="text-center pt-4 text-muted">
                            <?php echo site_phrase('already_have_an_account'); ?>? <a href="javascript::" onclick="toggoleForm('login')"><?php echo site_phrase('login'); ?></a>
                        </div>

                    </form>
                </div>
                <div class="mx-3 my-2 py-2 bordert">
<!--                    <div class="text-center py-3"> <a href="https://wwww.facebook.com" target="_blank" class="px-2"> <img src="https://www.dpreview.com/files/p/articles/4698742202/facebook.jpeg" alt=""> </a> -->
<!--                        <a href="https://www.google.com" target="_blank" class="px-2">-->
<!--                            <img src="https://www.freepnglogos.com/uploads/google-logo-png/google-logo-png-suite-everything-you-need-know-about-google-newest-0.png" alt=""> </a>-->
<!--                        <a href="https://www.github.com" target="_blank" class="px-2"> <img src="https://www.freepnglogos.com/uploads/512x512-logo-png/512x512-logo-github-icon-35.png" alt=""> </a> -->
<!--                    </div>-->
                </div>
            </div>
        </div>


        <div class="user-dashboard-content w-100 forgot-password-form1 hidden">
            <div class="content-title-box">
                <div class="title"><?php echo site_phrase('forgot_password'); ?></div>
                <div class="subtitle"><?php echo site_phrase('provide_your_email_address_to_get_password'); ?>.</div>
            </div>
            <form action="<?php echo site_url('login/forgot_password/frontend'); ?>" method="post" id="forgot_password">
                <div class="content-box">
                    <div class="basic-group">
                        <div class="form-group">
                            <label for="forgot-email"><span class="input-field-icon"><i class="fas fa-envelope"></i></span> <?php echo site_phrase('email'); ?>:</label>

                                 <input type="email" class="form-control" name = "email" id="forgot-email" placeholder="<?php echo site_phrase('email'); ?>" value="" required>

                            <small class="form-text text-muted"><?php echo site_phrase('provide_your_email_address_to_get_password'); ?>.</small>
                        </div>
                        <?php if(get_frontend_settings('recaptcha_status')): ?>
                            <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="<?php echo get_frontend_settings('recaptcha_sitekey'); ?>"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="content-update-box">
                    <button class="btn" type="submit"><?php echo site_phrase('reset_password'); ?></button>
                </div>
                <div class="forgot-pass text-center">
                    <?php echo site_phrase('want_to_go_back'); ?>? <a href="javascript::" onclick="toggoleForm('login')"><?php echo site_phrase('login'); ?></a>
                </div>
            </form>
        </div>


    </div>
</div>


<section class="category-course-list-area hidden">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-lg-9">
                <div class="user-dashboard-box mt-3">
                    <div class="user-dashboard-content w-100 login-form">
                        <div class="content-title-box">
                            <div class="title"><?php echo site_phrase('login'); ?></div>
                            <div class="subtitle"><?php echo site_phrase('provide_your_valid_login_credentials'); ?>.</div>
                        </div>


                        <form action="<?php echo site_url('login/validate_login/user'); ?>" method="post" id="login">
                            <div class="content-box">
                                <div class="basic-group">
                                    <div class="form-group">


                                        <label for="login-email"><span class="input-field-icon"><i class="fas fa-envelope"></i></span> <?php echo site_phrase('email'); ?>:</label>
                                        <input type="email" class="form-control" name = "email" id="login-email" placeholder="<?php echo site_phrase('email'); ?>" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="login-password"><span class="input-field-icon"><i class="fas fa-lock"></i></span> <?php echo site_phrase('password'); ?>:</label>
                                        <input type="password" class="form-control" name = "password" placeholder="<?php echo site_phrase('password'); ?>" value="" required>
                                    </div>
                                    <?php if(get_frontend_settings('recaptcha_status')): ?>
                                        <div class="form-group">
                                            <div class="g-recaptcha" data-sitekey="<?php echo get_frontend_settings('recaptcha_sitekey'); ?>"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="content-update-box">
                                <button class="btn" type="submit"><?php echo site_phrase('login'); ?></button>
                            </div>
                            <div class="forgot-pass text-center">
                                <span><?php echo site_phrase('or'); ?></span>
                                <a href="javascript::" onclick="toggoleForm('forgot_password')"><?php echo site_phrase('forgot_password'); ?></a>
                            </div>
                            <div class="account-have text-center">
                                <?php echo site_phrase('do_not_have_an_account'); ?>? <a href="javascript::" onclick="toggoleForm('registration')"><?php echo site_phrase('sign_up'); ?></a>
                            </div>
                        </form>
                    </div>


                    <div class="user-dashboard-content w-100 register-form hidden">
                        <div class="content-title-box">
                            <div class="title"><?php echo site_phrase('registration_form'); ?></div>
                            <div class="subtitle"><?php echo site_phrase('sign_up_and_start_learning'); ?>.</div>
                        </div>
                        <form action="<?php echo site_url('login/register'); ?>" method="post" id="sign_up">
                            <div class="content-box">
                                <div class="basic-group">
                                    <div class="form-group">
                                        <label for="first_name"><span class="input-field-icon"><i class="fas fa-user"></i></span> <?php echo site_phrase('first_name'); ?>:</label>
                                        <input type="text" class="form-control" name = "first_name" id="first_name" placeholder="<?php echo site_phrase('first_name'); ?>" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name"><span class="input-field-icon"><i class="fas fa-user"></i></span> <?php echo site_phrase('last_name'); ?>:</label>
                                        <input type="text" class="form-control" name = "last_name" id="last_name" placeholder="<?php echo site_phrase('last_name'); ?>" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="registration-email"><span class="input-field-icon"><i class="fas fa-envelope"></i></span> <?php echo site_phrase('email'); ?>:</label>
                                        <input type="email" class="form-control" name = "email" id="registration-email" placeholder="<?php echo site_phrase('email'); ?>" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="registration-password"><span class="input-field-icon"><i class="fas fa-lock"></i></span> <?php echo site_phrase('password'); ?>:</label>
                                        <input type="password" class="form-control" name = "password" id="registration-password" placeholder="<?php echo site_phrase('password'); ?>" value="" required>
                                    </div>
                                    <?php if(get_frontend_settings('recaptcha_status')): ?>
                                        <div class="form-group">
                                            <div class="g-recaptcha" data-sitekey="<?php echo get_frontend_settings('recaptcha_sitekey'); ?>"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="content-update-box">
                                <button class="btn" type="submit"><?php echo site_phrase('sign_up'); ?></button>
                            </div>
                            <div class="account-have text-center">
                                <?php echo site_phrase('already_have_an_account'); ?>? <a href="javascript::" onclick="toggoleForm('login')"><?php echo site_phrase('login'); ?></a>
                            </div>
                        </form>
                    </div>

                    <div class="user-dashboard-content w-100 forgot-password-form hidden">
                        <div class="content-title-box">
                            <div class="title"><?php echo site_phrase('forgot_password'); ?></div>
                            <div class="subtitle"><?php echo site_phrase('provide_your_email_address_to_get_password'); ?>.</div>
                        </div>
                        <form action="<?php echo site_url('login/forgot_password/frontend'); ?>" method="post" id="forgot_password">
                            <div class="content-box">
                                <div class="basic-group">
                                    <div class="form-group">
                                        <label for="forgot-email"><span class="input-field-icon"><i class="fas fa-envelope"></i></span> <?php echo site_phrase('email'); ?>:</label>
                                        <input type="email" class="form-control" name = "email" id="forgot-email" placeholder="<?php echo site_phrase('email'); ?>" value="" required>
                                        <small class="form-text text-muted"><?php echo site_phrase('provide_your_email_address_to_get_password'); ?>.</small>
                                    </div>
                                    <?php if(get_frontend_settings('recaptcha_status')): ?>
                                        <div class="form-group">
                                            <div class="g-recaptcha" data-sitekey="<?php echo get_frontend_settings('recaptcha_sitekey'); ?>"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="content-update-box">
                                <button class="btn" type="submit"><?php echo site_phrase('reset_password'); ?></button>
                            </div>
                            <div class="forgot-pass text-center">
                                <?php echo site_phrase('want_to_go_back'); ?>? <a href="javascript::" onclick="toggoleForm('login')"><?php echo site_phrase('login'); ?></a>
                            </div>
                        </form>
                    </div>


                </div>
            </div>


        </div>
    </div>
</section>

<script type="text/javascript">
  function toggoleForm(form_type) {
    if (form_type === 'login') {
      $('.login-form1').show();
      $('.forgot-password-form1').hide();
      $('.register-form1').hide();
    }else if (form_type === 'registration') {
      $('.login-form1').hide();
      $('.forgot-password-form1').hide();
      $('.register-form1').show();
    }else if (form_type === 'forgot_password') {
      $('.login-form1').hide();
      $('.forgot-password-form1').show();
      $('.register-form1').hide();
    }
  }
</script>
