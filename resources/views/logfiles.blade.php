<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>BBTools Logs</title>
  </head>
  <body>
    @foreach ($logs as $log)
      @if($log != ".gitignore" && $log != ".")
        <div><a href="log/{{$log}}">{{$log}}</a></div>
      @endif
    @endforeach
  </body>
</html>
