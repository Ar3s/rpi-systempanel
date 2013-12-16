{if count($widget->updates) > 0}
<div class="row">
	<div class="col-xs-12">
		<ul class="list-group">
			{for $i = 0 to count($widget->updates) - 1}
				{$update = $widget->updates[$i]}
				<li class="list-group-item">{$update}</li>
			{/for}
		</ul>
	</div>
</div>
{/if}
<script type="text/javascript">
	function callbackUpdatesFunc(data)
	{
		$('#{$user_widget_info->id_html} .panel-body').html(data);
	}

	$(document).ready(function () {
		setTimeout(function () {
			updateWidgetHtml({$user_widget_info->id}, '{$sid}', callbackUpdatesFunc, null);
		}, {$widget_info->updatetime});
	});
</script>