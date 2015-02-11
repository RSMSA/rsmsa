<?php
/**
 * The Controller for offences
 * 
 * @author Vincent P. Minde
 *
 */
class OffenceController extends BaseController {

	/**
	 * Process post request for offence.
	 * 
	 * This function creates a new offence or updates a new one
	 */
	public function processOffencePost()
	{
		//Fetch the content from the request
		$request = Request::instance();
		$content = $request->getContent();
		$json = json_decode($content,true);
		$offenceJSON = $json['offence'];
		$eventsJSON = $json['events'];
		try{
			//Start a transaction to save an offence
			DB::transaction(function()use ($offenceJSON,$eventsJSON)
			{
				$offence = "";
				$hasID = false;
				if(array_key_exists( "id", $offenceJSON)){
					if($offenceJSON['id'] != "")//If id exist and in not empty then the offence exists so update it
					{
						$offence = Offence::find($offenceJSON['id']);
						$this->saveOffence($offence,$offenceJSON);
						$hasID = true;
					}
				}
				if(!$hasID){
					
					$offence = new Offence();
					$this->saveOffence($offence,$offenceJSON);
				}
				//Save the offence event to the OffenceEvent model
				foreach($eventsJSON as $registry)
				{
					$event = new OffenceEvent();
					$event->offence_id = $offence->id;
					$event->offence_registry_id = $registry['id'];
					try{
						$event->save();
					}catch(Exception $qe){
						
					};
				}
			});
		}catch(Exception $exception){
			Log::error($exception);
			//Return error to client
			return "{'status':'ERROR','code': 1,'message':'Message will come soon.'}";
		}
		return "{'status':'OK'}";
	}
	/**
	 * 
	 * Saves the offence to database
	 * 
	 * @param Offence $offence
	 * 
	 * @param String $offenceJSON
	 */
	private function saveOffence($offence,$offenceJSON){
		//Set offence object value from json
		$offence->setValuesByJSON($offenceJSON);
		if(is_numeric ($offence['offence_date']))
		{
			$offence['offence_date'] = date("Y-m-d",$offence['offence_date']);
		}
		$offence->save();
	}
	/**
	 * Get the offence events made in a given offence
	 * 
	 * @param int|string $id(Offence id)
	 */
	public function getEvents($id)
	{
		$offence = Offence::find($id);
		//return $offence;
		return $offence->offenceRegistries;
	}
	/**
	 * Delete offence
	 * 
	 * @param int|string $id (Offence id)
	 */
	public function delete($id)
	{
		DB::transaction(function()use ($id)
		{
			$offence = Offence::find($id);
			$offence->delete();
		});
		
		return json_encode("{'status':'OK'}");
	}
	/**
	 * Get all offences
	 */
	public function getOffences(){
		return Offence::all();
	}
	/**
	 * Get all offence registries
	 */
	public function getOffenceRegistry()
	{
		return OffenceRegistry::all();
	}
	/**
	 * Get offence registry of a given offence
	 * 
	 * @param string|int $id
	 */
	public function getOffenceRegistryOffences($id){
		$offenceReg = OffenceRegistry::find($id);
		return $offenceReg->offences;
	}
	/**
	 * Get an offence
	 * 
	 * @param int|string $id
	 */
	public function getOffence($id)
	{
		return Offence::find($id);
	}
	/**
	 * Get a report in JSON format
	 * 
	 * This gets a JSON with requests parameters in the form
	 * 
	 * {
	 *			show :"",
	 *			horizontal :"",
	 *			gender :"",
	 *			year:"",
	 *			startYear:"",
	 *			endYear:"",
	 *			ageRange:"",
	 *			startDate:Date(),
	 *			endDate:Date()
	 *	}
	 */
	public function getReport()
	{
		//return Offence::groupBy('offence_date')->get();
		$request = Request::instance();
		$content = $request->getContent();
		$json = json_decode($content,true);
		$offences = new Offence();
		//Add column to query
		$column = array(DB::raw('count(*) as offences'));
		
		if($json['horizontal'] == 'year'){//Query by specific year
			//Add month column to the table query as time
			array_push($column,DB::raw("DATE_FORMAT(offence_date,'%M') as time"));
			$offences = $offences->where(DB::raw("DATE_FORMAT(offence_date,'%Y')"),"=", $json['year']);
		}else if($json['horizontal'] == 'years'){//Query by range of years
			//Add a year column to the table query
			array_push($column,DB::raw("DATE_FORMAT(offence_date,'%Y') as time"));
			if($json['startYear'] != '' && $json['endYear'] != ''){
				$offences = $offences->whereBetween(DB::raw("DATE_FORMAT(offence_date,'%Y')"), array($json['startYear'], $json['endYear']));
			}else if($json['startYear'] != ''){
				$offences = $offences->where(DB::raw("DATE_FORMAT(offence_date,'%Y')"),">=", array($json['startYear']));
			}else if($json['endYear'] != ''){
				$offences = $offences->where(DB::raw("DATE_FORMAT(offence_date,'%Y')"),"<=", array($json['endYear']));
			}
		}else if($json['horizontal'] == 'dates'){//Query by range of date
			//Add a date column to the table
			array_push($column,DB::raw("offence_date as time"));
			if($json['startDate'] != '' && $json['endDate'] != ''){
				$offences = $offences->whereBetween("offence_date", array($json['startDate'], $json['endDate']));
			}else if($json['startDate'] != ''){
				$offences = $offences->where("offence_date",">=", array($json['startDate']));
			}else if($json['endDate'] != ''){
				$offences = $offences->where("offence_date","<=", array($json['endDate']));
			}
		}else if($json['horizontal'] == 'age'){//Query by range of age
			//Add a year column to the table
			array_push($column,DB::raw("(YEAR(CURRENT_DATE) - YEAR(birthdate)) as time"));
			$offences = $offences->join('rsmsa_drivers', 'rsmsa_drivers.license_number', '=', 'rsmsa_offences.driver_license_number');
		}else{
			array_push($column,DB::raw("DATE_FORMAT(offence_date,'%M') as time"));
		}
		if($json['gender'] == 'M' || $json['gender'] == 'F'){//Query by gender
			if($json['horizontal'] != 'age')//Checks to ensure driver join is not made twice
			{
				$offences = $offences->join('rsmsa_drivers', 'rsmsa_drivers.license_number', '=', 'rsmsa_offences.driver_license_number');
			}
			$offences = $offences->where("gender","=",$json['gender']);
		}
		return $offences->select($column)->groupBy("time")->orderBy("offence_date", 'DESC')->get();	
	}
	/**
	 * 
	 * Get statistics same as report
	 * 
	 * TODO this will be done at the get report 
	 */
	public function getStats(){
		$request = Request::instance();
		$content = $request->getContent();
		$json = json_decode($content,true);
		if($json['type'] != null)
		{
			if($json['type'] == "gender")
			{
				return Driver::select(DB::raw("gender"),DB::raw("count(*) as offences"))->join('rsmsa_offences', 'rsmsa_drivers.license_number', '=', 'rsmsa_offences.driver_license_number')->groupBy('gender')->get();
			}
			
		}else
		{
			return Offence::select(DB::raw("DATE_FORMAT(offence_date,'%M') as time"), DB::raw('count(*) as offences'))
			->groupBy("time")->orderBy("offence_date")->get();
		}
		
	}
}