<table border="0">
	<tr>
		{foreach from=$flags item=flag}
			<td>
				{if $flag.cnt}
					<a href="/crmGo/contacts?countryCode={$flag.countryCode}"><img
						border="0"
						width="16" height="11"
						src="/images/flags/{$flag.img}"
						title="{$flag.country}: {$flag.cnt}"
					/></a>
				{else}
					<img
						border="0"
						width="16" height="11"
						src="/images/flags/{$flag.img}"
						title="{$flag.country}: -"
						style="opacity:0.4;"
					/>
				{/if}
			</td>
		{/foreach}
	<tr>
</table>
