<?php
add_action('wp_ajax_personal-login' , 'personal_personal_login_plugin');

add_action('wp_ajax_nopriv_personal-login' , 'personal_personal_login_plugin');

function personal_personal_login_plugin(){
    $username = isset($_POST['username'])?sanitize_text_field($_POST['username']):0;
    $password = isset($_POST['password'])?sanitize_text_field($_POST['password']):0;


    $all_data = [];
    $all_data ['ErrorMessage'] = [];
    $all_data ['is_sent'] = true;
    $all_data ['result_list'] = [$username];

    if( !$password || !$username){
        if(!$username){
            array_push($all_data ['ErrorMessage'] , "نام یا شماره را وارد به درستی وارد کنید");
        }
        if(!$password){
            array_push($all_data ['ErrorMessage'] , "رمز عبور را وارد به درستی وارد کنید");
        }

        $all_data ['is_sent'] = false;
        echo json_encode($all_data);
        wp_die();
    };
        $creds = array();
        $creds['user_login'] = $username;
        $creds['user_password'] = $password;
        $creds['remember'] = true;
        $user = wp_signon( $creds, false );
        if ( is_wp_error($user) ) {
           $error = $user->get_error_message();
           $all_data['ErrorMessage'] = [$error];
           $all_data ['is_sent'] = false;
           echo json_encode($all_data);
           wp_die();
        } else {
            wp_redirect('/thank-you'); // redirect to some sort of thank you page perhaps.
            wp_set_auth_cookie( $user->ID,true, 0, 0);
         }


    echo json_encode($all_data);
    wp_die();

}

/////////////////////////////////////////////////////////////////////
add_action('wp_ajax_personal-reg' , 'personal_personal_reg_plugin');

add_action('wp_ajax_nopriv_personal-reg' , 'personal_personal_reg_plugin');

function personal_personal_reg_plugin(){
    $usernameReg = isset($_POST['usernameReg'])?sanitize_text_field($_POST['usernameReg']):0;
    $passwordReg = isset($_POST['passwordReg'])?sanitize_text_field($_POST['passwordReg']):0;
    $emailReg = isset($_POST['emailReg'])?sanitize_email($_POST['emailReg']):0;
    $nameReg = isset($_POST['nameReg'])?sanitize_text_field($_POST['nameReg']):0;



    $all_data = [];
    $all_data ['ErrorMessage'] = [];
    $all_data ['is_sent'] = true;
    $all_data ['verify_code'] = false;
    $all_data ['result_list'] = [sanitize_email($emailReg)];

    if(!$nameReg || !$emailReg || !$passwordReg || !$usernameReg){
        if(!$nameReg){
            array_push($all_data ['ErrorMessage'] , "نام را وارد به درستی وارد کنید");
        }
        if(!$emailReg){
            array_push($all_data ['ErrorMessage'] , "ایمیل را وارد به درستی وارد کنید");
        }
        if(!$passwordReg){
            array_push($all_data ['ErrorMessage'] , "رمز عبور را وارد به درستی وارد کنید");
        }
        if(!$usernameReg){
            array_push($all_data ['ErrorMessage'] , "شماره را وارد به درستی وارد کنید");
        }

        $all_data ['is_sent'] = false;
        echo json_encode($all_data);
        wp_die();
    };

    $user_id = wp_create_user( $usernameReg , $passwordReg, $emailReg ); // this creates the new user and returns the ID
 
    if(!is_wp_error($user_id)){ // if the user exists/if creating was successful.
      $user = new WP_User( $user_id ); // load the new user

              
      $user->set_role('subscriber'); // give the new user a role, in this case a subscriber
      // now add your custom user meta for each data point
      $userdata = array(
        'ID' => $user_id,
        'display_name' => $nameReg,
        );    

    //     wp_update_user( $userdata );
    //   wp_set_auth_cookie( $user_id,true, 0, 0);
    //   wp_authenticate_email_password($user_id, $emailReg, $passwordReg);
    //   wp_set_auth_cookie( $user->ID,true, 0, 0);

    // ایجاد کد تأیید
    $verification_code = rand(100000, 999999); // کد 6 رقمی
    // ارسال ایمیل
    $subject = 'Your Verification Code';
    $message = "Your verification code is: $verification_code";
    wp_mail($emailReg , $subject, $message);
    //change form
    echo json_encode($all_data);
    wp_die();


    }else{
        $all_data ['ErrorMessage'] = [$user_id->get_error_message()];
        $all_data ['is_sent'] = false;
    }
    echo json_encode($all_data);
    wp_die();

}


