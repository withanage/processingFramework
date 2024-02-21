{**
 * plugins/generic/controlPublicFiles/templates/settings.tpl
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Settings form for the controlPublicFiles plugin.
 *}
<script>
	$(function() {ldelim}
		$('#jhoveValidatorSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

{translate key="plugins.generic.jhoveValidator.setting.description"}

<form
	class="pkp_form"
	id="jhoveValidatorSettings"
	method="POST"
	action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}"
>
	<!-- Always add the csrf token to secure your form -->
	{csrf}

	{fbvFormArea}
		{fbvFormSection label="plugins.generic.jhoveValidator.setting.enableValidation" for="enableValidation" list=true}
			{fbvElement
				type="checkbox"
				name="enableJhove"
				id="enableJhove"
				checked=$enableJhove
				value=true
				label="plugins.generic.jhoveValidator.setting.enableJhove.description"
				disabled=$disableOpenAire
				translate="true"
			}

		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormButtons submitText="common.save"}
</form>
