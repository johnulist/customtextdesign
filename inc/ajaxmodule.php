<?php
	/**
	* 2010-2014 Tuni-Soft
	*
	* NOTICE OF LICENSE
	*
	* This source file is subject to the Academic Free License (AFL 3.0)
	* It is available through the world-wide-web at this URL:
	* http://opensource.org/licenses/afl-3.0.php
	* If you did not receive a copy of the license and are unable to
	* obtain it through the world-wide-web, please send an email
	* to tunisoft.solutions@gmail.com so we can send you a copy immediately.
	*
	* DISCLAIMER
	*
	* Do not edit or add to this file if you wish to upgrade this module to newer
	* versions in the future. If you wish to customize the module for your
	* needs please refer to
	* http://doc.prestashop.com/display/PS15/Overriding+default+behaviors
	* for more information.
	*
	* @author    Tunis-Soft <tunisoft.solutions@gmail.com>
	* @copyright 2010-2014 Tuni-Soft
	* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
	*/

	include_once('../../../config/config.inc.php');

	$context = Context::getContext();

	$cookie = new Cookie('psAdmin');
	$token = Tools::getValue('token');
	$tab = Tools::getValue('tab');
	$context->employee = new Employee($cookie->id_employee);
	$admin_token = Tools::getAdminTokenLite($tab);

	$id_current = (int)Tools::getValue('id_product', 0);
	$restricted = array();
	if ($context->employee->id_profile != 1 && in_array($id_current, $restricted))
	{
		exit(Tools::jsonEncode(array(
			'success' => 0
		)));
	}

	if ($token != $admin_token)
	{
		exit(Tools::jsonEncode(array(
			'authorized' => 0
		)));
	}

	/** @var customtextdesign */
	$module = Module::getInstanceByName('customtextdesign');
	if (! $module->checkAdmin() || ! Tools::isSubmit('secure_key')
		|| Tools::getValue('secure_key') != $module->secure_key || ! Tools::isSubmit('action'))
	{
		exit(Tools::jsonEncode(array(
			'authorized' => 0
		)));
	}

	$action = Tools::getValue('action');

	if ($action == 'dnd')
	{
		if (Tools::getIsset('colors_table') && $table = Tools::getValue('colors_table'))
		{
			$pos = 0;
			if (is_array($table))
			{
				foreach ($table as $key => $row)
				{
					$ids = explode('_', $row);
					Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.$module->name.'_color` SET `position` = '.(int)$pos.' WHERE `id` = '.(int)$ids[1]);
					$pos++;
				}
			}
		}

		if (Tools::getIsset('fonts_table') && $table = Tools::getValue('fonts_table'))
		{
			$pos = 0;
			if (is_array($table))
			{
				foreach ($table as $key => $row)
				{
					$ids = explode('_', $row);
					Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.$module->name.'_font` SET `position` = '.(int)$pos.' WHERE `id` = '.(int)$ids[1]);
					$pos++;
				}
			}
		}

		if (Tools::getIsset('materials_table') && $table = Tools::getValue('materials_table'))
		{
			$pos = 0;
			if (is_array($table))
			{
				foreach ($table as $key => $row)
				{
					$ids = explode('_', $row);
					Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.$module->name.'_material` SET `position` = '.(int)$pos.' WHERE `id` = '.(int)$ids[1]);
					$pos++;
				}
			}
		}

		if (Tools::getIsset('groups_table') && $table = Tools::getValue('groups_table'))
		{
			$pos = 0;
			if (is_array($table))
			{
				foreach ($table as $key => $row)
				{
					$ids = explode('_', $row);
					Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.$module->name.'_group` SET `position` = '.(int)$pos.' WHERE `id` = '.(int)$ids[1]);
					$pos++;
				}
			}
		}

		if (Tools::getIsset('images_table') && $table = Tools::getValue('images_table'))
		{
			$pos = 0;
			if (is_array($table))
			{
				foreach ($table as $key => $row)
				{
					$ids = explode('_', $row);
					Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.$module->name.'_image` SET `position` = '.(int)$pos.' WHERE `id` = '.(int)$ids[1]);
					$pos++;
				}
			}
		}
	}

	if ($action == 'save_product_options')
	{
		$name = Tools::getValue('name');
		$value = Tools::getValue('value');
		$type = Tools::getValue('type');
		$id_product = (int)Tools::getValue('id_product');

		if ($name == 'active')
			$module->addCustomField($id_product);

		$data = array();
		$data[$name] = $value;
		$data['id_product'] = $id_product;

		$table = $module->name.'_product';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$id_product);
		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->update($table, $data, 'id_product = '.(int)$id_product);
		else
			Db::getInstance()->insert($table, $data);

		if ($name == 'required' && (int)$id_product)
			$module->addCustomField($id_product, true);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));

	}

	if ($action == 'add_new_panel')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		Db::getInstance()->insert($module->name.'_panels', $data);
		exit(Tools::jsonEncode(array(
			'success' => 1,
			'id_panel' => Db::getInstance()->Insert_ID()
		)));
	}

	if ($action == 'remove_panel')
	{
		$id_panel = (int)Tools::getValue('id_panel');
		Db::getInstance()->delete($module->name.'_panels', 'id_panel = '.$id_panel);
		Db::getInstance()->delete($module->name.'_product_trans', 'id_product_trans = '.$id_panel);
		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'update_panel')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		Db::getInstance()->insert($module->name.'_panels', $data);
		exit(Tools::jsonEncode(array(
			'success' => 1,
			'id_panel' => Db::getInstance()->Insert_ID()
		)));
	}

	if ($action == 'save_lang')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$data['id_lang'] = (int)Tools::getValue('id_lang');
		$name = Tools::getValue('name');
		if ($name == 'cflabel')
		{
			$data['label'] = Tools::getValue('value');
			$data['id_custom_field'] = (int)Tools::getValue('id_custom_field');

			$table = $module->name.'_custom_field';
			$sql = new DbQuery();
			$sql->from($table);
			$sql->where('id = "'.$data['id_custom_field'].'"');
			if ($custom_field = Db::getInstance()->getRow($sql))
			{
				$data['id_custom_field'] = (int)$custom_field['id_custom_field'];
				$table = $module->name.'_custom_field_trans';
				$sql = new DbQuery();
				$sql->from($table);
				$sql->where('id_custom_field = '.(int)$data['id_custom_field'].' AND id_lang = '.(int)$data['id_lang']);

				if (Db::getInstance()->getRow($sql))
					Db::getInstance()->update($table, $data, 'id_custom_field = '.(int)$data['id_custom_field'].' AND id_lang = '.$data['id_lang']);
				else
					Db::getInstance()->insert($table, $data);
			}
			exit(Tools::jsonEncode(array(
				'success' => 1
			)));
		}

		$data[$name] = pSQL(Tools::getValue('value'));

		$table = $module->name.'_product_trans';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_lang = '.(int)$data['id_lang']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->update($table, $data, 'id_product = '.$data['id_product'].' AND id_lang = '.$data['id_lang']);
		else
			Db::getInstance()->insert($table, $data);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'save_size')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$data['id_image'] = (int)Tools::getValue('id_image');
		$data['size'] = (float)Tools::getValue('size');
		$data['width'] = (float)Tools::getValue('width');
		$data['x'] = (int)Tools::getValue('x');
		$data['y'] = (int)Tools::getValue('y');
		$data['x_origin'] = (int)Tools::getValue('x_origin');
		$data['y_origin'] = (int)Tools::getValue('y_origin');
		$table = $module->name.'_measure';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->update($table, $data, 'id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);
		else
			Db::getInstance()->insert($table, $data);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'copy_size')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$id_image_bkp = (int)Tools::getValue('id_image');
		$data['id_image'] = $id_image_bkp;
		$data['size'] = (float)Tools::getValue('size');
		$data['width'] = (float)Tools::getValue('width');
		$data['x'] = (int)Tools::getValue('x');
		$data['y'] = (int)Tools::getValue('y');
		$data['x_origin'] = (int)Tools::getValue('x_origin');
		$data['y_origin'] = (int)Tools::getValue('y_origin');
		$table = $module->name.'_measure';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->update($table, $data, 'id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);
		else
			Db::getInstance()->insert($table, $data);

		$ids_str = Tools::getValue('ids');
		$ids = explode(',', $ids_str);
		foreach ($ids as $id_image)
		{
			if (! (int)$id_image || $id_image == $id_image_bkp)
				continue;
			$data['id_image'] = $id_image;
			Db::getInstance()->insert($table, $data);
		}
		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'save_cf')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$data['id_image'] = (int)Tools::getValue('id_image');
		$data['x'] = (int)Tools::getValue('x');
		$data['y'] = (int)Tools::getValue('y');
		$data['w'] = (int)Tools::getValue('w');
		$data['h'] = (int)Tools::getValue('h');
		$data['id'] = (int)Tools::getValue('id');

		$table = $module->name.'_custom_field';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where("id = '".$data['id']."' AND id_image = ".(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->update($table, $data, "id = '".$data['id']."' AND id_image = ".(int)$data['id_image']);
		else
			Db::getInstance()->insert($table, $data);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'copy_cf')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$id_image_bkp = (int)Tools::getValue('id_image');
		$data['id_image'] = $id_image_bkp;
		$data['size'] = (float)Tools::getValue('size');
		$data['width'] = (float)Tools::getValue('width');
		$data['x'] = (int)Tools::getValue('x');
		$data['y'] = (int)Tools::getValue('y');
		$data['x_origin'] = (int)Tools::getValue('x_origin');
		$data['y_origin'] = (int)Tools::getValue('y_origin');
		$table = $module->name.'_measure';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->update($table, $data, 'id_product = '.(int)$data['id_product'].' AND	id_image = '.(int)$data['id_image']);
		else
			Db::getInstance()->insert($table, $data);

		$ids_str = Tools::getValue('ids');
		$ids = explode(',', $ids_str);
		foreach ($ids as $id_image)
		{
			if (!(int)$id_image || $id_image == $id_image_bkp)
				continue;
			$data['id_image'] = $id_image;
			Db::getInstance()->insert($table, $data);
		}
		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'delete_cf')
	{
		$id = (int)Tools::getValue('id');
		$table = $module->name.'_custom_field';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where("id = '".$id."'");

		if ($custom_field = Db::getInstance()->getRow($sql))
		{
			$del = Db::getInstance()->delete($table, "id = '".$id."'");
			$id_custom_field = $custom_field['id_custom_field'];
			$table = $module->name.'_custom_field_trans';
			$del = Db::getInstance()->delete($table, 'id_custom_field = '.(int)$id_custom_field);
		}

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'save_overlay')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$id_image_bkp = (int)Tools::getValue('id_image');
		$data['id_image'] = $id_image_bkp;
		$data['image'] = Tools::getValue('image');

		$table = $module->name.'_overlay';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->update($table, $data, 'id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);
		else
			Db::getInstance()->insert($table, $data);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'delete_overlay')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$id_image_bkp = (int)Tools::getValue('id_image');
		$data['id_image'] = $id_image_bkp;

		$table = $module->name.'_overlay';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->delete($table, 'id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'save_mask')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$id_image_bkp = (int)Tools::getValue('id_image');
		$data['id_image'] = $id_image_bkp;
		$data['image'] = Tools::getValue('image');

		$table = $module->name.'_mask';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->update($table, $data, 'id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);
		else
			Db::getInstance()->insert($table, $data);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'delete_mask')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$id_image_bkp = (int)Tools::getValue('id_image');
		$data['id_image'] = $id_image_bkp;

		$table = $module->name.'_mask';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->delete($table, 'id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'save_mask2')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$id_image_bkp = (int)Tools::getValue('id_image');
		$data['id_image'] = $id_image_bkp;
		$data['image'] = Tools::getValue('image');

		$table = $module->name.'_mask2';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->update($table, $data, 'id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);
		else
			Db::getInstance()->insert($table, $data);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'delete_mask2')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$id_image_bkp = (int)Tools::getValue('id_image');
		$data['id_image'] = $id_image_bkp;

		$table = $module->name.'_mask2';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->delete($table, 'id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'save_replace')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$id_image_bkp = (int)Tools::getValue('id_image');
		$data['id_image'] = $id_image_bkp;
		$data['image'] = Tools::getValue('image');

		$table = $module->name.'_replace';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->update($table, $data, 'id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);
		else
			Db::getInstance()->insert($table, $data);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

	if ($action == 'delete_replace')
	{
		$data = array();
		$data['id_product'] = (int)Tools::getValue('id_product');
		$id_image_bkp = (int)Tools::getValue('id_image');
		$data['id_image'] = $id_image_bkp;

		$table = $module->name.'_replace';
		$sql = new DbQuery();
		$sql->from($table);
		$sql->where('id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		if (Db::getInstance()->getRow($sql))
			Db::getInstance()->delete($table, 'id_product = '.(int)$data['id_product'].' AND id_image = '.(int)$data['id_image']);

		exit(Tools::jsonEncode(array(
			'success' => 1
		)));
	}

?>
