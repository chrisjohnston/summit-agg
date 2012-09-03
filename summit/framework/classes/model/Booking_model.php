<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Booking model
 *
 * @author Scott Darby
 */
Class Booking_model {

	public function get_all()
	{
		$result = Fl_DB::select("SELECT
								 wp_booking.*,
								 wp_bookingdates.*,
								 wp_bookingtypes.*
								 FROM wp_booking
								 LEFT JOIN wp_bookingdates
								 ON wp_bookingdates.booking_id = wp_booking.booking_id
								 LEFT JOIN wp_bookingtypes
								 ON wp_bookingtypes.booking_type_id = wp_booking.booking_type
								 ORDER BY wp_bookingdates.booking_date ASC
								 LIMIT 200");
		return Fl_DB::get_result_array($result);
	}

}
