<?php

namespace ijeffro\Airlines;

use Illuminate\Database\Eloquent\Model;

/**
 * AirlineList
 *
 */
class Airlines extends Model {

	/**
	 * @var string
	 * Path to the directory containing airlines data.
	 */
	protected $airlines;

	/**
	 * @var string
	 * The table for the airlines in the database, is "airlines" by default.
	 */
	protected $table;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
       $this->table = \Config::get('airlines.table_name');
    }

    /**
     * Get the airlines from the JSON file, if it hasn't already been loaded.
     *
     * @return array
     */
    protected function getAirlines()
    {
        //Get the airlines from the JSON file
        if (sizeof($this->airlines) == 0){
            $this->airlines = json_decode(file_get_contents(__DIR__ . '/Models/airlines.json'), true);
        }

        //Return the airlines
        return $this->airlines;
    }

	/**
	 * Returns one airline
	 *
	 * @param string $id The airline id
     *
	 * @return array
	 */
	public function getOne($id)
	{
        $airlines = $this->getAirlines();
		return $airlines[$id];
	}

	/**
	 * Returns a list of airlines
	 *
	 * @param string sort
	 *
	 * @return array
	 */
	public function getList($sort = null)
	{
	    //Get the airlines list
	    $airlines = $this->getAirlines();

	    //Sorting
	    $validSorts = array(
					'code',
					'name',
					'country_code',
					'country_name'
        );

	    if (!is_null($sort) && in_array($sort, $validSorts)){
	        uasort($airlines, function($a, $b) use ($sort) {
	            if (!isset($a[$sort]) && !isset($b[$sort])){
	                return 0;
	            } elseif (!isset($a[$sort])){
	                return -1;
	            } elseif (!isset($b[$sort])){
	                return 1;
	            } else {
	                return strcasecmp($a[$sort], $b[$sort]);
	            }
	        });
	    }

	    //Return the airlines
		return $airlines;
	}

	/**
	 * Returns a list of airlines suitable to use with a select element in Laravelcollective\html
	 *
	 * @param string sort
	 *
	 * @return array
	 */
	public function getListForSelect($sort = null)
	{
		foreach ($this->getList('name') as $key => $value) {
			$airlines[$key] = $value['name'];
		}

		//return the array
		return $airlines;
	}
}
