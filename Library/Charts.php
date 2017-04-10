<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ignite\Analytics\Library;

use Lava;

/**
 * Description of Charts
 *
 * @author dervis
 */
class Charts {

    public static function procedureCharts($data) {
        $procedures = Lava::DataTable();
        $procedures->addStringColumn('Procedure')
                ->addNumberColumn('Price');
        foreach ($data as $item) {
            $procedures->addRow([$item->procedures->name, $item->price]);
        }
        Lava::BarChart('procedureCharts', $procedures, [
            'title' => 'Procedures Report',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14
            ]
        ]);
    }

    public static function visitCharts($visits) {
        $finances = Lava::DataTable();
        $finances->addDateColumn('Month');
        $clinics = get_clinics();
        foreach ($clinics as $clinic) {
            $finances->addNumberColumn($clinic);
        }
        foreach ($visits as $visit) {
            $finances->addRow([$visit->created_at, $visits->where('clinic', 1)->count(), $visits->where('clinic', 2)->count()]);
        }
        Lava::ColumnChart('visitCharts', $finances, [
            'title' => 'Clinic Performance',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14
            ]
        ]);
    }

}
