{**
 * @param $title
 * @param $notifications
 * @param $events
 *}
<div class="eventList col-sm-6">
	<div class="page-header">
		<h2 style="display:inline;">{$title}</h2>
		<div style="float: right;">
			<button class="allRead btn btn-primary" type="button">すべて既読にする</button>
		</div>
	</div>
	<div class="list-group">
		{foreach $notifications as $notification}
			{if $notification->status == 'create'}{$statusLabel = 'primary'}{/if}
			{if $notification->status == 'update'}{$statusLabel = 'success'}{/if}
			{if $notification->status == 'delete'}{$statusLabel = 'warning'}{/if}

			<a href="{$notification->subjectUrl}" class="list-group-item notificationLink" data-module-id="{$notification->moduleId}" data-item-id="{$notification->itemId}">
				<h4 class="list-group-item-heading">{$notification->subject}</h4>
				<p class="list-group-item-text">
					{$notification->receiveTime|date_format:'%F %T'}
					<span class="label label-{$statusLabel}">{$notification->status}</span>
					{$notification->senderName}
				</p>
				<p>{$notification->abstract}</p>
			</a>
			{foreachelse}
			なし
		{/foreach}
	</div>
</div>