{% variable messageClasses %}
	wcMessage {% if isAuthorWpUser %} wcWpMessage {% endif isAuthorWpUser %} {% if isAuthorCurrentUser %} wcCurrentUserMessage {% endif isAuthorCurrentUser %} {% if hidden %} wcMessageHidden {% endif hidden %} {% if !allowedToGetTheContent %} wcInvisible {% endif allowedToGetTheContent %}{{ cssClasses }}
{% endvariable messageClasses %}

<div class="{{ messageClasses }}" data-id="{{ messageId }}" data-chat-user-id="{{ messageChatUserId }}">
	{% if avatarUrl %}
		<img class="wcMessageAvatar" src="{{ avatarUrl }}" />
	{% endif avatarUrl %}

	{% if avatarUrl %}
		<div class="wcMessageContainer">
	{% endif avatarUrl %}

	<span class="wcMessageUser" {% if isTextColorSetForUserName %}style="color:{{ textColor }}"{% endif isTextColorSetForUserName %}>
		{{ renderedUserName }}
	</span>
	<span class="wcMessageTime" data-utc="{{ messageTimeUTC }}"></span>
	
	<br class='wcClear' />
	<span class="wcMessageContent" {% if isTextColorSetForMessage %}style="color:{{ textColor }}"{% endif isTextColorSetForMessage %}>
		<span class="wcMessageContentInternal">{{ messageContent }}</span>

		<a href="javascript://" class="wcAdminAction wcMessageApproveButton wcInvisible" data-id="{{ messageId }}" title="Approve the message"></a>
		<a href="javascript://" class="wcAdminAction wcMessageDeleteButton wcInvisible" data-id="{{ messageId }}" title="Delete the message"></a>
		<a href="javascript://" class="wcAdminAction wcMessageEditButton wcInvisible" data-id="{{ messageId }}" title="Edit the message"></a>
		<a href="javascript://" class="wcAdminAction wcUserBanButton wcInvisible" data-id="{{ messageId }}" title="Ban this user"></a>
		<a href="javascript://" class="wcAdminAction wcUserKickButton wcInvisible" data-id="{{ messageId }}" title="Kick this user"></a>
		<a href="javascript://" class="wcAdminAction wcSpamReportButton wcInvisible" data-id="{{ messageId }}" title="Report spam"></a>
		<br class='wcClear' />
	</span>

	{% if avatarUrl %}
		</div>
	<br class='wcClear' />
	{% endif avatarUrl %}
</div>