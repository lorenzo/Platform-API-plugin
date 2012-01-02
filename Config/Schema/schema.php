<?php 
/* generated on: 2011-12-22 11:59:04 : 1324551544 */
class ApiSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $acos = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'parent_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'foreign_key' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'unique', 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'collate' => NULL, 'comment' => ''),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'collate' => NULL, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'alias' => array('column' => 'alias', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_bin', 'engine' => 'InnoDB')
	);
	public $aros = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'parent_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'foreign_key' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'unique', 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'collate' => NULL, 'comment' => ''),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'collate' => NULL, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'alias' => array('column' => 'alias', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_bin', 'engine' => 'InnoDB')
	);
	public $aros_acos = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'aro_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'aco_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_bin', 'comment' => '', 'charset' => 'utf8'),
		'_create' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => NULL, 'comment' => ''),
		'_read' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => NULL, 'comment' => ''),
		'_update' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => NULL, 'comment' => ''),
		'_delete' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => NULL, 'comment' => ''),
		'_admin' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => NULL, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'ARO_ACO_KEY' => array('column' => array('aro_id', 'aco_id'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_bin', 'engine' => 'InnoDB')
	);
}
