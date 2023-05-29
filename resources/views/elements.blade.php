<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="{{ $title }}">
        <title>{{ $title }}</title>
        <link rel="stylesheet" href=" {{ $styleHref }}" @if($styleSri) integrity="{{ $styleSri }}" @endif crossorigin="anonymous">
        <style>
            elements-api {
                display: block;
                min-height: 100vh;
            }

            elements-api > div {
                display: block;
                height: 100vh !important;
            }
        </style>
    </head>
    <body>
        <elements-api apiDescriptionUrl="{{ $route }}" router="hash" layout="sidebar"></elements-api>
    </body>
    <script src="{{ $scriptSrc }}" @if($scriptSri) integrity="{{ $scriptSri }}" @endif crossorigin="anonymous"></script>
</html>
