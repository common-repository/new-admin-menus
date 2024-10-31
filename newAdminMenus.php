<?php
/*
Plugin Name: New Admin Menus
Plugin URI: http://webkoof.com/wordpress-plugins/new-admin-menus-change-your-menus-in-wordpress/
Description: Admin can change all the menu item labels
Version:  1.0
Author: Vikash Kumar
Author URI: http://vika.sh
*/

/*  Copyright 2010  Vikash Kumar  (email : vikash.iitb@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


error_reporting(0);
// create custom plugin settings menu
add_action('admin_menu', 'newAdminMenus_create_menu');
add_action( 'admin_menu', 'newAdminMenus_change_menu_label' );
add_action( 'init', 'newAdminMenus_change_post_label' ); 

function newAdminMenus_change_menu_label(){
	global $menu, $submenu, $previous;
	$previous['menu'] = $menu;
	$previous['submenu'] = $submenu;
	
	foreach ($menu as $key=>$val) {
		if ($val[0]!= "") {
			$optionName = "Menu".$key;
			$newName = get_option($optionName);
			if ($newName=='HIDE') unset($menu[$key]);
			elseif (strlen(trim($newName)) == 0) {}
			else $menu[$key][0] = $newName;
			
			$subMenuKey = $val[2];
			$submenus = $submenu[$subMenuKey];
			foreach ($submenus as $k=>$m) {
					$subOptionName = "SubMenu-$key-$k";
					$newSubName = get_option($subOptionName);
					if (strlen(trim($newSubName)) == 0) {}
					else $submenu[$subMenuKey][$k][0] = $newSubName;
			}
			
		}
	}
	
	$l = get_option('newPostLabel');
	if ($l!="") {
		$menu[5][0] = $l;
		$submenu['edit.php'][5][0] = $l;
		$submenu['edit.php'][10][0] = "Add $l";
		$submenu['edit.php'][16][0] = "$l Tags";
	}
		

}

function newAdminMenus_change_post_label() {
	global $wp_post_types;
	$labels = &$wp_post_types['post']->labels;
	$l = get_option('newPostLabel');
	if ($l=="") $l = "Post";
	$labels->name = $l;
	$labels->singular_name = $l;
	$labels->add_new = "Add $l";
	$labels->add_new_item = "Add $l";
	$labels->edit_item = "Edit $l";
	$labels->new_item = $l;
	$labels->view_item = "View $l";
	$labels->search_items = "Search $l";
	$labels->not_found = "No $l found";
	$labels->not_found_in_trash = "No $l found in Trash";
}

function newAdminMenus_create_menu() {

	//create new top-level menu
	add_menu_page('New Admin Menus', 'Admin Menus', 'administrator', 'newAdminMenus', 'newAdminMenus_settings_page');
	//add_submenu_page( 'newAdminMenus', 'Change Post Label', 'Change Post Labels', 'administrator', 'change_post_label', 'newAdminMenus_post_label');
	
	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function newAdminMenus_post_label() {
?>
	<div class="wrap">
	<h2>Change "Post" Label:</h2>
	<div style="padding: 20px; background-color: #eee;">
<i> Note: </i> This will override the settings above. If you want to change your "Post" and want to call it "Article", simply write "Article" below and save. This will not only change the admin menus but it will also change the buttons/text on "All Posts" Page.
</div>
	
	<form method="post" action="options.php">
	<?php settings_fields( 'newAdminMenus-postLabel' ); ?>
	<table class="form-table" style="width:500px;">
	<tr valign="top">
        <th scope="row">Change "Post" to: </th>
		<?php $optionName = "newPostLabel" ?>
        <td><input type="text" name="<?php echo $optionName?>" value="<?php echo get_option($optionName); ?>" /></td>
     </tr>
	  </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php } 

function register_mysettings() {
	//register our settings
	global $previous;
	$menu = $previous['menu'];
	$submenu = $previous['submenu'];
	foreach ($menu as $key=>$val) {
		if ($val[0]!= "") {
			$optionName = "Menu".$key;
			register_setting( 'newAdminMenus-settings', $optionName);
			
			$subMenuKey = $val[2];
			$submenus = $submenu[$subMenuKey];
			foreach ($submenus as $k=>$m) {
					$subOptionName = "SubMenu-$key-$k";
					register_setting( 'newAdminMenus-settings', $subOptionName);
			}
		}
	}
	register_setting( 'newAdminMenus-postLabel', 'newPostLabel');
	
}

function newAdminMenus_settings_page() {

?>
<div class="wrap">
<h2>New Admin Menus</h2>

<div style="padding: 20px; background-color: #eee;">
<i> Note: </i> If you want to hide any one of the menus, change its name to <b>HIDE</b>
</div>

<form method="post" action="options.php">
<table class="form-table" style="width:500px;">
	<tr valign="top">
        <th style="width:200px"><strong>Old Menu Name</strong></th>
        <th style="width:200px"><strong>New Menu Name</strong></th>
    </tr>

    <?php settings_fields( 'newAdminMenus-settings' ); ?>
	<?php
	
		global $previous;	
		$menu = $previous['menu'];
		$submenu = $previous['submenu'];
		echo "<pre>";
		//print_r($menu);
		//print_r($submenu);
		
		$n=0;
		foreach ($menu as $key=>$val) {
			if ($val[0]!= "") {
				//echo "Menu".$key." = ". $val[0];
				$optionName = "Menu".$key;
				$prevName = $val[0];
				
				$subMenuKey = $val[2];
				$submenus = $submenu[$subMenuKey];
	?>
   
				<tr valign="top">
				<th scope="row" ><strong><?php echo $prevName?></strong></th>
				<td><input type="text" name="<?php echo $optionName?>" value="<?php echo get_option($optionName); ?>" /></td>
				</tr>
		
		<?php 
				foreach ($submenus as $k=>$m) {
					$prevName = $m[0];
					$subOptionName = "SubMenu-$key-$k";
		?>
					<tr valign="top">
					<th scope="row" style="padding-left: 40px;"><?php echo $prevName?></th>
					<td><input type="text" name="<?php echo $subOptionName?>" value="<?php echo get_option($subOptionName); ?>" /></td>
		
					</tr>
		<?php
				}
		
			} 
		} ?>
         
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php 


newAdminMenus_post_label();

} ?>
