<?php

/*
 * =============================================================================
 *
 * Collabmed Solutions Ltd
 * Project: iClinic
 * Author: Samuel Okoth <sodhiambo@collabmed.com>
 *
 * =============================================================================
 */

namespace Ignite\Reports\Sidebar;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Ignite\Core\Contracts\Authentication;
use Maatwebsite\Sidebar\Menu;
use Maatwebsite\Sidebar\SidebarExtender as Panda;

/**
 * Description of SidebarExtender
 *
 * @author Samuel Dervis <samueldervis@gmail.com>
 */
class SidebarExtender implements Panda
{

    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @param Authentication $auth
     *
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    public function extendWith(Menu $menu)
    {
        $menu->group('Dashboard', function (Group $group) {
            $group->item('Reports', function (Item $item) {
                $item->weight(100);
                $item->authorize($this->auth->hasAccess('reports.*'));
                $item->icon('fa fa-bar-chart');

                $item->item('Patient Management', function (Item $item) {
                    $item->icon('fa fa-superpowers');
                    $item->authorize($this->auth->hasAccess('reports.patient'));

                    $item->item('Procedures Perfomed', function (Item $item) {
                        $item->icon('fa fa-life-ring');
                        $item->route('reports.patients.procedures');
                    });
                    $clinics = [
                        ['name' => 'hpd', 'icon' => 'fa-stethoscope',],
                        ['name' => 'popc', 'icon' => 'fa-openid', 'show' => 'Pedeatrics'],
                        ['name' => 'orthopeadic', 'icon' => 'fa-magnet', 'show' => 'Orthopeadic'],
                        ['name' => 'mopc', 'icon' => 'fa-magic', 'show' => 'Medical'],
                        ['name' => 'sopc', 'icon' => 'fa-paw', 'show' => 'Surgical'],
                        ['name' => 'gopc', 'icon' => 'fa-sun-o', 'show' => 'Gyenecology'],
                        ['name' => 'physio', 'icon' => 'fa-tint', 'show' => 'Physiotherapy'],
                    ];
                    foreach ($clinics as $clinic) {
                        $clinic = (object)$clinic;
                        if (m_setting('evaluation.with_clinic_' . $clinic->name)) {
                            $name = $clinic->show ?? strtoupper($clinic->name);
                            $item->item($name . ' Reports', function (Item $item) use ($clinic) {
                                $item->icon('fa ' . $clinic->icon);
                                $item->route('reports.patients.clinic', $clinic->name);
                                $item->authorize($this->auth->hasAccess('reports.clinics'));
                            });
                        }
                    }
                    $item->item('Patient Diagnoses', function (Item $item) {
                        $item->icon('fa fa-bath');
                        $item->route('reports.patients.treatment');
                    });
                    $item->item('Medication Given', function (Item $item) {
                        $item->icon('fa fa-tablet');
                        $item->route('reports.patients.medication');
                    });
                    $item->item('Patient Visits', function (Item $item) {
                        $item->icon('fa fa-meetup');
                        $item->route('reports.patients.visits');
                    });
                    $item->item('Lab Procedures', function (Item $item) {
                        $item->icon('fa fa-filter');
                        $item->route('reports.labs');
                    });
                });
                $item->item('Inventory analytics', function (Item $item) {
                    $item->icon('fa fa-shopping-bag');
                    //  $item->route('evaluation.waiting_nurse');
                    $item->authorize($this->auth->hasAccess('reports.inventory'));

                    $item->item('Stock Report', function (Item $item) {
                        $item->icon('fa fa-hourglass-half');
                        $item->route('reports.inventory.stocks');
                        // $item->authorize($this->auth->hasAccess('inventory.Reports.View Stock Report'));
                    }); //stock Reports

                    $item->item('Stock Movement', function (Item $item) {
                        $item->icon('fa fa-arrows');
                        $item->route('reports.inventory.stocks.movement');
                        //$item->authorize($this->auth->hasAccess('inventory.Reports.View Stock Movement Report'));
                    }); //stock Reports


                    $item->item('Item Expiry', function (Item $item) {
                        $item->icon('fa fa-calendar');
                        $item->route('reports.inventory.stocks.expiry');
                        // $item->authorize($this->auth->hasAccess('inventory.Reports.View Item Expiry Report'));
                    }); //stock Reports
                });

                $item->item('Financial analytics', function (Item $item) {
                    $item->icon('fa fa-money');
                    $item->authorize($this->auth->hasAccess('reports.financial'));


                    $item->item('Doctor Summary', function (Item $item) {
                        $item->icon('fa fa-user-md');
                        $item->route('reports.finance.doctor');
                        $item->authorize($this->auth->hasAccess('reports.financial'));
                    });
                    $item->item('Sales Report', function (Item $item) {
                        $item->icon('fa fa-btc');
                        $item->route('reports.finance.sales');
                        $item->authorize($this->auth->hasAccess('reports.financial'));
                    });

                    /*
                      $item->item('Doctor Summary', function (Item $item) {
                      $item->icon('fa fa-user-md');
                      $item->route('reports.finance.per_doctor');
                      $item->authorize($this->auth->hasAccess('reports.financial'));
                      }); */

                    $item->item('Department Summary', function (Item $item) {
                        $item->icon('fa fa-group');
                        $item->route('reports.finance.department');
                        $item->authorize($this->auth->hasAccess('reports.financial'));
                    });

                    $item->item('Per Payment Mode', function (Item $item) {
                        $item->icon('fa fa-cc-paypal');
                        $item->route('reports.finance.payment_mode');
                        $item->authorize($this->auth->hasAccess('reports.financial'));
                    });

                    $item->item('Payments Summary', function (Item $item) {
                        $item->icon('fa fa-money');
                        $item->route('reports.finance.cashier');
                        $item->authorize($this->auth->hasAccess('reports.financial'));
                    });
                });


                $item->item('Pharmacy analyitcs', function (Item $item) {
                    $item->icon('fa fa-tablet');
                    $item->authorize($this->auth->hasAccess('reports.pharmacy'));


                    $item->item('Sales', function (Item $item) {
                        $item->icon('fa fa-cart-arrow-down');
                        $item->route('reports.inventory.sales');
                        // $item->authorize($this->auth->hasAccess('inventory.Reports.View Sales Reports'));
                    }); //Sales

                    $item->item('Sales per Item', function (Item $item) {
                        $item->icon('fa fa-gift');
                        $item->route('reports.inventory.sales.product');
                        //$item->authorize($this->auth->hasAccess('inventory.Reports.View Product Sales Report'));
                    }); //Sales
                });

                /*  $item->item('Design', function (Item $item) {
                  $item->icon('fa fa-shopping-bag');
                  //  $item->route('evaluation.waiting_nurse');
                  $item->authorize($this->auth->hasAccess('reports.inventory'));
                  }); */
            });
        });
        return $menu;
    }

}
