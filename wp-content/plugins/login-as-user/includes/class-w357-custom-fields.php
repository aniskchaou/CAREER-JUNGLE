<?php
/* ======================================================
# Login as User WordPress Plugin! - v1.3.0 (Free)
# -------------------------------------------------------
# For WordPress
# Author: Web357
# Copyright (©) 2009-2021 Web357. All rights reserved.
# License: GPLv2 or later, http://www.gnu.org/licenses/gpl-2.0.html
# Website: https:/www.web357.com/
# Demo: https://demo.web357.com/wordpress/login-as-user
# Support: support@web357.com
# Last modified: 09 Jan 2021, 02:18:11
========================================================= */

/**
 * Define the internationalization functionality
 */
class LoginAsUser_fields {

	function textField($args) 
	{ 
		$options = get_option('login_as_user_options');
		$class = (isset($args['_class'])) ? $args['_class'] : '';
		$placeholder = (isset($args['placeholder'])) ? $args['placeholder'] : '';
		$size = (isset($args['size'])) ? $args['size'] : 10;
		$maxlength = (isset($args['maxlength'])) ? $args['maxlength'] : 50;
		$default_value = (isset($args['default_value'])) ? $args['default_value'] : '';
		$desc = (isset($args['desc'])) ? $args['desc'] : '';
		$prefix = (isset($args['prefix'])) ? $args['prefix'] : '';
		?>
		<fieldset><?php echo (!empty($prefix) ? $prefix : ''); ?>
		<input 
			type='text' 
			name='login_as_user_options[<?php echo esc_attr($args['name']); ?>]' 
			id='<?php echo esc_attr($args['label-for']); ?>' 
			class='<?php echo esc_attr($class); ?>' 
			placeholder='<?php echo esc_html__($placeholder); ?>'
			value='<?php echo esc_attr(isset($options[$args['name']]) ? $options[$args['name']] : $default_value); ?>'
			size='<?php echo absint($size); ?>'
			maxlength='<?php echo absint($maxlength); ?>'
			>
		</fieldset>
		<?php if (!empty($desc)): ?>
        <p class="description">
			<?php echo wp_kses( __( $desc, 'login-as-user' ), array( 'strong' => array(), 'br' => array() ) ); ?>
		</p>
		<?php endif; ?>
		<?php
	}

	function imageField($args) 
	{ 
		$options = get_option( 'login_as_user_options' );
		$name = $args['id'];
		$width = $args['width'];
		$height = $args['height'];
		$img_id = $args['img_id'];
		$default_image = '';

		// Set variables
		if ( !empty( $options[$name] ) ) {
			$image_attributes = wp_get_attachment_image_src( $options[$name], array( $width, $height ) );
			$src = $image_attributes[0];
			$value = $options[$name];
		} else {
			$src = $default_image;
			$value = '';
		}
		?>

		<div class="w357-imageField">

			<?php if (!empty($src)): ?>
					<img data-src="<?php echo esc_url($default_image); ?>" src="<?php echo esc_url($src); ?>" width="<?php echo absint($width); ?>px" height="<?php echo absint($height); ?>px" />		
			<?php else: ?>
				<img data-src="<?php echo esc_url($default_image); ?>" src="<?php echo esc_url($src); ?>" width="<?php echo absint($width); ?>px" height="<?php echo absint($height); ?>px" style="display:none" />		
			<?php endif; ?>

			<div>
				<input type="hidden" name="login_as_user_options[<?php echo $name; ?>]" id="login_as_user_options[<?php echo $name; ?>]" value="<?php echo esc_attr($value); ?>" />
				<button type="submit" class="upload_image_button button">Upload image</button>

				<?php if (!empty($src)): ?>
					<button type="submit" class="remove_image_button button">&times;</button>
				<?php else: ?>
					<button type="submit" class="remove_image_button button" style="display:none">&times;</button>
				<?php endif; ?>

			</div>
		</div>
		
		<?php
	}

	function hiddenField($args) 
	{ 
		$options = get_option('login_as_user_options');
		$default_value = (isset($args['default_value'])) ? $args['default_value'] : '';
		?>
		<input 
			type='hidden' 
			name='login_as_user_options[<?php echo esc_attr($args['name']); ?>]' 
			value='<?php echo esc_attr(isset($options[$args['name']]) ? $options[$args['name']] : $default_value); ?>'
			>
		<?php
	}

