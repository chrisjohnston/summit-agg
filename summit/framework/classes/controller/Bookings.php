<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Booking Analytics
 *
 * @author Scott Darby
 */
class Bookings extends Page_controller {

	public function __construct()
	{
		parent::__construct();

		//set up main view
		$this->_master_page = new Fl_View('masterpages/masterpage');
		
		//load booking model
		$this->booking_model = new Booking_model();
	}

	// ------------------------------------------------------------------------

	/**
	 * Index
	 */
	public function action_index($params, $test)
	{
		$view = new Fl_View('bookings/index');

		//get all bookings
		$view->bind('bookings', $this->booking_model->get_all());

		//bind view output to master page
		$content = $view->get_output();
		$this->_master_page->bind('content', $content);
		
		//page title
		$this->_master_page->bind('page_title', 'Bookings');

		//display page
		$this->_master_page->render_output();
	}

}