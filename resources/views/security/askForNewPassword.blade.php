

<!DOCTYPE html>
<html lang="en">

<!--View of the page used to ask for a link to change a forgoten password-->

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retrieve Password</title>
</head>
<body>
    <form action="{{ url('/change-password') }}" method="post">
        {{  csrf_field() }}

        <input type="email" name="email" id="email">
        <span class="text-danger">@error('email'){{$message}}@enderror</span>

        

        
        <!--Success Message-->
        @if(session('status'))
            <div>{{ session('status') }}</div>
        @endif
        <br>
        <button type="submit"> Retrieve my password</button>

    </form>
</body>
</html>