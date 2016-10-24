<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>soundtrack non stop</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Bangers" rel="stylesheet">
        <link href="{{ url('css/styles.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css">

        <!-- Styles -->
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
            <div class="top-right links">
                <a href="{{ url('/login') }}">Login</a>
                <a href="{{ url('/register') }}">Register</a>
            </div>
            @endif
            <div class="content">
                <div class="title m-b-md">
                    <h1>Testsida</h1>
                </div>

                <div class="links">
                    <form method="post" action="{{ url('/search') }}">
                        {!! csrf_field() !!}
                        <label for="text"><h2>Mata in Filmtitel</h2></label>
                        <input type="text" class="form-control" name="titleinput" placeholder="Title">
                        <button type="submit" class="btn btn-danger" id="searchbtn"> Sök
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </body>
</html>
