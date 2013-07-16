<html>
<head>
	<title>CI Login Page</title>
</head>
<body>
	
	<form method="post" action="http://localhost/netbox/netscan/users/setPassword">

		<table width="400" border="0" cellpadding="5">
			<tr>
				<th width="213" align="right" scope="row"> Email Address</th>
				<td width="161"> <input type="text" name="email" size="20"/></td>
			</tr>
			<tr>
				<th width="213" align="right" scope="row"> New Password</th>
				<td width="161"> <input type="password" name="newpass" size="20"/></td>
			</tr>
			<tr>
				<th align="right" scope="row">Rewrite Password</th>
				<td> <input type="password" name="writepass" size="20"/></td>
			</tr>
			<tr>
				<th align="right" scope="row"></th>
				<td> <input type="submit" name="submit" value="submit"/></td>
			</tr>
		</table>
	</form>
</body>
</html>