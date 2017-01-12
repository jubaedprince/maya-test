@extends('layouts.app')

@section('additional_in_head')
    <script src="https://sdk.accountkit.com/en_US/sdk.js"></script>
    <script src="/js/login_with_fb.js"></script>
@endsection

@section('content')
    <div class="col-md-4 col-md-offset-4 text-center added-top-margin">
        <div class="panel panel-default">
            <div class="panel-body">
                <h1>Maya Login</h1>
                Enter country code (e.g. +1):
                <input class="form-control" type="text" id="country_code" value="+880"/>
                Enter phone number without spaces (e.g. 1674983245):
                <input class="form-control" type="text" id="phone_num"/><br>
                <button class="btn btn-default" onclick="phone_btn_onclick();">Login via SMS</button><br>
                <h2>OR</h2>
                Enter email address
                <input  class="form-control" type="text" id="email"/><br>
                <button class="btn btn-default" onclick="email_btn_onclick();">Login via Email</button>

                <h2>OR</h2>
                <div class="row added-top-margin">
                    <fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
                    </fb:login-button>
                </div>

            </div>
        </div>

    </div>
    <form id="my_form" action="/login" method="POST">
        {{csrf_field()}}
        <input id="code" name="code" type="hidden">
        <input id="csrf_nonce" name="csrf_nonce" type="hidden">
        <input id="fb_login" name="fb_login" type="hidden">
        <input id="fb_access_token" name="fb_access_token" type="hidden">
    </form>
@endsection

@section('scripts')
    {{--account kit script--}}
    <script>
        // initialize Account Kit with CSRF protection
        AccountKit_OnInteractive = function(){
            AccountKit.init(
                    {
                        appId:1208741232553333,
                        state:"{{csrf_token()}}",
                        version:"v1.0",
                        debug:true
                    }
            );
        };

        // login callback
        function loginCallback(response) {
            console.log(response);
            if (response.status === "PARTIALLY_AUTHENTICATED") {
                document.getElementById("code").value = response.code;
                document.getElementById("csrf_nonce").value = response.state;
                document.getElementById("my_form").submit();
            }
            else if (response.status === "NOT_AUTHENTICATED") {
                // handle authentication failure
            }
            else if (response.status === "BAD_PARAMS") {
                // handle bad parameters
            }
        }

        // phone form submission handler
        function phone_btn_onclick() {
            var country_code = document.getElementById("country_code").value;
            var ph_num = document.getElementById("phone_num").value;
            AccountKit.login('PHONE',
                    {countryCode: country_code, phoneNumber: ph_num}, // will use default values if this is not specified
                    loginCallback);
        }


        // email form submission handler
        function email_btn_onclick() {
            var email_address = document.getElementById("email").value;

            AccountKit.login('EMAIL', {emailAddress: email_address}, loginCallback);
        }

    </script>
@endsection