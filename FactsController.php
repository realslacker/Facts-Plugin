<?php
/*
 * Facts Plugin for WolfCMS <http://www.wolfcms.org>
 * Copyright (C) 2011 Shannon Brooks <shannon@brooksworks.com>
 */

//	security measure
if (!defined('IN_CMS')) { exit(); }

class FactsController extends PluginController {

	
	//	INIT **************************************************************************************
	//	*******************************************************************************************

	const VALID_INPUT		= 'id,name,data,url';
	
	const LOG_ERROR					= 3;
	const LOG_WARNING				= 4;
	const LOG_NOTICE				= 5;
	const LOG_INFO					= 6;
	
	public $settings;

	public function __construct() {
		self::__checkPermission();
		$this->setLayout('backend');
		$this->assignToLayout('sidebar', new View(FACTS_PLUGIN_ROOT.'/views/sidebar'));
		$this->__load_settings();
		
	}// Init */
	
	
	//	DISPLAY PAGES *****************************************************************************
	//	*******************************************************************************************
	
	//	redirect index to list of facts
	public function index() {
		self::__checkPermission('facts_view');
		$this->display('facts/views/index', array(
			'facts' => Fact::findAll()
		));
	}//*/

	//	displays the edit page for new record
	public function add() {
		self::__checkPermission('facts_new');
		$this->display('facts/views/edit');
	}//*/
	
	//	displays the edit page for updating record
	public function edit($id=null) {
		self::__checkPermission('facts_edit');
		if (is_null($id)) {
			Flash::set('error',__('No ID specified!'));
			redirect(get_url('plugin/facts'));
		}
		if (!$fact = Fact::findById($id)) {
			Flash::set('error',__('Could not find record!'));
			redirect(get_url('plugin/facts'));
		}
		$fact = (array)$fact;
		$this->display('facts/views/edit',$fact);
	}//*/
	
	
	//	DATA MANIPULATION FUNCTIONS ***************************************************************
	// ********************************************************************************************

	//	delete a record
	public function delete($id=null) {
	
		//	make sure user has rights to delete
		self::__checkPermission('facts_delete');
		
		//	check to make sure ID is set and valid
		if (is_null($id)) {
			$this->__log(__('error encountered deleting fact').'; '.__('ID not specified'),self::LOG_ERROR);
			Flash::set('error',__('No ID specified!'));
			redirect(get_url('plugin/facts'));
		}
		if (!$fact = Fact::findById($id)) {
			$this->__log(__('error encountered deleting fact').'; '.__('could not find fact by ID'),self::LOG_ERROR);
			Flash::set('error',__('Could not find record!'));
			redirect(get_url('plugin/facts'));
		}

		//	delete record from database
		if (!$fact->delete()) {
			$this->__log(__('error encountered deleting fact').'; '.__('could not remove from database'),self::LOG_ERROR);
			Flash::set('error',__('Could not delete record!'));
			redirect(get_url('plugin/facts'));
		}

		//	success!
		$this->__log(__('deleted fact').' "'.$fact->name.'"');
		Flash::set('success',__('Deleted fact successfully!'));
		redirect(get_url('plugin/facts'));

	}//*/
	
	//	update record
	public function update($id=null) {
	
		//	make sure user has rights to edit
		self::__checkPermission('facts_edit');
		
		//	check to make sure ID is set and valid
		if (is_null($id)) {
			$this->__log(__('error encountered updating fact').'; '.__('ID not specified'),self::LOG_ERROR);
			Flash::set('error',__('No ID specified!'));
			redirect(get_url('plugin/facts'));
		}
		if (!$fact = Fact::findById($id)) {
			$this->__log(__('error encountered updating fact').'; '.__('could not find fact by ID'),self::LOG_ERROR);
			Flash::set('error',__('Could not find record!'));
			redirect(get_url('plugin/facts'));
		}
		
		//	retrieve the new values from $_POST
		$input = $this->__validate($_POST);
		$input['updated'] = date('Y-m-d H:i:s');
		
		//	update the record with the new values
		foreach ($input as $key => $value) $fact->$key = $value;
		if (!$fact->save()) {
			$this->__log(__('error encountered updating fact'),self::LOG_ERROR);
			Flash::set('error',__('Could not update the record in the database.'));
			redirect(get_url('plugin/facts/edit/'.$id));
		}
		
		$this->__log(__('updated fact').' "'.$fact->name.'"');
		Flash::set('success',__('Record updated!'));
		redirect(get_url('plugin/facts'));
	}//*/

	//	create record
	public function create() {
	
		//	make sure user has rights to create
		self::__checkPermission('facts_new');
		
		//	get the validated input
		$input = $this->__validate($_POST);
		
		//	set the created date
		$input['created'] = date('Y-m-d H:i:s');
		$input['updated'] = date('Y-m-d H:i:s');
		
		//	save the new record
		$fact = new Fact($input);
		if (!$fact->save()) {
			$this->__log(__('error encountered creating new fact'),self::LOG_ERROR);
			Flash::set('error',__('Could not save record in database!'));
			redirect(get_url('plugin/facts/new'));
		}
		
		//	pat on the back and send back to the list
		$this->__log(__('created new fact').' "'.$fact->name.'"');
		Flash::set('success',__('Record saved!'));
		redirect(get_url('plugin/facts'));

	}//*/
	
	// UTILITY FUNCTIONS **************************************************************************
	// ********************************************************************************************

	//	check that user has permissions
	private static function __checkPermission($permission='facts_view') {
		AuthUser::load();
		if ( ! AuthUser::isLoggedIn()) {
			redirect(get_url('login'));
		}
		if ( ! AuthUser::hasPermission($permission) ) {
			Flash::set('error', __('You do not have permission to access the requested page!'));
			if (! AuthUser::hasPermission('facts_view') ) redirect(get_url());
			else redirect(get_url('plugin/facts'));
		}
	}//*/

	//	log an event (uses dashboard plugin)
	//	default log level is LOG_INFO
	private function __log($message=null,$level=6) {
		Observer::notify('log_event', __('Facts').': :username '.$message.'.', 'facts', $level);
	}//*/

	//	clean invalid keys from input by intersecting the array
	//	against array of valid keys
	private static function __clean($settings,$keys) {
		if (!is_array($settings)) return array();
		$valid = is_array($keys) ? $keys : explode(',',$keys);
		$valid = array_combine($valid,$valid);
		return array_intersect_key($settings, $valid);
	}//*/
	
	//	read settings into $this->settings
	private function __load_settings() {
		if (!$this->settings = Plugin::getAllSettings('facts')) {
			Flash::set('error', __('Unable to retrieve plugin settings.'));
			redirect(get_url('setting'));
			return;
		}
	}//*/
	
	//	validate facts
	private function __validate($input) {
		
		//	remove invalid keys from input array
		$input = $this->__clean($input,self::VALID_INPUT);
		
		//	setup path for redirect
		$redirect = 'plugin/facts/edit'.(isset($input['id']) ? '/'.$input['id'] : '');
		
		//	clean the name and leave just the basics
		if (empty($input['name'])) {
			Flash::set('error',__('Name is Required'));
			redirect(get_url($redirect));
		}
		if (empty($input['data'])) {
			Flash::set('error',__('Data is Required'));
			redirect(get_url($redirect));
		}
		
		//return the array
		return $input;

	}//*/
	
}