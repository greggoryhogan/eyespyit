{% variable messageClasses %}
	wcMessage {% if isAuthorWpUser %} wcWpMessage {% endif isAuthorWpUser %} {% if isAuthorCurrentUser %} wcCurrentUserMessage {% endif isAuthorCurrentUser %} {% if hidden %} wcMessageHidden {% endif hidden %} {% if !allowedToGetTheContent %} wcInvisible {% endif allowedToGetTheContent %}{{ cssClasses }}
{% endvariable messageClasses %}

<div class="{{ messageClasses }}" data-id="{{ messageId }}" data-chat-user-id="{{ messageChatUserId }}">
	{% if avatarUrl %}
		<img class="wcMessageAvatar" src="{{ avatarUrl }}" />
	{% endif avatarUrl %}

	<div class="wcMessageContentContainer">
		<span class="wcMessageContent" {% if isTextColorSetForMessage %}style="color:{{ textColor }}"{% endif isTextColorSetForMessage %}>
			<span class="wcMessageUser" {% if isTextColorSetForUserName %}style="color:{{ textColor }}"{% endif isTextColorSetForUserName %}>
				{{ renderedUserName }}:
			</span>
			<span class="wcMessageContentInternal">{{ messageContent }}</span>
		</span>
		<br class='wcClear' />

		<span class="wcMessageFooter">
			<a href="#" class="wcAdminAction wcMessageApproveButton wcInvisible" data-id="{{ messageId }}" title="Approve the message"></a>
			<a href="#" class="wcAdminAction wcMessageDeleteButton wcInvisible" data-id="{{ messageId }}" title="Delete the message"></a>
			<a href="#" class="wcAdminAction wcMessageEditButton wcInvisible" data-id="{{ messageId }}" title="Edit the message"></a>
			<a href="#" class="wcAdminAction wcUserBanButton wcInvisible" data-id="{{ messageId }}" title="Ban this user"></a>
			<a href="#" class="wcAdminAction wcUserKickButton wcInvisible" data-id="{{ messageId }}" title="Kick this user"></a>
			<a href="#" class="wcAdminAction wcSpamReportButton wcInvisible" data-id="{{ messageId }}" title="Report spam"></a>
			<span class="wcMessageTime" data-utc="{{ messageTimeUTC }}"></span>
		</span>
		<br class='wcClear' />
	</div>
</div>