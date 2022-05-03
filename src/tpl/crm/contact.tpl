{if $myAssignees}
	<br />
	<br />
	<table border="0">
		<tr class="snZebra2">
			<td>Assign {$contact.name} to:</td>
			{foreach from=$myAssignees item=assignedId}
				<td><a href="/crmGo/assign?contactId={$contact.id}&assignedId={$assignedId}&back=contact">{$assignedId|userName}</a></td>
			{/foreach}
		</tr>
	</table>
	<br />
	<br />
	<br />
{/if}
<table border="0">
	<tr class="snHeaderRow">
		<td colspan="2" align="center">
			<a href="/crmGo/editContact?contactId={$contact.id}"><img
				border="0"
				src="/images/edit.png"
				title="Edit Contact"
				alt="Edit Contact"
			/></a>
			{$contact.name} - {$contact.countryCode} - {$contact.phone}
			{*****************}
			{if $contact.quali == 1}
				<img border="0" src="/images/thumbUp.png"
					alt="{$contact.phone} is Qalified"
					title="{$contact.phone} is Qalified"
				/>
			{else}
				<img border="0" src="/images/thumbDown.png"
					alt="{$contact.phone} is Not Qalified"
					title="{$contact.phone} is Not Qalified"
				/>
			{/if}
			{*****************}
			{if $contact.phone2}
				(
					{$contact.phone2}
					{*****************}
					{if $contact.quali2 == 1}
						<img border="0" src="/images/thumbUp.png"
							alt="{$contact.phone2} is Qalified"
							title="{$contact.phone2} is Qalified"
						/>
					{else}
						<img border="0" src="/images/thumbDown.png"
							alt="{$contact.phone2} is Not Qalified"
							title="{$contact.phone2} is Not Qalified"
						/>
					{/if}
					{*****************}
				)
			{/if}
		</td>
	</tr>
	<tr class="snSeparatorRow">
		<td colspan="2" height="10">{***********************}</td>
	</tr>
	<tr class="snRow">
		<td>Email</td>
		<td>
			<a href="mailto:{$contact.email}">{$contact.email}</a>
		</td>
	</tr>
	<tr class="snRow">
		<td>Traffic Source</td>
		<td>
			{$contact.trafficSource}
		</td>
	</tr>
	<tr class="snRow">
		<td>Campaign</td>
		<td>
			{$contact.campaign}
		</td>
	</tr>
	<tr class="snRow">
		<td>Priority</td>
		<td>
			{$contact.priority}
		</td>
	</tr>
	<tr class="snRow">
		<td>Created On</td>
		<td>{$contact.lastTouch}</td>
	</tr>
	<tr class="snRow">
		<td>By</td>
		<td>{$contact.createdBy}</td>
	</tr>
	<tr class="snRow">
		<td>Last Change</td>
		<td>{$contact.lastTouch}</td>
	</tr>
	<tr class="snSeparatorRow">
		<td colspan="2" height="10">{***********************}</td>
	</tr>
	<tr class="snRow">
		<td>Company</td>
		<td>{$contact.company}</td>
	</tr>
	<tr class="snRow">
		<td>Job Title</td>
		<td>{$contact.jobTitle}</td>
	</tr>
	<tr class="snRow">
		<td>Address</td>
		<td>{$contact.address}</td>
	</tr>
	<tr class="snRow">
		<td>City</td>
		<td>{$contact.city}</td>
	</tr>
	<tr class="snRow">
		<td>State</td>
		<td>{$contact.state}</td>
	</tr>
	<tr class="snRow">
		<td>Zip</td>
		<td>{$contact.zip}</td>
	</tr>
	<tr class="snSeparatorRow">
		<td colspan="2" height="10">{***********************}</td>
	</tr>
</table>
{****************************************************************}
{if $comments}
<br />
<table border="0">
	<tr class="snHeaderRow">
		<td colspan="2" align="center">Comments</td>
	</tr>
	<tr class="snHeaderRow">
		<td></td>
		<td align="right">when</td>
		<td>by</td>
	</tr>
	{foreach from=$comments item=comment}
		<tr class="snRow">
			<td>{$comment.comment}</td>
			<td align="right">{$comment.datetime}</td>
			<td>{$comment.lastChangeBy}</td>
		</tr>
	{/foreach}
</table>
{/if}
{****************************************************************}
{if $calls}
<br />
<table border="0">
	<tr class="snHeaderRow">
		<td colspan="2" align="center">Call History</td>
	</tr>
	{foreach from=$calls item=call}
		<tr class="snRow">
			<td align="right">{$call.datetime}</td>
			<td>
				{if $call.answer == 1}
					<img border="0"
						src="/images/thumbUp.png"
						width="16" height="16"
						alt="answered"
						title="answered"
					/>
				{else}
					<img border="0" src="/images/thumbDown.png"
						width="16" height="16"
						alt="no answer"
						title="no answer"
					/>
				{/if}
				{*****************}
			</td>
		</tr>
	{/foreach}
</table>
{/if}
{****************************************************************}
<br />
<form method="post" action="/crmGo/comment">
<table border="0">
	<tr class="snHeaderRow">
		<td>Add a Comment:</td>
	</tr>
	<tr class="snRow">
		<td><textarea name="comment" rows="5" cols="80"></textarea></td>
	</tr>
	<tr class="snHeaderRow">
		<td align="right">
			<input type="hidden" name="contactId" value="{$contact.id}" />
			<input type="submit" align="right" value="Add comment for {$contact.name}" />
		</td>
	</tr>
</table>
</form>
