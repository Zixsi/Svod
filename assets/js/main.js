$(document).ready(function(){
	
	$('.report_form_block').on('click', '.btn_select_file', function(){
		$(this).parent().find('input[type="file"]').click();
	})
	
	$('.report_form_block input[type="file"]').on('change', function() {
		var target = $(this).get(0);
		if(target.files && target.files[0])
		{
			var filename = target.files[0].name;
			var tmpext = filename.split('.'); 
			var ext = tmpext[tmpext.length - 1];
			if(ext == 'csv')
			{
				$(this).parent().find('.btn_select_file').text(filename);
			}
			else
			{
				$(this).empty();
				alert('Файл должен иметь расширение *.csv');
			}
		}
	})
	
	$('.report_form_block').on('click', '.btn_send', function(){
		$('#load_file').modal({
			keyboard: false,
		})
	})
})
