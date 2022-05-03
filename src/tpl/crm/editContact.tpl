<br />
<form method="post" action="/crmGo/editContact">
<table border="0">
	<tr class="snHeaderRow">
		<td colspan="2">Update Contact Information</td>
	</tr>
	<tr class="snRow">
		<td>Name</td>
		<td><input type="text" name="name" class="required" size="50" value="{$contact.name}" /></td>
	</tr>
	<tr class="snRow">
		<td>Country</td>
		<td>
			{msuShowTpl file="select.tpl" from=$countries name="countryCode" selected=$contact.countryCode}
		</td>
	</tr>
	<tr class="snRow">
		<td>Phone</td>
		<td><input type="text" name="phone" class="required" size="40" value="{$contact.phone}" /></td>
	</tr>
	<tr class="snRow">
		<td>Email</td>
		<td><input type="text" name="email" size="80" value="{$contact.email}" /></td>
	</tr>
	<tr class="snRow">
		<td>Traffic Source</td>
		<td><input type="text" name="trafficSource" size="40" value="{$contact.trafficSource}" /></td>
	</tr>
	<tr class="snRow">
		<td>Campaign</td>
		<td><input type="text" name="campaign" size="40" value="{$contact.campaign}" /></td>
	</tr>
	<tr class="snRow">
		<td>Phone2</td>
		<td><input type="text" name="phone2" size="40" value="{$contact.phone2}" /></td>
	</tr>
	<tr class="snSeparatorRow">
		<td colspan="2" height="10">{***********************}</td>
	</tr>
	<tr class="snRow">
		<td>Company</td>
		<td><input type="text" name="company" size="80" value="{$contact.company}" /></td>
	</tr>
	<tr class="snRow">
		<td>Job Title</td>
		<td><input type="text" name="jobTitle" size="60" value="{$contact.jobTitle}" /></td>
	</tr>
	<tr class="snRow">
		<td>Address</td>
		<td><input type="text" name="address" size="100" value="{$contact.address}" /></td>
	</tr>
	<tr class="snRow">
		<td>City</td>
		<td><input type="text" name="city" size="30" value="{$contact.city}" /></td>
	</tr>
	<tr class="snRow">
		<td>State</td>
		<td><input type="text" name="state" size="20" value="{$contact.state}" /></td>
	</tr>
	<tr class="snRow">
		<td>Zip</td>
		<td><input type="text" name="zip" size="16" value="{$contact.zip}" /></td>
	</tr>
	<tr class="snHeaderRow">
		<td colspan="2" align="right">
			<input type="hidden" name="contactId" value="{$contact.id}" />
			<input type="submit" align="right" value="Update" />
		</td>
	</tr>
</table>
</form>
