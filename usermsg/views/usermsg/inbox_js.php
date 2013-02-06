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

function userMsgSendReply(id, isSend){
	if(!isSend){
		$("#usermsgReplyDiv_"+id).hide();
		$("#usermsgReplyDiv_"+id+" textarea").val('');
	}
	else
	{
		var subject = $("#usermsgReplyDiv_"+id+" input").val();
		var message = $("#usermsgReplyDiv_"+id+" textarea").val();
		
		//turn on the waiter
		$("#sendMessageWaitHolder").html('<img src="<?php echo url::base();?>media/img/loading_y.gif" />');
		
		//send the data to the server
		$.post("<?php echo url::base()?>usermsg/send_reply", { "id":id,
				"subject":subject,
				"message":message },
				  function(data){
					$("#sendMessageWaitHolder").html(''); //turn off waiter
				    if(data.status == "success")
				    {
					    alert("Message sent successfully");					   
						$("#usermsgReplyDiv_"+id+" textarea").val('');
						$("#usermsgReplyDiv_"+id).hide();
				    }
				    else
				    {
					    if(typeof data.message == 'undefined')
					    {
					    	alert("Error sending. Please try again.");
					    }
					    else
					    {
						    alert(data.message);
					    }
				    }
				  }, "json");  
	}	
}