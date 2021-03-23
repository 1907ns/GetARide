<!DOCTYPE html>
<html lang="en">

<!--View of the page used to change his forgotten password-->

<!--TODO 22/03/2021 ADD VALIDATION ERROR SIGNALISATION-->


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retrieve Password</title>
</head>
<body>
    <form action="{{ url('/reset-password') }}" method="post">
        {{  csrf_field() }}



        <label>Email</label>
        <input type="email" name="email" id="email">
        @if($errors->get('token'))
            <div>TOKEN</div>
        @endif
        @if($errors->get('password_confirmation'))
            <div>TOKEN</div>
        @endif
        <br>
        <label>New Password</label>
        <input type="password" name="password" id="email">
        <br>
        <label>Retype your new Password</label>
        <input type="password" name="password_confirmation" id="email">
        <br>
        <input name="token" type="hidden" value="{{ $token }}">
        @if($errors->get('token'))
            <div>{{ __('passwords.token') }}</div>
        @endif
        <button type="submit"> Reset</button>

    </form>
</body>
</html>