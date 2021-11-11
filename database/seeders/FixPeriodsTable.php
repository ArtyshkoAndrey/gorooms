<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FixPeriodsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $periods = \App\Models\Period::all();
        foreach ($periods as $period) {
            if($period->cost_type_id == 1) {
                $period->start_at = substr($period->start_at, 0, 2);
                $period->end_at = substr($period->end_at, 0, 2);
            } else {
                if ($period->start_at) {
                    if (!is_numeric($period->start_at)) {

                    } else {
                        if(strlen($period->start_at) <= 2) {
                            $period->start_at = str_pad($period->start_at, 2, '0', STR_PAD_LEFT);
                            $period->start_at .= ':00';
                        }
                    }
                } else {
                    $period->start_at = '00:00';
                }

                if ($period->end_at) {
                    if (!is_numeric($period->end_at)) {
                        if ($period->end_at[2] != ':') {
                            $period->end_at = null;
                        }
                    } else {
                        if (strlen($period->end_at) <= 2) {
                            $period->end_at = str_pad($period->end_at, 2, '0', STR_PAD_LEFT);
                            $period->end_at .= ':00';
                        }
                    }
                }
            }

            if (strlen($period->start_at) >= 2 && $period->start_at[0] == 0 && $period->start_at[1] != 0) {
                $period->start_at = substr($period->start_at, 1,4);
            }
            if (strlen($period->end_at) >= 2 && $period->end_at[0] == 0 && $period->end_at[1] != 0) {
                $period->end_at = substr($period->end_at, 1,4);
            }

            $period->save();
        }
    }
}
