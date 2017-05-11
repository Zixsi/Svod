<?=Modules::run('template_mod/header', '', $arResult)?>
	<div class="reloader_page">
		<div class="loader"></div>
		<div class="info">Не закрывайте и не перезагружайте эту страницу</div>
		<form method="post" id="postform" action="/">
			<input type="hidden" name="go" value="Y"/>
		</form>
		<script>window.setTimeout("document.getElementById('postform').submit()",500);</script>
	</div>
<?=Modules::run('template_mod/footer', '', $arResult)?>