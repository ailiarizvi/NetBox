<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js">
</script>

<script type="text/javascript">

$(document).ready(function(){	
	$.getJSON("http://localhost/netbox/netscan/host/gethostinfo",function(result){
	alert(JSON.stringify(result));
	});
});

</script>
</head>
<body>

</body>
</html>