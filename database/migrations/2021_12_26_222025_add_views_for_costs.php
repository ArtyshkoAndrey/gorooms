<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddViewsForCosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE ALGORITHM=UNDEFINED VIEW `min_hotels_costs` AS select `hotels`.`id` AS `hotel_id`,min(`costs`.`value`) AS `min_cost` from ((`costs` left join `rooms` on((`costs`.`room_id` = `rooms`.`id`))) left join `hotels` on((`rooms`.`hotel_id` = `hotels`.`id`))) group by `hotels`.`id`');
        DB::statement('CREATE ALGORITHM=UNDEFINED VIEW `min_rooms_costs` AS select `rooms`.`id` AS `room_id`,min(`costs`.`value`) AS `min_cost` from (`costs` left join `rooms` on((`costs`.`room_id` = `rooms`.`id`))) group by `rooms`.`id`');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
