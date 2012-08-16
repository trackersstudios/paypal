<form name="settings_update" id="settings_update" method="post" action="<?= base_url() ?>api/settings/modify" enctype="multipart/form-data">
<div class="content_wrap_inner">

	<div class="content_inner_top_right">
		<h3>App</h3>
		<p><?= form_dropdown('enabled', config_item('enable_disable'), $settings['paypal']['enabled']) ?></p>
		<p><a href="<?= base_url() ?>api/<?= $this_module ?>/uninstall" id="app_uninstall" class="button_delete">Uninstall</a></p>
	</div>


    <h3>Account Info</h3>

    <p>Sandboxed
    	<?= form_dropdown('sandbox', config_item('yes_or_no'), $settings['paypal']['sandbox']) ?>
    </p>

    <p>Username
    	<input type="text" name="username" value="<?= $settings['paypal']['username'] ?>">
    </p>

    <p>Password
    	<input type="text" name="password" value="<?= $settings['paypal']['password'] ?>">
    </p>
    
    <p>API Signature
    	<input type="text" name="signature" value="<?= $settings['paypal']['signature'] ?>">
    </p>

    <p>Application ID
    	<input type="text" name="application_id" value="<?= $settings['paypal']['application_id'] ?>">
    </p>

    <p>Developer Email
    	<input type="text" name="account_email" value="<?= $settings['paypal']['account_email'] ?>">
    </p>
    

	<input type="hidden" name="module" value="<?= $this_module ?>">
	<p><input type="submit" name="save" value="Save" /></p>
	

</div>
</form>

<?= $shared_ajax ?>