<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Creates the HTML to render an inbox for users
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Message - http://ethertontech.com
 */
?>

$(function() {
 
    // if the function argument is given to overlay,
    // it is assumed to be the onBeforeLoad event listener
    $("a[rel]").overlay({
 
        mask: 'gray',
        effect: 'apple',
 
        onBeforeLoad: function() {
 
            // grab wrapper element inside content
            var wrap = this.getOverlay().find(".contentWrap");
 
            // load the page specified in the trigger
            wrap.load(this.getTrigger().attr("href"));
        }
 
    });
});


function userMsgReply(id){
	$("#usermsgReplyDiv_"+id).show();
}

function deleteMsg(id)
{
	if(confirm("Are you sure you want to delete this message?")){
		$("#message_id").val(id);
		$("#inboxForm").submit();
	}
}