	function textareaWordpressEditorField($args) 
	{ 
		$options = get_option('login_as_user_options');
	    $editor_id = $args['name']; 
		$class = (isset($args['_class'])) ? $args['_class'] : '';
		$editor_settings = array('textarea_name' => 'login_as_user_options['.$args['name'].']', 'editor_class' => $class);
		$default_value = (isset($args['default_value'])) ? $args['default_value'] : '';
		$content = (isset($options[$args['name']])) ? $options[$args['name']] : $default_value;
		wp_editor( $content, $editor_id, $editor_settings );
	}

	function textareaField($args) 
	{ 
		$options = get_option('login_as_user_options');
		$class = (isset($args['_class'])) ? $args['_class'] : '';
		$default_value = (isset($args['default_value'])) ? $args['default_value'] : '';
		?>
		
		<textarea 
			id="<?php echo esc_attr($args['name']); ?>" 
			name="login_as_user_options[<?php echo esc_attr($args['name']); ?>]" 
			rows="<?php echo absint($args['rows']); ?>" 
			cols="<?php echo absint($args['cols']); ?>" 
			class="<?php echo esc_attr($class); ?>"
			placeholder="<?php echo esc_html__($args['placeholder']); ?>"><?php echo esc_textarea(isset($options[$args['name']]) && !empty($options[$args['name']]) ? $options[$args['name']] : $default_value); ?></textarea>
		<?php
	}

	function selectField($args)
	{ 
		$name = $args['id'];
		$default_value = $args['default_value'];
		$select_options = $args['options'];
		$options = get_option('login_as_user_options');
		$desc = (isset($args['desc'])) ? $args['desc'] : '';
		?>
		<select name="login_as_user_options[<?php echo $name; ?>]">

		<?php for ($i=0;$i<count($select_options);$i++): ?>

			<option value="<?php echo esc_attr($select_options[$i]['value']); ?>" <?php echo (($select_options[$i]['value'] == (isset($options[$name]) ? $options[$name] : $default_value) ) ? 'selected' : ''); ?>><?php echo $select_options[$i]['label']; ?></option>

		<?php endfor; ?>
		</select>
		<?php if (!empty($desc)): ?>
        <p class="description">
			<?php echo wp_kses( __( $desc, 'login-as-user' ), array( 'strong' => array(), 'br' => array() ) ); ?>
		</p>
		<?php endif; ?>
		<?php
	}

	function radioField($args)
	{ 
		$name = $args['id'];
		$default_value = $args['default_value'];
		$radio_options = $args['options'];
		$field_description = (isset($args['field_description'])) ? $args['field_description'] : '';
		$options = get_option('login_as_user_options');

		for ($i=0;$i<count($radio_options);$i++): ?>

			<input 
				type='radio' 
				id='<?php echo $radio_options[$i]['id']; ?>' 
				name='login_as_user_options[<?php echo $name; ?>]' 
				value='<?php echo esc_attr($radio_options[$i]['value']); ?>'
				<?php if ( $radio_options[$i]['value'] == (isset($options[$name]) ? $options[$name] : $default_value) ) echo 'checked="checked"'; ?>
			>
			<label for="<?php echo $radio_options[$i]['id']; ?>" style="margin-right: 10px;"><?php echo $radio_options[$i]['label']; ?></label>

		<?php endfor; ?>

		<?php if (!empty($field_description)): ?>
			<div class="w357_settings_field_description"><?php echo $field_description; ?></div>
		<?php endif; ?>
		<?php
	}

	function checkboxField($args)
	{
		$name = $args['id'];
		$default_value = $args['default_value'];
		$ckeckbox_options = $args['options'];
		$field_description = (isset($args['field_description'])) ? $args['field_description'] : '';
		$options = get_option('login_as_user_options');

		for ($i=0;$i<count($ckeckbox_options);$i++):
		?>

			<input 
				type='checkbox' 
				id='<?php echo $ckeckbox_options[$i]['id']; ?>' 
				name='login_as_user_options[<?php echo $name; ?>][]' 
				value='<?php echo esc_attr($ckeckbox_options[$i]['value']); ?>'
				<?php if (in_array($ckeckbox_options[$i]['value'], (isset($options[$name]) ? $options[$name] : $default_value))) echo 'checked="checked"'; ?>
			>
			<label for="<?php echo $ckeckbox_options[$i]['id']; ?>" style="margin-right: 10px;"><?php echo $ckeckbox_options[$i]['label']; ?></label>

		<?php endfor; ?>

		<?php if (!empty($field_description)): ?>
			<div class="w357_settings_field_description"><?php echo $field_description; ?></div>
		<?php endif; ?>
		<?php
	}
}