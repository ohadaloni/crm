<table border="0">
	<tr>
		<td>
			<img
				border="0"
				width="16" height="16"
				src="/images/campaign.png"
				title="campaign"
			/>
		</td>
		{foreach from=$campaigns item=campaign}
			<td>
				{if $campaign.cnt}
					<a href="/crmGo/contacts?campaign={$campaign.campaign}">{$campaign.campaign}</a>
				{else}
					{* {$campaign.campaign} *}
				{/if}
			</td>
		{/foreach}
	<tr>
</table>
