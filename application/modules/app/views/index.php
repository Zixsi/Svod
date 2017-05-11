<?=Modules::run('template_mod/header', '', $arResult)?>
<?//Debug::Log($arResult);?>
<div class="report_form_block">
	<div class="info">Загрузите файл в формате CSV</div>
	<?if(!empty($arResult['error'])):?>
		<div class="alert alert-danger"><?=$arResult['error']?></div>
	<?endif;?>
	<form action="" method="post" enctype="multipart/form-data" class="form-inline" role="form">
		<input type="hidden" name="<?=$arResult['secret_key']['key']?>" value="<?=$arResult['secret_key']['value']?>">
		<div class="form-group file_wrap">
			<input type="file" name="file">
			<button type="button" class="btn btn-primary btn_select_file">Выберете файл</button>
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary btn_send">Отправить</button>
		</div>
		<div class="clearfix"></div>
	</form>
	
	<div class="modal fade" id="load_file" tabindex="-1" role="dialog" aria-hidden="false" data-backdrop="static">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-body">
					<div class="info">Загрузка файла...</div>
					<div class="loader"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<?if(count($arResult['list'])):?>
	<table class="table table-condensed">
		<thead>
			<tr>
				<th>Название</th>
				<th class="col-xs-3">Дата</th>
			</tr>
		</thead>
		<tbody>
			<?foreach($arResult['list'] as $item):?>
				<tr>
					<td><a href="/<?=$item['FILE']?>" target="_blank"><?=$item['NAME']?></a></td>
					<td><?=date('d.m.Y H:i:s', strtotime($item['DATE']))?></td>
				</tr>
			<?endforeach;?>
		</tbody>
	</table>
<?endif;?>
<?=Modules::run('template_mod/footer', '', $arResult)?>