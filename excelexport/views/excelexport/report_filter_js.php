<script type="text/javascript">

/**
 * Toggle AND or OR
 */
function excelExportClick()
{
	var url =  	"<?php echo url::base()?>excelexport?";


	// Check if there are any parameters
	if ($.isEmptyObject(urlParameters))
	{
		urlParameters = {show: "all"}
	}

	url += $.param(urlParameters);
	
	window.location = url;
	return false;
}

/**
 * Set the selected categories as selected
 */
$(document).ready(function() {

	var html = '<br/><a href="<?php echo url::base()?>excelexport" id="excelExport" onclick="excelExportClick(); return false;" class="exportButton"><?php echo Kohana::lang('excelexport.export_to_excel')?></a>';
	$("#filter-controls p").append(html);
});




</script>