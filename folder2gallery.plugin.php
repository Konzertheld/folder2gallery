<?php
/*
 * Folder2gallery Plugin
 */

class Folder2gallery extends Plugin
{
	/**
	 * Run activation routines.
	 */
	public function action_plugin_activation()
	{

	}

	/**
	 * Run deactivation routines.
	 */
	public function action_plugin_deactivation( $file )
	{

	}

	public function action_init()
	{

	}

	public function filter_plugin_config( $actions, $plugin_id )
	{
		if ( $this->plugin_id() == $plugin_id ){
			$actions[]= _t( 'Configure', 'folder2gallery' );
		}
		return $actions;
	}

	public function action_plugin_ui( $plugin_id, $action )
	{
		if ( $this->plugin_id() == $plugin_id ) {
			switch ( $action ) {
				case _t( 'Configure', 'folder2gallery' ):
					$form = new FormUI( 'folder2gallery' );
					
					$form->append( 'submit', 'save', 'Save' );
					$form->on_success( array($this, 'formui_submit' ) );
					$form->out();
					break;
			}
		}
	}

	public function formui_submit( FormUI $form )
	{
		Session::notice( _t( 'Folder2gallery options saved.', 'folder2gallery' ) );
		$form->save();
	}

	public function action_form_publish( FormUI $form, Post $post)
	{
		$fieldset = $form->publish_controls->append('fieldset', 'folder2gallery', _t('Folder2gallery'));

		//$tags_buttons = $tagselector->append('wrapper', 'tags_buttons');
		//$tags_buttons->class = 'container';
		$fieldset->append('static', 'addgallerybutton', '<input type="button" value="'._t('Add gallery').'" id="do_folder2gallery">');
		$fieldset->append('static', 'addgallerygallery', '<input type="text" value="'._t('folder name').'" id="folder2gallery_folder">');
	}
	
	/**
	 * Add the required javascript to the publish page
	 * @param Theme $theme The admin theme instance
	 **/
	public function action_admin_header($theme)
	{
		Stack::add('admin_header_javascript', $this->get_url(true) . 'folder2gallery.js', 'folder2gallery', 'jquery');
		// Add the AJAX callback URL.
		$url = 'folder2gallery.url=\''.URL::get('auth_ajax', array('context' => 'folder2gallery')).'\'';
		Stack::add('admin_header_javascript', $url, 'folder2gallery_url', 'folder2gallery');
	}
	
	 
	/**
	 * Respond to Javascript callbacks
	 * The name of this method is action_auth_ajax_ followed by what you passed to the context parameter above.
	 */
	public function action_auth_ajax_folder2gallery($handler)
	{
		// Get the data that was sent
		$folder = $handler->handler_vars['folder'];
		
		// Get the folder content
		$images = scandir(Site::get_dir('user') . "/files/galleries/$folder");
		$path = Site::get_url('user') . "/files/galleries/$folder";
		
		// Test method
		if(array_search(".small", $images))
		{
			// Subfolder method
			foreach($images as $image)
			{
				if(substr($image,0,1)==".") continue;
				$imagelist["$path/$image"] = "$path/.small/$image";
			}
		}
		
		// Convert list to gallery
		$gallerystring = "";
		foreach($imagelist as $large => $small)
		{
			$gallerystring .= "<a href='$large'><img src='$small' class='f2g_pic'></a>";
		}

		// Wipe anything else that's in the buffer
		ob_end_clean();

		// Send the response
		echo json_encode($gallerystring);
	}
}
?>
