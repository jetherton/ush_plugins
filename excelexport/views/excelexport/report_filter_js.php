<script type="text/javascript">

/**
 * Toggle AND or OR
 */
function excelExportClick(isHtml)
{
	var url =  	"<?php echo url::base()?>excelexport?";


	// Check if there are any parameters
	if ($.isEmptyObject(urlParameters))
	{
		urlParameters = {show: "all"}
	}

	url += $.param(urlParameters);

	if(isHtml)
	{
		url += "&html=true";
	}
	
	window.location = url;
	return false;
}

/**
 * Set the selected categories as selected
 */
$(document).ready(function() {

	var html = '<br/><a href="<?php echo url::base()?>excelexport?" id="excelExport" onclick="excelExportClick(false); return false;" class="exportButton"><?php echo Kohana::lang('excelexport.export_to_csv')?></a>';
	$("#filter-controls p").append(html);

	html = '<br/><br/><a href="<?php echo url::base()?>excelexport" id="excelExport" onclick="excelExportClick(true); return false;" class="exportButton"><?php echo Kohana::lang('excelexport.export_to_html_table')?></a>';
	$("#filter-controls p").append(html);
});




</script>