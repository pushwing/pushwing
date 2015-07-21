<?php
if ($use_username) {
	$username = array(
		'name'	=> 'username',
		'id'	=> 'username',
		'value' => set_value('username'),
		'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
		'size'	=> 30,
	);
}
$nickname = array(
	'name'	=> 'nickname',
	'id'	=> 'nickname',
	'value' => (!empty($sns_nickname)) ? $sns_nickname : set_value('nickname'),
	'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
	'size'	=> 30,
);
$age = array(
	'name'	=> 'age',
	'id'	=> 'age',
	'value' => set_value('age'),
	'maxlength'	=> 4,
	'size'	=> 30,
);
$local = array(
	'name'	=> 'local',
	'id'	=> 'local',
	'value' => set_value('local'),
	'maxlength'	=> 10,
	'size'	=> 30,
);
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> (!empty($sns_email)) ? $sns_email : set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
$phone = array(
	'name'	=> 'phone',
	'id'	=> 'phone',
	'value' => set_value('phone'),
	'maxlength'	=> 20,
	'size'	=> 30,
);
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'value' => set_value('password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$confirm_password = array(
	'name'	=> 'confirm_password',
	'id'	=> 'confirm_password',
	'value' => set_value('confirm_password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8,
);
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php echo form_open($this->uri->uri_string()); ?>
<table>
	<?php if ($use_username) { ?>
	<tr>
		<td><?php echo form_label('Username', $username['id']); ?></td>
		<td><?php echo form_input($username); ?></td>
		<td style="color: red;"><?php echo form_error($username['name']); ?><?php echo isset($errors[$username['name']])?$errors[$username['name']]:''; ?></td>
	</tr>
	<?php } ?>
    <tr>
		<td><?php echo form_label('Nickname', $nickname['id']); ?></td>
		<td><?php echo form_input($nickname); ?></td>
		<td style="color: red;"><?php echo form_error($nickname['name']); ?><?php echo isset($errors[$nickname['name']])?$errors[$nickname['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('연령대', $age['id']); ?></td>
		<td><?php echo form_input($age); ?></td>
		<td style="color: red;"><?php echo form_error($age['name']); ?><?php echo isset($errors[$age['name']])?$errors[$nage['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('지역', $local['id']); ?></td>
		<td><?php echo form_input($local); ?></td>
		<td style="color: red;"><?php echo form_error($local['name']); ?><?php echo isset($errors[$local['name']])?$errors[$local['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Email Address', $email['id']); ?></td>
		<td><?php echo form_input($email); ?></td>
		<td style="color: red;"><?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']])?$errors[$email['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('전화번호', $phone['id']); ?></td>
		<td><?php echo form_input($phone); ?></td>
		<td style="color: red;"><?php echo form_error($phone['name']); ?><?php echo isset($errors[$phone['name']])?$errors[$phone['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Password', $password['id']); ?></td>
		<td><?php echo form_password($password); ?></td>
		<td style="color: red;"><?php echo form_error($password['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Confirm Password', $confirm_password['id']); ?></td>
		<td><?php echo form_password($confirm_password); ?></td>
		<td style="color: red;"><?php echo form_error($confirm_password['name']); ?></td>
	</tr>

	<?php if ($captcha_registration) {
		if ($use_recaptcha) { ?>
	<tr>
		<td colspan="2">
			<div id="recaptcha_image"></div>
		</td>
		<td>
			<a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a>
			<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
			<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="recaptcha_only_if_image">Enter the words above</div>
			<div class="recaptcha_only_if_audio">Enter the numbers you hear</div>
		</td>
		<td><input type="text" id="recaptcha_response_field" name="recaptcha_response_field" /></td>
		<td style="color: red;"><?php echo form_error('recaptcha_response_field'); ?></td>
		<?php echo $recaptcha_html; ?>
	</tr>
	<?php } else { ?>
	<tr>
		<td colspan="3">
			<p>Enter the code exactly as it appears:</p>
			<?php echo $captcha_html; ?>
		</td>
	</tr>
	<tr>
		<td><?php echo form_label('Confirmation Code', $captcha['id']); ?></td>
		<td><?php echo form_input($captcha); ?></td>
		<td style="color: red;"><?php echo form_error($captcha['name']); ?></td>
	</tr>
	<?php }
	} ?>
</table>
<?php echo form_submit('register', 'Register'); ?>
<?php echo form_close(); ?>