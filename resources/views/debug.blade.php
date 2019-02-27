<!DOCTYPE html>
<html>
	<body>
		@foreach ($debug as $message)

			@if(is_string($message) || is_numeric($message))
				<p>{{$message}}</p>
			@else<?php 
				ob_start();
				var_dump($message);
				$result = ob_get_clean();
				echo "<p>".$result."</p>";
			?>@endif
		@endforeach
	</body>
</html>