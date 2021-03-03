<table border="0">
	<tr>
		<td>
			<img
				border="0"
				width="16" height="16"
				src="/images/traffic.png"
				title="trafficSource"
				alt="trafficSource"
			/>
		</td>
		{foreach from=$trafficSources item=trafficSource}
			<td>
				{if $trafficSource.cnt}
					<a href="/crmGo/contacts?trafficSource={$trafficSource.trafficSource}">{$trafficSource.trafficSource}</a>
				{else}
					{* {$trafficSource.trafficSource} *}
				{/if}
			</td>
		{/foreach}
	<tr>
</table>
