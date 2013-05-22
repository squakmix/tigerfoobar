

$(document).ready(function() {
	resetScale();
	

	/*------Upvoting Industry Tags-------*/


	//Onclick, send vote to server
	//switch value when clicked
	$('.tagUpvote').click(function() {
		sendTagUpvote($(this));
	});

	//Sends the upvote or downvote to the server using the type of tag
	//(industry or claim tag) and other specific tag information
	function sendTagUpvote(button) {
		var voted = parseInt($(button).attr('voted')); //0 if not voted, 1 if voted
		//alert($(this).attr('tagid'));
		var clicked = $(button);
		//Dom changes
		var oldVotes = parseInt($(clicked.parent().children(".tagTotal")[0]).text());
		if(!voted) {		//just voted, add vote
			clicked.text('-');
			clicked.attr('voted','1');
			oldVotes ++;
			clicked.parent().addClass('userVoted');
		}else {				//just unvoted, remove vote
			clicked.text('+');
			clicked.attr('voted','0');
			oldVotes --;
			clicked.parent().removeClass('userVoted');
		}
		$(clicked.parent().children(".tagTotal")[0]).text(oldVotes);
		$.ajax({
			type: 'POST',
			url: 'http://127.0.0.1/action/upvoteIndustry',
			data: {
				industryID: $(clicked).attr('tagid'),
				objectID: $(clicked).attr('objectid'), //the objet (claim or company) being affected
				tagType: $(clicked).attr('tagtype'),
				voted: voted
			},
			dataType: 'json',
			success: function(json) {
				//Dom changes processed pre-query
			},
			error: function(json) {
				//alert error message for now
				alert('Server Error');
			}
		});
	}

	//Displays textbox to add new industry tag
	$('#addTag').click( function() {
		$('#newTagPopup').show(200);
		$('#newclaimtag_name').focus();
	});

	//Autocomplete for adding new industry
	var projects = [
		{
			value: "jquery",
			label: "jQuery",
			desc: "the write less, do more, JavaScript library",
			icon: "jquery_32x32.png"
		}
	];

	//Called when typing into new industry/new tag text box
	$( "#newtag_name" ).autocomplete({
		minLength: 0,
		source: function (request, response) {
	        $.ajax({

	            url: "/data/industryList/" + $('#newtag_name').val(),
	            data: {tagtype: $("#newtag_name").attr('tagtype')},
	            dataType: 'json',
	            success: function (data) {
	                response(data.map(function (value) {
	                    return {
	                        'label':  value.value ,
	                        'value': value.label
	                    };  
	                }));
	            }   
	        }); 
	    }, 
	    select: function( event, ui ) {
	    	/*
			alert( ui.item ?
			"Selected: " + ui.item.value :
			"Nothing selected, input was " + this.value );
			*/
			sendNewTag(ui.item.label,ui.item.value);
		}
	});
	
	//Sends information about the newly created
	//industry-company connection to the server

	//TODO: 
	function sendNewTag(label, value) {
		var newLink = $('<li>', {
			"html": '	<span class="tagName">' + label + '</span>' +
					'	<span>(</span> ' +
					'		<span class="industryTotal">0</span>' +
					'	<span>)</span>' +
					'	<span class="industryUpvote" tagid="'+ value + '" objectid="'+ $('#industryTags').attr('objectid') +'" voted="0">' +
					'		+  ' +
					'	</span>'
		});
		$('#industryTags').append(newLink);
		//call current vote method, triger click
		$(newLink.children('.industryUpvote')[0]).click(function() {
			sendTagUpvote($(this));
		});
		$(newLink.children('.industryUpvote')[0]).click();
	}


	/*-----------------Kudos Scale-----------------------*/


	//Alert Kudos value on hover
	$('.scoreBox').hover(
		function() {
			if ($(this).attr('value') == 0)
				$(this).text('F');
			else
				$(this).text($(this).attr('value'));
		},
		function() {
			$(this).text('');
		}
	);
	
	$('.scoreBox').click(function() {
		resetScale();
		$(this).addClass('selectedRating');
	});

	//Method found in general.js
	applyColors(parseFloat($('#averageScore').text()), $('#averageScore'), 'color');
	applyColors(parseFloat($('#averageScore').text()), $('#scoreContent'), 'border-left', '5px solid ');
	applyColors(parseFloat($('#averageScore').text()), $('#claimPopTags li'), 'background-color');
	
	$.each($('.claimScore'), function() {
		applyColors(parseFloat($(this).text()), $(this).parent(), 'background-color');
	});

	$.each($('#discussionContent li'), function() {
		applyColors(parseInt($(this).attr('value')), $(this), 'border-left', '5px solid ');
	});

	//Resets and recolors the kudos scale to get rid of border color
	function resetScale() {
		$.each($('.scoreBox'), function(i) {
			$(this).css('background-color', colors[i]);
			$(this).css('border', '2px solid ' + colors[i]);
			$(this).removeClass('selectedRating');
		});
	}

	/*-----------------Discussion-----------------------*/

	$('#newComment').click(function() {
		$('#newCommentPopup').show(200);
		$('#newCommentPopup textarea').focus();
		$('.lightsout').fadeIn();
	});

	$('.cancelButton').click(function() {
		$('#newCommentPopup').hide(200);
		$('.lightsout').fadeOut();
	});

	$('.reply').click(function() {
		$parentLi = $(this).parent().parent().attr('id');
		$('#' + $parentLi + 'reply').show();
		$('#' + $parentLi + 'reply textarea').focus();
	});

	$('.submitReply').click(function() {
		
	});

	$('.cancelReply').click(function() {
		$('.replyBox').hide();		
	});

	$('.buttonsContainer').hover(function() {
		$(this).css('opacity', '1');
	}, function() {
		$(this).css('opacity', '0.4');
	});

	$('#discussionContent li').click(function() {
		console.log('collapse all children of this');
	});
});
