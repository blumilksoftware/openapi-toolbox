<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ $title }}">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements@7.7.16/styles.min.css" integrity="sha384-1lLf7J28IOR7k5RlItk6Y+G3hDgVB3y4RCgWNq6ZSwjYfvJXPtZAdW0uklsAZbGW" crossorigin="anonymous">
  </head>
  <body>
    <elements-api apiDescriptionUrl="{{ $route }}" router="hash" layout="sidebar"></elements-api>
  </body>
  <script src="https://unpkg.com/@stoplight/elements@7.7.16/web-components.min.js" integrity="sha384-bwBnouovwwSJc5fWe7VFNxRg+T2lPHhUcHIzdf7mFfqTZkYtM3T/ehzfEr8F02yY" crossorigin="anonymous"></script>
</html>
