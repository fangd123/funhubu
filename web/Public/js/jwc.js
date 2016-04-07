$(function(){
	$('#tool').hide();
	$('#submit').click(function(){
		$('#main').hide();
		$('#tool').show();
		$form = $(this).parent();
		$data = $form.serializeArray();
		for(var index = 0; index < $data.length; index ++){
			if($data[index].value == '' || $data[index].value == null
				|| $data[index].value == undefined){
				alert("请填写完整的信息");
				return false;
			}
		}
		
		var $target = window.location.href;
		$data = $form.serialize();
		$.post($target,$form.serialize(),function(result){
			if(result.status != 'success'){
				alert(result.code);
				location.reload();
				return false;
			}

			$('#main').empty();
			$table = $('<table>').addClass('table table-bordered well');
			for(var i = 0; i < result.data.length; i++){
				$row = $('<tr>');
				for(var j = 0; j < result.data[i].length; j++){
					$row.append($('<td>').html(result.data[i][j]));
				}
				$table.append($row);
			}
			$('#tool').fadeOut();
			$('#main').append($('<div>').addClass('row').append($table));
			$('#main').fadeIn();
		});
	});
});
