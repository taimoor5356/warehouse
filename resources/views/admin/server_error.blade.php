<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>500 Server Error</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Lato|Roboto+Slab');
        * {
            position: relative;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .centered {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        h1 {
            margin-bottom: 50px;
            font-family: 'Lato', sans-serif;
            color: #5e72e4;
        }

        .message {
            display: inline-block;
            line-height: 1.2;
            transition: line-height .2s, width .2s;
            overflow: hidden;
        }

        .message,
        .hidden {
            font-family: 'Roboto Slab', serif;
            font-size: 18px;
        }

        .hidden {
            color: #FFF;
        }
    </style>
</head>
<body>
    <section class="centered">
        <img src="{{ asset('images/titlelogo.jpg') }}" height="70" width="70" alt="">
        <h1><span class="border border-primary"><b style="font-size: 30px">500</b> Internal Server Error</span></h1>
        {{-- <div class="container">
            <span class="message" id="js-whoops"></span> <span class="message" id="js-appears"></span> <span class="message" id="js-error"></span> <span class="message" id="js-apology"></span>
            <div><span class="hidden" id="js-hidden">Message Here</span></div>
        </div> --}}
    </section>
    <hr>
</body>
</html>