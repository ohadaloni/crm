<form method="get" action="/crm/updatePasswd">
	<table border="0">
		<tr class="crm">
			<td colspan="2">Changeing Password for {$loginEmail}</td>
		</tr>
		<tr class="crm">
			<td>Old Password</td>
			<td><input type="text" name="oldPasswd" size="30" /></td>
		</tr>
		<tr class="crm">
			<td>New Password</td>
			<td><input type="text" name="newPasswd" size="30" /></td>
		</tr>
		<tr class="crm">
			<td>New Password (again)</td>
			<td><input type="text" name="newPasswd2" size="30" /></td>
		</tr>
		<tr class="crm">
			<td></td>
			<td><input type="submit" value="Update Password" /></td>
		</tr>
	</table>
</form>
