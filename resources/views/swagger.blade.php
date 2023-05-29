<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="{{ $title }}">
        <title>{{ $title }}</title>
        <link rel="stylesheet" href=" {{ $styleHref }}" @if($styleSri) integrity="{{ $styleSri }}" @endif crossorigin="anonymous">
    </head>
    <body>
        <div id="swagger-ui"></div>
        <script src="{{ $scriptSrc }}" @if($scriptSri) integrity="{{ $scriptSri }}" @endif crossorigin="anonymous"></script>
        <script>
          window.onload = () => {
            window.ui = SwaggerUIBundle({
              url: "{{ $route }}",
              dom_id: '#swagger-ui',
            })
          }
        </script>
    </body>
</html>
