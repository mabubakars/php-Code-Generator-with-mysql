$(document).ready(function(){
	var bools = 0;
	
	$('#addtable').on('click',function(){
			popup("i am here");
	})
	
	$('a.btn-ok, #dialog-overlay, #dialog-box').click(function () {        
        $('#dialog-overlay, #dialog-box').hide();        
        return false;
    });
    
    // if user resize the window, call the same function again
    // to make sure the overlay fills the screen and dialogbox aligned to center    
    $(window).resize(function () {
        
        //only do it if the dialog box is not hidden
        if (!$('#dialog-box').is(':hidden')) popup();        
    });    
	
	
	$('#db').change(function(){
		$('#mainform').empty();
		var db = $(this).val();
		if(db!=''){
			$.getJSON( 'functions/functions.php?db='+db, function(data) {
				jQuery.fn.print_r(data);
				$('#creatRelation').show();
			});
		}
	});
	$('#mainform').on('click', '.generate', function (event) {
		var jsonData= '';
		var table = $(this).siblings('table');
		jsonData = getJson(table);
		if(jsonData!="false]}"){
		 $.ajax
		({
			type: "POST",
			url: 'functions/functions.php',
			data: 'json='+ jsonData
		}).done(function( html ) {
			$( "#results" ).append( html );
		});
		}
	})
	$('#mainform').on('change','select[name="isparent"]',function(){
			$(this).siblings('select[name="displaymember"]').toggle();
	});
	$('#creatRelation').on('click',function(){
			bools = 1;
	})
	var tr1 = '';
	var tr2 = '';
	var color1 = '';
	var color2 = '';
	$('#show').on('click',function(e){
		var tr = $(e.target).parent();
		if(bools==1){
			if(e.target.tagName=='TD'){
				tr1 = tr;
				color1 = tr1.css('background-Color');
				var text = $(tr1).find('td:nth-child(3)').text();
				if(text=='int' || text=='bigint'){
				}else{
					alert('Primary key must be "int" type.');
					return false;
				}
				tr1.css('background-Color','#8c8c8c');
				bools = 2;
			}
		}else if(bools==2){
			if(e.target.tagName=='TD'){
				tr2 = tr;
				color2 = tr2.css('background-Color');
				var text = $(tr1).find('td:nth-child(3)').text();
				bools = 0;
				if(tr2.children('td:nth-child(4)').text()!='No'){
					alert('Relation Already exist for this field.');
					tr1.css('background-Color',color1);
					tr2.css('background-Color',color2);
					return false;
				}
				if(text=='int' || text=='bigint'){
				var relation = tr1.siblings('.head').find('.table-header-top > a').text() + '.' +  tr1.children('td:nth-child(1)').text();
					var displayFileds = creatDisplayFileds(tr1);
					var isParent = creatIsparent();
					tr2.children('td:nth-child(4)').text(relation);
					tr2.children('td:nth-child(5)').html(displayFileds+isParent);
					tr2.css('background-Color','#8c8c8c');
				}else{
					alert('foriegn key must be "int" type.');
					tr1.css('background-Color',color1);
					tr2.css('background-Color',color2);
					return false;
				}
			}
		}
	});
	function creatDisplayFileds(tr){
		var s = '<select size="3" multiple="" name="displaymember">';
		$.each(tr.siblings('tr'),function(){
			var text = $(this).children('td:nth-child(1)').text();
			if(text!=null && text!=''){
				s+='<option value="'+text+'">'+text+'</option>';
			}
		});
		s+='</select>';
		return s;
	}
	function creatIsparent(){
		var t='<a href="#">is parent </a>';
		t+= '<select name="isparent">';
		t+='<option value="No">No</option>';
		t+='<option value="Yes">Yes</option>';
		t+='</select>';
		return t;
	}
	$('#creatRelation').on({
		mouseenter: function () {
			var position = $(this).position();
			var width = 50;
			$("#tooltip").html("<p>Click on create relation. click on first table attribute and then targetted table attribute. relation will create automatically and show in attribute row. </p>");
			$("#tooltip").css({position: "absolute",top: position.top - 60 + "px",left: (position.left - (400)) + "px"});
			$("#tooltip").show();
			},
			mouseleave: function () {
				$("#tooltip").hide();
			}
		});
});

