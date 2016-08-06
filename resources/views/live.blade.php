<!DOCTYPE html>
<html>
<head>
	<title>Live</title>

	<meta name="viewport" content="width=device-width, user-scalable=no">

	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

</head>
<body>

	<div class="container">
		<live></live>
	</div>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.3.7/socket.io.min.js"></script>
	<script src="{{ elixir('js/live.js') }}"></script>

</body>
</html>