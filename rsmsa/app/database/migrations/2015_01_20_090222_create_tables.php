<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('rsmsa_persons', function ($table) {
            $table->increments('id');
            $table->string('first_name', 128);
            $table->string('last_name');
            $table->string('address');
            $table->string('gender');
            $table->date('birthdate');
            $table->string('phone_number')->unique();
            $table->string('email')->unique();
            $table->timestamps();
        });


        Schema::create('rsmsa_users', function ($table) {
            $table->increments('id');
            $table->string('username', 128)->unique();
            $table->string('password', 60);
            $table->string('level', 128);
            $table->string('remember_token', 100)->nullable();
            $table->integer('person_id')->unsigned();
            $table->foreign('person_id')->references('id')->on('rsmsa_persons')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('rsmsa_stations', function ($table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->integer('district_id');
            $table->integer('region_id');
            $table->timestamps();
        });
        Schema::create('rsmsa_police', function ($table) {
            $table->increments('id');
            $table->string('rank_no')->unique();
            $table->integer('station_id')->unsigned();
            $table->foreign('station_id')->references('id')->on('rsmsa_stations')->onDelete('cascade');
            $table->integer('person_id')->unsigned();
            $table->foreign('person_id')->references('id')->on('rsmsa_persons')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('rsmsa_vehicles', function ($table) {
            $table->increments('id');
            $table->string('vehicle_control_number');
            $table->string('plate_number');
            $table->string('owner_id');
            $table->string('owner_name');
            $table->string('owner_nationality');
            $table->string('ownership_category');
            $table->string('owner_physical_address');
            $table->string('owner_address');
            $table->string('owner_phone_number');
            $table->string('make');
            $table->string('type');
            $table->string('body_type');
            $table->string('model_number');
            $table->string('class');
            $table->string('engine_number');
            $table->string('engine_capacity');
            $table->string('fuel');
            $table->string('number_of_axial');
            $table->string('axial_distance');
            $table->string('seating_capacity');
            $table->string('color');
            $table->string('yom');
            $table->string('chasis_no');
            $table->string('tare_weight');
            $table->string('gross_wheight');
            $table->string('imported_from');
        });
        Schema::create('rsmsa_drivers', function ($table) {
            $table->increments('id');
            $table->string('license_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('physical_address');
            $table->string('address');
            $table->string('national_id');
            $table->string('gender');
            $table->string('birthdate');
            $table->string('nationality');
            $table->string('phone_number');
            $table->string('occupation');
            $table->string('driving_class');
            $table->string('expiry_date');
        });
        Schema::create('rsmsa_licence_renewal', function ($table) {
            $table->increments('id');
            $table->integer('driver_id');
            $table->string('renewal_date');
            $table->string('expiry_date');
            $table->timestamps();
        });
        Schema::create('rsmsa_offence_registry', function ($table) {
            $table->increments('id');
            $table->text('nature');
            $table->string('section');
            $table->string('relating');
            $table->string('amount');
            $table->timestamps();
        });
        	Schema::create('rsmsa_payments', function ($table) {
        		$table->increments('id');
        		$table->string('mode');
        		$table->timestamps();
        	});
        Schema::create('rsmsa_receipts', function ($table) {
        	$table->increments('id');
        	$table->string('receipt_number');
        	$table->string('amount');
        	$table->string('payment_mode');
        	$table->date('date');
        	$table->timestamps();
        });
        Schema::create('rsmsa_offence_receipts', function ($table) {
        	$table->integer('offence_id')->unsigned();
            $table->integer('receipt_id')->unsigned();
            $table->primary(array('offence_id', 'receipt_id'));
        });
        Schema::create('rsmsa_offences', function($table)
        {
            $table->increments('id');
            $table->string('to');
            $table->string('address');
            $table->date('offence_date');
            $table->string('place');
            $table->string('facts');
            $table->string('vehicle_plate_number');
            $table->string('driver_license_number');
            $table->string('rank_no');
            $table->boolean('admit');
            $table->boolean('paid');
            $table->string('payment_mode');
            $table->string('latitude');
            $table->string('longitude');
            $table->timestamps();
        });

        Schema::create('rsmsa_insurance', function ($table) {
            $table->increments('id');
            $table->string('company_name')->unique();
            $table->string('principal_officer');
            $table->string('type');
            $table->string('phone_number');
            $table->string('policy_number');
            $table->string('address');
            $table->string('po_box');
            $table->string('fax');
            $table->string('email');
            $table->string('website');
            $table->string('phone_number');
            $table->timestamps();
        });
        Schema::create('rsmsa_car_insurance', function ($table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('car_id');
            $table->string('insurance_type');
            $table->string('start_date');
            $table->string('end_date');
            $table->timestamps();
        });
        Schema::create('rsmsa_offence_events', function ($table) {
            $table->integer('offence_id')->unsigned();
            $table->integer('offence_registry_id')->unsigned();
            $table->primary(array('offence_id', 'offence_registry_id'));
            $table->foreign('offence_id')->references('id')->on('rsmsa_offences');
            $table->foreign('offence_registry_id')->references('id')->on('rsmsa_offence_registry');
        });
        Schema::create('rsmsa_apps', function ($table) {
            $table->increments('id');
            $table->string('location')->unique();
        });

        //The Accident Table
        Schema::create('rsmsa_accidents', function ($table) {

            $table->increments('id');
            $table->string('accident_reg_number');
            $table->string('accident_class');
            $table->string('ocs_check');
            $table->string('supervisor_check');
            $table->string('rank_no');
            $table->foreign('rank_no')->references('rank_no')->on('rsmsa_police');
            $table->string('sign_date');
            $table->integer('accident_fatal')->unsigned();
            $table->integer('accident_severe_injury')->unsigned();
            $table->integer('accident_simple_injury')->unsigned();
            $table->integer('accident_only_damage')->unsigned();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('cause');
            $table->string('weather');
            $table->string('hit_run');
            $table->string('accident_date_time');
            $table->string('accident_area');
            $table->string('area_region');
            $table->string('area_district');
            $table->string('road_name');
            $table->string('road_number');
            $table->string('road_mark');
            $table->string('intersection_name');
            $table->string('intersection_number');
            $table->string('intersection_mark');
            $table->timestamps();

        });

        //the accident_driver table

        Schema::create('rsmsa_accident_driver', function ($table) {
            $table->increments('id');
            $table->integer('accident_id')->unsigned();
            $table->foreign('accident_id')->references('id')->on('rsmsa_accidents');
            $table->integer('driver_id')->unsigned();
            $table->foreign('driver_id')->references('id')->on('rsmsa_drivers');
            $table->string('severity');
            $table->string('phone_use');
            $table->string('seat_belt');
            $table->integer('alcohol')->unsigned();
            $table->timestamps();

        });

        //the accident_vehicle table

        Schema::create('rsmsa_accident_vehicle', function ($table) {
            $table->increments('id');
            $table->integer('accident_id')->unsigned();
            $table->foreign('accident_id')->references('id')->on('rsmsa_accidents');
            $table->integer('vehicle_id')->unsigned();
            $table->foreign('vehicle_id')->references('id')->on('rsmsa_vehicles');
            $table->string('vehicle_damage');
            $table->integer('vehicle_fatal')->unsigned();
            $table->integer('vehicle_severe_injury')->unsigned();
            $table->integer('vehicle_simple_injury')->unsigned();
            $table->integer('vehicle_not_injured')->unsigned();
            $table->timestamps();

        });


        //the accident_passenger table

        Schema::create('rsmsa_accident_passenger', function ($table) {
            $table->increments('id');
            $table->integer('accident_id')->unsigned();
            $table->foreign('accident_id')->references('id')->on('rsmsa_accidents');
            $table->string('pass_name');
            $table->string('pass_gender');
            $table->string('pass_dob');
            $table->string('pass_physical_address');
            $table->string('pass_address');
            $table->string('pass_national_id');
            $table->string('pass_phone_number');
            $table->string('pass_seat_belt');
            $table->integer('pass_alcohol')->unsigned();
            $table->string('pass_casuality');
            $table->timestamps();

        });

        //the accident_witness table
        Schema::create('rsmsa_accident_witness', function ($table) {
            $table->increments('id');
            $table->integer('accident_id')->unsigned();
            $table->foreign('accident_id')->references('id')->on('rsmsa_accidents');
            $table->string('witness_name');
            $table->string('witness_gender');
            $table->string('witness_dob');
            $table->string('witness_physical_address');
            $table->string('witness_address');
            $table->string('witness_national_id');
            $table->string('witness_phone_number');;
            $table->timestamps();

        });


        Schema::create('districts', function ($table) {
            $table->increments('id');
            $table-> string('name');
            $table-> integer('region_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('regions', function ($table) {
            $table->increments('id');
            $table-> string('name');
            $table-> string('coordinate');
            $table->timestamps();
        });
    }
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
        Schema::drop('rsmsa_apps');
        Schema::drop('rsmsa_stations');
		Schema::drop('rsmsa_persons');
		Schema::drop('rsmsa_users');
		Schema::drop('rsmsa_police');
		Schema::drop('rsmsa_vehicles');
		Schema::drop('rsmsa_drivers');
		Schema::drop('rsmsa_offence_registry');
		Schema::drop('rsmsa_receipts');
		Schema::drop('rsmsa_offences');
		Schema::drop('rsmsa_offence_receipts');
		Schema::drop('rsmsa_offence_events');
        Schema::drop('rsmsa_accidents');
        Schema::drop('rsmsa_accident_driver');
        Schema::drop('rsmsa_accident_vehicle');
        Schema::drop('rsmsa_accident_passenger');
        Schema::drop('rsmsa_accident_witness');
        Schema::drop('rsmsa_insurance');
        Schema::drop('rsmsa_districts');
        Schema::drop('rsmsa_regions');
	}

}
