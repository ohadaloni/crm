{if $users}
<table border="0" cellpadding="3" cellspacing="3">
	<tr class="snHeaderRow">
		<td colspan="10" align="center">Users</td>
	</tr>
	<tr class="snHeaderRow">
		<td>loginEmail</td>
		<td>Last Seen</td>
		<td>Last Activity</td>
		<td>Last Looked At</td>
		<td>Last Approached</td>
		<td>Assignees</td>
	</tr>
	{foreach from=$users item=user}
		<tr class="snRow">
			<td>
				<a target="crmUser"
					href="/crmGo/user?userId={$user.id}"
					>{$user.loginEmail}</a>
			</td>
			<td>{$user.lastSeen|e2h}</td>
			<td>{$user.lastTouched|e2h}</td>
			<td>{$user.lastFelt|e2h}</td>
			<td>{$user.lastHealed|e2h}</td>
			<td>
				<a href="/crmGo/bulkAssignUp?userId={$user.id}"><img
					border="0"
					src="/images/thumbDown.png"
					title="Assign All contacts from {$user.loginEmail} to me ({$userId|userName})"
				/></a>
				<a href="/crmGo/bulkAssign?userId={$user.id}&howMany=7"><img
					border="0"
					src="/images/redCircle16x16.png"
					title="Assign 7 contacts from me ({$userId|userName}) to {$user.loginEmail}"
				/></a>
				<a href="/crmGo/bulkAssign?userId={$user.id}&howMany=100"><img
					border="0"
					src="/images/orangeCircle16x16.png"
					title="Assign 100 contacts from me ({$userId|userName}}} to {$user.loginEmail}"
				/></a>
				<a href="/crmGo/bulkAssign?userId={$user.id}&howMany=1000"><img
					border="0"
					src="/images/greenCircle16x16.png"
					title="Assign 1000 contacts from me ({$userId|userName}}} to {$user.loginEmail}"
				/></a>
				({$user.id|numAssigned|number_format})
			</td>
		</tr>
	{/foreach}
</table>
{/if}
