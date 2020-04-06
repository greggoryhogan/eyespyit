<link rel='stylesheet' href='{{ themeStyles }}' type='text/css' media='all' />

<div id='{{ chatId }}' class='wcContainer {% if sidebarMode %} wcSidebarMode {% endif sidebarMode %}'>
	{% if showWindowTitle %}
		<div class='wcWindowTitle'>{{ windowTitle }}&#160;{% if sidebarMode %}<a href="javascript://" class="wcWindowTitleMinMaxLink"></a>{% endif sidebarMode %}</div>
	{% endif showWindowTitle %}

	<div class="wcWindowContent">
		<div class="wcUserNameHint">{{ messageEnterUserName }}</div>
		
		<form method="post" class="wcUserNameForm">
			<input type="hidden" value="1" name="wcUserNameSelection" />
			<input type="text" name="wcUserName" class="wcUserName" required />
			<input type="submit" value="{{ messageLogin }}" />
		</form>
		
		{% if errorMessage %}
			<div class='wcError wcUserNameError'>{{ errorMessage }}</div>
		{% endif errorMessage %}
	</div>
</div>

{{ cssDefinitions }}
{{ customCssDefinitions }}

{% if sidebarMode %}
	<script type='text/javascript'>
		jQuery(window).load(function() {
			var chatId = '{{ chatId }}';
			var channelId = '{{ channelId }}';

			wisechat.utils.htmlUtils.adjustTitleToContent(chatId);
			wisechat.utils.htmlUtils.addMinimalizeFeature(chatId, channelId, {{ fbBottomOffset }}, {{ fbBottomThreshold }});
			{% if isDefaultTheme %}
				wisechat.utils.htmlUtils.adjustContainerBackgroundColorToParent(chatId);
			{% endif isDefaultTheme %}
			wisechat.utils.htmlUtils.adjustBottomOffset(chatId, {{ fbBottomOffset }}, {{ fbBottomThreshold }});
		});
	</script>
{% endif sidebarMode %}