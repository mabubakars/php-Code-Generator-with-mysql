(document).ready(function(){
	$('.get').live('click',function(){
		$id = $(this).attr('id');
	})
})

function generateHTML(index,val,counter){
	if(photo != ""){
		image = val.photo;
	}else{
		image = 'th.jpg';
	}
	var output = [];
	output.push('<div class="picscontent'+val.counter+'">');
	output.push('<div class="prodpic">');
	output.push('<img src="images/'+val.image+'" height="127" width="100" /></div>');
	output.push('<div class="prodtext">');
	output.push('<h2>'+val.title+'</h2>');
	output.push('<p>This is a great new product. On sale this week only.<br>');
	output.push('<strong>Autor:'+val.author+'</strong><br>');
	output.push('<strong>Price:'+val.price+'</strong>');
	output.push('</p>');
	output.push('<a href="cart.php?action=add&id='+val.id+'">');
	output.push('<span style="float:right);">');
	output.push('<img src="images/littlecart1.png" alt="Add to cart" height="20" width="20">');
	output.push('</span></a>');
	output.push('</div>');
	output.push('</div>');
	return output;
}