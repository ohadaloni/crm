{if $loginEmail}
	Welcome {$loginEmail}:
	<a href="/crmGo/settings"
		title="Change Account Settings: password, email"
		alt="Change Account Settings: password, email"
	>Settings</a>,
	{if $controller}
		<a href="/crmGo/land"
			title = "Come straight to this page after I login"
			alt = "Come straight to this page after I login"
		>land here</a>,
	{/if}
	<a href="?logOut=logOut">logout</a>.
{else}
	{include file="login.tpl"}
{/if}
