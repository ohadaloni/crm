<table border="0" cellpadding="3" cellspacing="3" width="100%">
	<tr class="snHeaderRow">
		<td colspan="8" align="center">Contacts</td>
		<td>Priority</td>
		<td>Tags</td>
		<td rowspan="2">Answering?</td>
	</tr>
	<tr class="snHeaderRow">
		<td></td>
		<td>Name</td>
		<td>Assigned</td>
		<td>
			<img
				border="0"
				src="/images/world.png"
				title="Country"
			/>
		</td>
		<td align="center">
			<img
				border="0"
				src="/images/clock.png"
				title="Time Now"
			/>
		</td>
		<td>Phone</td>
		<td align="center">
						<img border="0" src="/images/email.png"
							title="Email"
					/></a>
		</td>
		<td>Last</td>
		<td>
			<table border="0">
				<tr>
					{foreach from=$prioritySums key=priority item=sum}
						{if $sum < 10}
							{assign var=label value=$sum}
						{else}
							{assign var=label value="#"}
						{/if}
						<td width="18">
							<a
								title="{$priority}: {$sum}"
								href="/crmGo/contacts?priority={$priority}"
							>{$label}</a>
						</td>
					{/foreach}
				</tr>
			</table>
		</td>
		<td>
			<table border="0">
				<tr>
					{foreach from=$tagSums key=tagId item=sum}
						{if $sum < 10}
							{assign var=label value=$sum}
						{else}
							{assign var=label value="#"}
						{/if}
						<td width="18">
							<a
								title="{$tagId|tagName}: {$sum}"
								href="/crmGo/contacts?tagId={$tagId}"
							>{$label}</a>
						</td>
					{/foreach}
				</tr>
			</table>
		</td>
	</tr>
	{foreach from=$contacts item=contact}
		<tr class="snRow">
			<td>
				<a href="/crmGo/editContact?contactId={$contact.id}"><img
					border="0"
					width="16" height="16"
					src="/images/edit.png"
					title="Edit Contact Info"
				/></a>
			</td>
			<td>
				<a href="/crmGo/contact?contactId={$contact.id}"
					title="TrafficSource:{$contact.trafficSource}, Campaign:{$contact.campaign}"
				>{$contact.name}</a>
			</td>
			<td>
				<table border="0">
					<tr>
						<td>
							<a
									title="{$contact.assignedId|userName}"
								>{$contact.assignedId|userName|substr:0:1}</a>

						</td>
						{if $mgrId}
							<td>
								<a href="/crmGo/assign?contactId={$contact.id}&amp;assignedId={$mgrId}&amp;back=contacts"
									><img
										border="0"
										width="16" height="16"
										src="/images/arrowUp.png"
										title="assign to {$mgrId|userName}"
								/></a>
							</td>
						{/if}
						{foreach from=$myAssignees item=assignedId}
							<td>
								<a href="/crmGo/assign?contactId={$contact.id}&amp;assignedId={$assignedId}&amp;back=contacts"
									title="assign to {$assignedId|userName}"
									>{$assignedId|userName|substr:0:1}</a>
							</td>
						{/foreach}
					</tr>
				</table>
			</td>
			<td>
				<a
					title="{$contact.country}"
					href="/crmGo/contacts?countryCode={$contact.countryCode}"
				><img 
					border="0"
					src="/images/flags/{$contact.countryCode|strtolower}.png"
				/></a>
			</td>
			<td align="center">
				<a
					target="worldTimeServer"
					href="http://www.worldtimeserver.com/current_time_in_{$contact.countryCode}.aspx"
					><img border="0" src="/images/clock.png"
						title="Time In {$contact.country}"
				/></a>
			</td>
			<td>
				{$contact.phone}
				{*****************}
				{if $contact.quali == 1}
					<img border="0"
						src="/images/thumbUp.png"
						width="16" height="16"
						title="{$contact.phone} is Qalified"
					/>
				{else}
					<img border="0" src="/images/thumbDown.png"
						width="16" height="16"
						title="{$contact.phone} is Not Qalified"
					/>
				{/if}
				{*****************}
			</td>
			<td align="center">
				{if $contact.email}
					<a
						target="mailTo"
						href="mailto:{$contact.email}?Subject=Hi&body={"Hi `$contact.name`,...Sincerly,`$userName`"|urlEncode}"
						><img border="0" src="/images/email.png"
							title="Email {$contact.name} at {$contact.email}"
					/></a>
				{/if}
			</td>
			<td>
				{if $contact.lastTouch}
					{$contact.lastTouch}
					{if $contact.lastChangeBy != $loginEmail}
						<a
							title="{$contact.lastChangeBy}"
						>({$contact.lastChangeBy|substr:0:1})</a>
					{/if}
				{/if}
			</td>
			<td>
				<table border="0">
					<tr>
						{foreach from=$priorities key=priority item=img}
							<td width="18">
								{if $contact.priority == $priority}
									<img border="0" src="/images/{$img}"
										width="16" height="16"
										title="Priority Now: {$priority}"
										/>
								{else}
									<a
										href="/crmGo/setPriority?contactId={$contact.id}&amp;priority={$priority}"
										><img border="0" src="/images/fade/{$img}"
											width="16" height="16"
											title="Set Priority to {$priority}"
											/></a>
								{/if}
							</td>
						{/foreach}
					</tr>
				</table>
			</td>
			<td>
				<table border="0">
					<tr>
						{foreach from=$contact.tags item=tag}
							<td width="18">
								{if $tag.onOff}
									<a
										href="/crmGo/unTag?contactId={$contact.id}&amp;tagId={$tag.id}"
										><img border="0" src="/images/{$tag.img}"
										width="16" height="16"
										title="Turn {$tag.name} off"
										/></a>
								{else}
									<a
										href="/crmGo/tag?contactId={$contact.id}&amp;tagId={$tag.id}"
										><img border="0" src="/images/fade/{$tag.img}"
										width="16" height="16"
										title="Turn {$tag.name} on"
										/></a>
								{/if}
							</td>
						{/foreach}
					</tr>
				</table>
			</td>
			<td>
				<table border="0" width="100%">
					<tr>
						<td align="left">
							<a href="/crmGo/answered?contactId={$contact.id}"><img border="0"
								src="/images/thumbUp.png"
								width="16" height="16"
								title="{$contact.name} just answered phone"
							/></a>
						</td>
						<td align="right">
							<a href="/crmGo/noAnswer?contactId={$contact.id}"><img border="0"
									src="/images/thumbDown.png"
									width="16" height="16"
									title="{$contact.name} phone rings but no answer"
								/></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	{/foreach}
</table>