function getJson(table){
	var th= table.find('.table-header-top a').text();
	var jsonData = '{"'+th+'": [';
	var i= 0;
	table.find('tbody').children("tr").each(function() {
	if($(this).attr('class')!='head'){
		  if(i!=0){
			jsonData+= ',';
		  }	
			var relation = $.trim($(this).find('td:nth-child(4)').text());
			var isParent = $(this).find('select[name="isparent"]').val();
			jsonData += '{';
			jsonData += '"name" : "'+$(this).find('td:nth-child(1)').text()+'",';
			jsonData += '"position" : "'+$(this).find('td:nth-child(2)').text()+'",';
			jsonData += '"DataType" : "'+$(this).find('td:nth-child(3)').text()+'",';
			jsonData += '"relation" : "'+$(this).find('td:nth-child(4)').text()+'",';
			if(relation==='No'){
				jsonData += '"option" : "'+$(this).find('select[name="typename"]').val()+'"';
			}else{
				var value = "";
				$(this).find('select[name="displaymember"]').each(function(i, selected){
					if(i!=0){
						value = ',';
					}
					value += $(selected).val();
				});
				if(value=="null," && isParent=="No"){
					alert("Please select atleast one value from combobox in "+$(this).find('td:nth-child(1)').text());
					jsonData = false;
					return jsonData;
				}
				jsonData += '"option" : [{"displaymemeber" : "'+ value +'"},{"isparent" : "'+$(this).find('select[name="isparent"]').val()+'"}]';
			}
			jsonData += '}';
			i=1;
		}
	})
	jsonData += ']}';
	return jsonData;	
}
(function($){
    $.fn.print_r = function (object) {
        if (jQuery.isArray( object ) || jQuery.type( object ) == 'object') {
            return jQuery.each( object, function(index, value) {
				var t = "";
				t='<div class="draggable">';
				t+='<div class="generate">Generate</div>'
				t+='<table cellspacing="0" cellpadding="0" border="0" class="product-table">';
				t+='<tr class="head">';
				t+='	<th colspan= "5" class="table-header-top"><a href="">'+index+'</a><span style="float:right; margin:0px 5px 0px 0px; color:white;"><select><option value="Yes">Yes</Option><option value="No">No</Option></select><span></th>';
				t+='	</tr>';
				t+='<tr class="head">';
				t+='	<th class="table-header-repeat line-left"><a href="">Name</a>	</th>';
				t+='	<th class="table-header-repeat line-left"><a href="">Position</a></th>';
				t+='	<th class="table-header-repeat line-left"><a href="">Data Type</a></th>';
				t+='	<th class="table-header-repeat line-left"><a href="">Relation</a></th>';
				t+='	<th class="table-header-repeat line-left"><a href="">Options</a></th>';
				t+='</tr>';
					jQuery.each( value, function(ind, val) {
						ind ++;
						t+='<tr>';
						t+='<td>' + val.name + '</td>';
						t+='<td>' + val.position + '</td>';
						t+='<td>' + val.DataType + '</td>';
						t+='<td>';
						if(val.relation!= ''){
							t+= val.relation['table']+'.'+val.relation['column'];
						}
						else{
							t+='No';
						}
						t+='</td>';
						t+='<td>';
						
						if(val.relation!= ''){
							t+='<select name="displaymember" multiple size=3>';
							jQuery.each(val.relation.displayFileds, function(i, v) {
								t+='<option value="'+v+'">'+v+'</option>';
							});
							t+='</select>';
							t+='<br>'
							t+='<a href="#">is parent </a>'
							t+= '<select name = "isparent">';
							t+= '<option value="No">No</option>';
							t+= '<option value="Yes">Yes</option>';
							t+= '</select>';
						}else{
							t+='<select name="typename">';
							t+='<option value="textbox">textbox</option>';
							t+='<option value="checkbox">checkbox</option>';
							t+='<option value="radio">radio</option>';
							t+='<option value="Textarea">Textarea</option>';
							t+='<option value="datetime">datetime</option>';
							t+='<option value="image">image</option>';
							t+='<option value="hidden">hidden</option>';
							t+='</select>';
						}
						t+='</td>';
						t+='</tr>'; 
					}); 
				t+='</table>';
				t+='</div>';
				$(t).draggable().appendTo("#mainform");
            });
        } else {
            return false;
        }
    };
})(jQuery);

function popup(message) {
        
    // get the screen height and width
    var maskHeight = $(document).height();
    var maskWidth = $(window).width();
    
    // calculate the values for center alignment
    var dialogTop = (maskHeight/3) - ($('#dialog-box').height());
    var dialogLeft = (maskWidth/2) - ($('#dialog-box').width()/2);
    
    // assign values to the overlay and dialog box
    $('#dialog-overlay').css({height:maskHeight, width:maskWidth}).show();
    $('#dialog-box').css({top:dialogTop, left:dialogLeft}).show();
    
    // display the message
    $('#dialog-message').html(message);
            
}