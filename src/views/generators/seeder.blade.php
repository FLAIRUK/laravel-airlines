use Illuminate\Database\Seeder;

class AirlinesSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Empty the airlines table
        DB::table(\Config::get('airlines.table_name'))->delete();

        //Get all of the airlines
        $airlines = Airlines::getList();
        foreach ($airlines as $airlineId => $airline){
            DB::table(\Config::get('airlines.table_name'))->insert(array(
                'id' => $airlineId,
                'code' => $airline['code'],
                'name' => $airline['name'],
                'country_code' => $airline['country_code'],
                'country_name' => $airline['country_name'],
            ));
        }
    }
}
