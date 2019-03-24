@extends('layouts.app')

@section('head')
    <link type="text/css" rel="stylesheet" href="https://cdn.firebase.com/libs/firebaseui/3.5.2/firebaseui.css" />
    <script src="https://www.gstatic.com/firebasejs/5.0.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.0.0/firebase-auth.js"></script>
    <script src="https://cdn.firebase.com/libs/firebaseui/3.5.2/firebaseui.js"></script>

    <script type="text/javascript">


        var json = '{!! $data['source'] !!}';

        var jsonParse = JSON.parse(json);

        var signIn = [
        ];
        jsonParse.src.forEach(function (value) {

            switch (value)
            {
                case "Google":
                    signIn.push(firebase.auth.GoogleAuthProvider.PROVIDER_ID);
                    break;
                case "Facebook":
                    signIn.push(firebase.auth.FacebookAuthProvider.PROVIDER_ID);
                    break;
                case "Twitter":
                    signIn.push(firebase.auth.TwitterAuthProvider.PROVIDER_ID);
                    break;
                case "Github":
                    signIn.push(firebase.auth.GithubAuthProvider.PROVIDER_ID);
                    break;
                case "Email":
                    signIn.push(firebase.auth.EmailAuthProvider.PROVIDER_ID);
                    break;
                case "Phone":
                    signIn.push(firebase.auth.PhoneAuthProvider.PROVIDER_ID);
                    break;
                default:

                    break;

            }
        });

        // Initialize Firebase
        var config = {
            apiKey: "{{ $data["apiKey"] }}",
            authDomain: "{{ $data["authDomain"] }}",
            databaseURL: "{{ $data["db"] }}",
            projectId: "{{ $data["projectId"] }}",
            storageBucket: "{{ $data["bucket"] }}",
            messagingSenderId: "{{ $data["senderId"] }}"
        };

        firebase.initializeApp(config);
        // FirebaseUI config.
        var uiConfig = {
            signInSuccessUrl: '{{ url($data['redirectTo']) }}',
            callbacks: {
                signInSuccess: function (currentUser, credential, redirectUrl) {

                    console.log(credential);
                    console.log(currentUser);

                    $.ajax({
                        type: "POST",
                        async: false,
                        data: {
                            // "name": currentUser.displayName,
                            "email": currentUser.email,
                            "_token": "{{ csrf_token() }}",
                            "pic": currentUser.photoURL ,
                            "source": 6,
                            "sign_in_id": currentUser.uid
                        },
                        success: function(response)
                        {
                            if(response.status == 0)
                            {

                                alert('Sign Up Failed');

                                return false;

                            }

                        }
                    });
                    // Do something.
                    // Return type determines whether we continue the redirect automatically
                    // or whether we leave that to developer to handle.
                    return true;
                },
                signInFailure: function(error) {
                    // Some unrecoverable error occurred during sign-in.
                    // Return a promise when error handling is completed and FirebaseUI
                    // will reset, clearing any UI. This commonly occurs for error code
                    // 'firebaseui/anonymous-upgrade-merge-conflict' when merge conflict
                    // occurs. Check below for more details on this.
                    return handleUIError(error);
                }
            },
            signInOptions: signIn,
            // Terms of service url.
            tosUrl: '/tos', //cjapp-2073098949.eu-west-1.elb.amazonaws.com
            // Privacy Policy Url.
            privacyPolicyUrl: '/pp'
        };

        // Initialize the FirebaseUI Widget using Firebase.
        var ui = new firebaseui.auth.AuthUI(firebase.auth());
        // The start method will wait until the DOM is loaded.
        ui.start('#firebaseui-auth-container', uiConfig);

    </script>

@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="firebaseui-auth-container"></div>
            </div>
        </div>
    </div>
@endsection
