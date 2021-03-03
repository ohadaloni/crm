<br />
<form method="post" class="validateForm" action="/crmGo/newUser">
<table border="0">
	<tr class="snHeaderRow">
		<td colspan="2">New Crm User</td>
	</tr>
	<tr class="snRow">
		<td>name</td>
		<td><input type="text" name="name" size="50" /></td>
	</tr>
	{if $role == 'accountManager'}
		<tr class="snRow">
			<td>Manager</td>
			<td><input type="checkbox" name="manager" /></td>
		</tr>
	{/if}
	<tr class="snRow">
		<td>Login Email</td>
		<td><input type="text" name="loginEmail" size="80" /></td>
	</tr>
	<tr class="snRow">
		<td>Starting Password</td>
		<td><input type="text" name="passwd" size="30" /></td>
	</tr>
	<tr class="snRow">
		<td>phone</td>
		<td><input type="text" name="phone" size="40" /></td>
	</tr>
	<tr class="snHeaderRow">
		<td colspan="2" align="right">
			<input type="submit" align="right" value="New User" />
		</td>
	</tr>
</table>
</form>
