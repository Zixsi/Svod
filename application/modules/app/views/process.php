<?=Modules::run('template_mod/header', '', $arResult)?>
	<div class="reloader_page">
		<div class="loader"></div>
		<div class="info">На сервере уже запущен процесс обработки. Дождитесь завершения.</div>
		<form method="post" id="postform" action="/">
			<input type="hidden" name="go" value="Y"/>
		</form>
		<script>window.setTimeout("document.getElementById('postform').submit()",5000);</script>
	</div>
<?=Modules::run('template_mod/footer', '', $arResult)?>