////////////////////////////////////////////////////
add_shortcode('login_reg_shortcode' , 'login_reg_shortcode_callback');

function login_reg_shortcode_callback(){
    ?>
    <style>
    div#login-main-content {
display: flex;
justify-content: space-around;
}

label {
    display: block;
    margin: 5px 0;
}

div#login-main-content div {
    width: 35%;
    background: white;
    padding: 10px;
    border-radius: 14px;
}

div#login-main-content-right-form {
    width: 100% !important;!i;!;
}

div#login-main-content-right-form input {
    width: 100%;
}
@media only screen and (max-width:800px) {
    div#login-main-content {
    display: block;
    }

    div#login-main-content div {
        width: 100%;
        margin-top: 35px;
    }
}
</style>
    <div id="login-main-content">
        <div id="login-main-content-right">
            <div id="form-loader">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" width="100" height="100" style="shape-rendering:auto;display:block;" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g transform="rotate(0 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.9166666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(30 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.8333333333333334s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(60 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.75s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(90 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.6666666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(120 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.5833333333333334s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(150 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.5s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(180 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.4166666666666667s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(210 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.3333333333333333s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(240 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.25s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(270 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.16666666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(300 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.08333333333333333s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(330 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="0s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g></g></g></svg>
            </div>
            <div id="login-main-content-right-title">
                <h3>ورود</h3>
            </div>
            <div id="login-main-content-right-form">
                <form action="" method="post" id="form-login">
                    <p>
                        <label for="username">ایمیل یا شماره</label>
                        <input type="text" class="" name="username" id="username" autocomplete="username" value="" required aria-required="true">
                    </p>
                    <p>
                        <label for="password"> رمز عبور </label>
                        <input type="password" class="" name="password" id="password" autocomplete="password" value="" required aria-required="true">
                    </p>
                    <p>
                        <input type="submit" class="" name="submit" for="form-login" id="submit" autocomplete="username" value="ورود" required aria-required="true">
                        
                    </p>
                </form>
                <!-- <script>
                            $(document).ready(function(){
                     
                            })
                        </script> -->
            </div>
        </div>
        <div id="login-main-content-left">
            <div id="form-loader">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" width="100" height="100" style="shape-rendering:auto;display:block;" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g transform="rotate(0 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.9166666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(30 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.8333333333333334s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(60 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.75s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(90 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.6666666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(120 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.5833333333333334s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(150 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.5s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(180 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.4166666666666667s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(210 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.3333333333333333s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(240 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.25s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(270 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.16666666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(300 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="-0.08333333333333333s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g transform="rotate(330 50 50)"><rect fill="#000000" height="12" width="6" ry="6" rx="3" y="24" x="47"><animate repeatCount="indefinite" begin="0s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate></rect></g><g></g></g></svg>
            </div>
            <div id="login-main-content-right-title">
                <h3>ثبت نام</h3>
            </div>
            <div id="login-main-content-right-form">
                <form action="" method="post" id="form-reg">
                    <p>
                        <label for="name-reg"> نام </label>
                        <input type="text" class="" name="name-reg" id="name-reg" autocomplete="name-reg" value="" required aria-required="true">
                    </p>
                    <p>
                        
                        <label for="username-reg">شماره موبایل</label>
                        <input type="number" class="" name="username-reg" id="username-reg" autocomplete="username-reg" value="" required aria-required="true">
                    </p>
                    <p>
                        <label for="email-reg"> ایمیل  </label>
                        <input type="text" class="" name="email-reg" id="email-reg" autocomplete="email-reg" value="" required aria-required="true">
                    </p>
                    <p>
                        <label for="password-reg"> رمز عبور </label>
                        <input type="password" class="" name="password-reg" id="password-reg" autocomplete="password-reg" value="" required aria-required="true">
                    </p>
                    <p>
                        <input type="submit" class="" name="submit-reg" for="form-reg" id="submit-reg" autocomplete="username" value="ثبت نام" required aria-required="true">                            
                    </p>
                    <p>
                        <label for="password-reg"> رمز عبور </label>
                        <input type="password" class="" name="password-reg" id="password-reg" autocomplete="password-reg" value="" required aria-required="true">
                    </p>

                </form>

                <script>
                    $(document).ready(function(){
                        $(' div#form-loader').hide()
                        $('#submit').click(function(e){
                                    let username = $("#username").val();
                                    let password = $("#password").val();
                                    e.preventDefault();
                                    let ajaxURL = '<?php echo admin_url('admin-ajax.php');?>'; 
                                                //here can make loader start
                                    $('div#login-main-content-right div#form-loader').show()
                                    $.ajax({
                                        
                                        type : 'POST',
                                        dataType : 'json',
                                        url : ajaxURL,
                                        data :{
                                            username : username,
                                            password : password,
                                            action : 'personal-login',

                                        },
                                        error : function(e){
                                            $(' div#form-loader').hide()

                                        },
                                        success : function(data){
                                            $(' div#form-loader').hide()

                                            if(data.is_sent){
                                                console.log("redirect");
                                                window.location.href = "<?php if ( get_option( 'woocommerce_myaccount_page_id' ) ) {echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );} ?>";
                                            }else{
                                                $("#errors").show()
                                                $("#errors").empty()

                                                data.ErrorMessage.forEach(error=>{
                                                    $("#errors").append(`
                                                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                                                    <div id="errors-content">
                                                                        ${error}
                                                                    </div>
                                                                </div>
                                                    `)
                                                    setTimeout(function() {
                                                        $("#errors").hide()
                                                    }, 3000);
                                                })
                                            }
                                            
                                        }
                                    })
                                }) ;    
                        $('#submit-reg').click(function(e){
                                    e.preventDefault();
                                    let usernameReg = $("#username-reg").val();
                                    let passwordReg = $("#password-reg").val();
                                    let emailReg = $("#email-reg").val();
                                    let nameReg = $("#name-reg").val();
                                    let ajaxURL = '<?php echo admin_url('admin-ajax.php');?>'; 

                                                //here can make loader start
                                    $('div#login-main-content-left div#form-loader').show()


                                    $.ajax({
                                        
                                        type : 'POST',
                                        dataType : 'json',
                                        url : ajaxURL,
                                        data :{
                                            usernameReg : usernameReg,
                                            passwordReg : passwordReg,
                                            emailReg : emailReg , 
                                            nameReg : nameReg,
                                            action : 'personal-reg',

                                        },
                                        error : function(e){
                                            $(' div#form-loader').hide()

                                            console.log(e)
                                        },
                                        success : function(data){
                                            $(' div#form-loader').hide()
                                            if(data.verify_code){
                                                $("#username-reg").parent().hide();
                                            }
                                            if(data.is_sent){
                                                window.location.href = "<?php if ( get_option( 'woocommerce_myaccount_page_id' ) ) {echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );} ?>";

                                            }else{
                                                $("#errors").show()
                                                $("#errors").empty()

                                                data.ErrorMessage.forEach(error=>{
                                                    $("#errors").append(`
                                                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                                                    <div id="errors-content">
                                                                        ${error}
                                                                    </div>
                                                                </div>
                                                    `)
                                                    setTimeout(function() {
                                                        $("#errors").hide()
                                                    }, 3000);
                                                })
                                    
                                            }
                                            
                                        }
                                    })
                                })        
                    })
                
                </script>
            </div>
            <div id="verification-form" style="display: none;">
                <form id="verify-code-form">
                    <label for="verification_code">Enter Verification Code:</label>
                    <input type="text" id="verification_code" name="verification_code" required>
                    <input type="hidden" id="user_id" name="user_id">
                    <button type="submit">Verify</button>
                </form>
            </div>

        </div>
    </div>
    <div id="errors" >
    </div>

<?php
}