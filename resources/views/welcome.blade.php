<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- <script src="https://unpkg.com/@elevenlabs/convai-widget-embed" async type="text/javascript"></script> -->
         <!-- import app.js -->
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @include('partials.head')

    </head>
    <body class="bg-[#141414] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">

        <!-- welcome to boss nova health -->
        <div>
            <h1 class="text-4xl font-bold text-center mb-4 text-gray-100">This will the boss nova's website</h1>
            <flux:button
    href="{{ route('dashboard') }}"
    icon:trailing="arrow-up-right"
>
Go to Dashboard
</flux:button>
        </div>



        <elevenlabs-convai agent-id="agent_5801kew9b5jfft8v3yerqzy6w4c3">

    </body>
</html>
