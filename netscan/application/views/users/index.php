<html>
<head>
	<title>CI Login Page</title>
</head>
<body>
	
	<form method="post" action="http://localhost/netbox/netscan/users/login">

		<table width="400" border="0" cellpadding="5">
			<tr>
				<th width="213" align="right" scope="row"> Email ID</th>
				<td width="161"> <input type="text" name="email" size="20"/></td>
			</tr>
			<tr>
				<th align="right" scope="row"> Password</th>
				<td> <input type="password" name="password" size="20"/></td>
			</tr>
			<tr>
				<th align="right" scope="row"> Remember me</th>
				<td> <input type="checkbox" name="rememberme" value="1"></td>
			</tr>
			 
			<tr>
				<th align="right" scope="row"></th>
				<td> <input type="submit" name="login" value="login"/></td>
			</tr>
		</table>
	</form>
</body>
</html>