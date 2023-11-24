{**
 * templates/settings.tpl
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief Settings form of the plugin.
 *}
<script>
	$(function () {ldelim}
		$('#mostCitedSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
		let last = null;
		$('#provider').change(function () {ldelim}
			$('#' + last)
				.addClass('pkp_helpers_display_none')
				.find('input')
				.each((i, input) => input.required = false);
			$('#' + (last = this.value)).removeClass('pkp_helpers_display_none')
				.find('input')
				.each((i, input) => input.required = true);
		{rdelim})
			.trigger('change');
	{rdelim});

	document.querySelectorAll('.checkNum').forEach(function (el) {ldelim}
		el.addEventListener("input", elem => el.value = (isNaN(el.value)) ? el.value.replace(elem.data, '') : el.value);
	{rdelim})
</script>
<form
		class="pkp_form"
		id="mostCitedSettings"
		method="POST"
		action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}"
>
	{csrf}
	{fbvFormArea id="mostCitedSettings"}
		{fbvFormSection list=true label="plugins.generic.mostCited.provider" description="plugins.generic.mostCited.provider.desc"}
			{fbvElement type="select" id="provider" from=$providers selected=$provider size=$fbvStyles.size.SMALL}
		{/fbvFormSection}
		<div id="api_description">{translate key="plugins.generic.mostCited.api.desc"}</div>
		<br/>
		{fbvFormSection id="scopus" class="pkp_helpers_display_none" label="plugins.generic.mostCited.scopus" description="plugins.generic.mostCited.scopus.desc"}
			{fbvElement type="text" password="true" required="true" id="scopusKey" class=$citationsScopusSaved value=$scopusKey label="plugins.generic.mostCited.scopus.key"}
		{/fbvFormSection}
		{fbvFormSection id="crossref" class="pkp_helpers_display_none" label="plugins.generic.mostCited.crossref" description="plugins.generic.mostCited.crossref.desc"}
			{fbvElement type="text" password="true" required="true" id="crossrefUser" class=$citationsCrossrefUserSaved value=$crossrefUser label="plugins.generic.mostCited.crossref.name" inline=true}
			{fbvElement type="text" password="true" required="true" id="crossrefPassword" class=$citationscrossrefPasswordSaved value=$crossrefPassword label="plugins.generic.mostCited.crossref.password" inline=true}
		{/fbvFormSection}
		{fbvFormSection title="plugins.generic.mostCited.quantity"}
			{fbvElement type="text" id="quantity" required="true" class="checkNum" value=$quantity label="plugins.generic.mostCited.quantity.desc"}
		{/fbvFormSection}
		{fbvFormSection title="plugins.generic.mostCited.header"}
			{fbvElement type="text" id="header" value=$header label="plugins.generic.mostCited.header.desc" multilingual=true}
		{/fbvFormSection}
		{fbvFormSection label="plugins.generic.mostCited.position" list=true description="plugins.generic.mostCited.position.desc"}
			{fbvElement type="checkbox" id="position" value="1" checked=$position label="plugins.generic.mostCited.position.check"}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormButtons submitText="common.save"}
</form>
