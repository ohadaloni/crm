<table border="0">
	<tr>
		<td valign="top">
			<table border="0">
				<tr class="crmHeaderRow">
					<td>Files</td>
				</tr>
				{foreach from=$files item=file}
					<tr class="crmRow">
						<td>
							<a href="/showSource?file={$file}">{$file}</a>
						</td>
					</tr>
				{/foreach}
			</table>
		</td>
		<td valign="top">
			{if $file}
				<h4>{$file}</h4>
				{$source}
			{/if}
		</td>
	</tr>
</table>
