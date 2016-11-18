use Illuminate\Database\Migrations\Migration;

class CharifyAirlinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::table(\Config::get('airlines.table_name'), function($table)
            {
								DB::statement("ALTER TABLE " . DB::getTablePrefix() . \Config::get('airlines.table_name') . " MODIFY code CHAR(3) NOT NULL DEFAULT ''");
                DB::statement("ALTER TABLE " . DB::getTablePrefix() . \Config::get('airlines.table_name') . " MODIFY name CHAR(255) NOT NULL DEFAULT ''");
                DB::statement("ALTER TABLE " . DB::getTablePrefix() . \Config::get('airlines.table_name') . " MODIFY country_code CHAR(2) NOT NULL DEFAULT ''");
                DB::statement("ALTER TABLE " . DB::getTablePrefix() . \Config::get('airlines.table_name') . " MODIFY country_name CHAR(225) NOT NULL DEFAULT ''");
            });
        }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
            Schema::table(\Config::get('airlines.table_name'), function($table)
            {
							DB::statement("ALTER TABLE " . DB::getTablePrefix() . \Config::get('airlines.table_name') . " MODIFY code CHAR(3) NOT NULL DEFAULT ''");
							DB::statement("ALTER TABLE " . DB::getTablePrefix() . \Config::get('airlines.table_name') . " MODIFY name CHAR(255) NOT NULL DEFAULT ''");
							DB::statement("ALTER TABLE " . DB::getTablePrefix() . \Config::get('airlines.table_name') . " MODIFY country_code CHAR(2) NOT NULL DEFAULT ''");
							DB::statement("ALTER TABLE " . DB::getTablePrefix() . \Config::get('airlines.table_name') . " MODIFY country_name CHAR(225) NOT NULL DEFAULT ''");
            });
	}

}
