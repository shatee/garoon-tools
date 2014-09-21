{extends file="base.tpl"}

{block name="head" append}
	<script type="text/javascript" src="/js/app/notification.js"></script>
	<link href="/css/app/notification.css" rel="stylesheet"/>
{/block}

{block name="headerTitle"}通知{/block}

{block name="contentMain"}
	{include file="App/Notification/list.tpl" title="その他" notifications=$categorizedNotifications.other}
	{include file="App/Notification/list.tpl" title="投入予定" notifications=$categorizedNotifications.deploySchedule}
	{include file="App/Notification/list.tpl" title="休み" notifications=$categorizedNotifications.rest}
	{include file="App/Notification/list.tpl" title="非公開" notifications=$categorizedNotifications.private}

	{include file="App/Notification/event.tpl"}
{/block}
