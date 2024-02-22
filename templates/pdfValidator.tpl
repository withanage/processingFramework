{**
 * templates/TextureArticleGalley.tpl
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Texture editor page
 *}
<script type="text/javascript">

	$(function() {ldelim}
		$('#pdfValidatorForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim})

</script>


<form class="pkp_form" id="pdfValidatorForm" method="post" action="{url op="validatePDF" submissionId=$submissionId stageId=$stageId fileStage=$fileStage submissionFileId=$submissionFileId}">

    {csrf}

	{fbvFormArea id="pdfValidatorFormArea"}

    {fbvFormSection title=""}


    {/fbvFormSection}

    {/fbvFormArea}

    {fbvFormButtons submitText="common.save"}
</form>

