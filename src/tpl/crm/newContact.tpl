<br />
<form method="post" action="/crmGo/newContact">
<table border="0">
	<tr class="snHeaderRow">
		<td colspan="2">New Contact</td>
	</tr>
	<tr class="snRow">
		<td>Name</td>
		<td><input type="text" name="name" class="required" size="50" /></td>
	</tr>
	<tr class="snRow">
		<td>Country</td>
		<td>
			{msuShowTpl file="select.tpl" from=$countries name="countryCode"}
		</td>
	</tr>
	<tr class="snRow">
		<td>Phone</td>
		<td><input type="text" name="phone" class="required" size="40" /></td>
	</tr>
	<tr class="snRow">
		<td>Email</td>
		<td><input type="text" name="email" size="80" /></td>
	</tr>
	<tr class="snRow">
		<td>Traffic Source</td>
		<td><input type="text" name="trafficSource" size="50" /></td>
	</tr>
	<tr class="snRow">
		<td>Campaign</td>
		<td><input type="text" name="campaign" size="40" /></td>
	</tr>
	<tr class="snSeparatorRow">
		<td colspan="2">More:</td>
	</tr>
	<tr class="snRow">
		<td>Phone2</td>
		<td><input type="text" name="phone2" size="40" /></td>
	</tr>
	<tr class="snRow">
		<td>Company</td>
		<td><input type="text" name="company" size="80" /></td>
	</tr>
	<tr class="snRow">
		<td>Job Title</td>
		<td><input type="text" name="jobTitle" size="60" /></td>
	</tr>
	<tr class="snRow">
		<td>Address</td>
		<td><input type="text" name="address" size="100" /></td>
	</tr>
	<tr class="snRow">
		<td>City</td>
		<td><input type="text" name="city" size="30" /></td>
	</tr>
	<tr class="snRow">
		<td>State</td>
		<td><input type="text" name="state" size="20" /></td>
	</tr>
	<tr class="snRow">
		<td>Zip</td>
		<td><input type="text" name="zip" size="16" /></td>
	</tr>
	<tr class="snHeaderRow">
		<td colspan="2" align="right">
			<input type="submit" align="right" value="Create New Contact Record" />
		</td>
	</tr>
</table>
</form>
