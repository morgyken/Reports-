<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 * Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
extract($data);
$start = Illuminate\Support\Facades\Input::get('start');
$end = Illuminate\Support\Facades\Input::get('end');
$total = number_format(0, 2);

$cash_amnt = 0;
$mpesa_amnt = 0;
$cheq_amnt = 0;
$card_amnt = 0;

$i_amount = number_format(0, 2);
$n = 0;
if (!isset($mode)) {
    $mode = null;
}

function get_doctor_total($name, $doctor, $amount) {
    $doctor_amount = array_combine($doctor, $amount);
    $total = 0;
    foreach ($doctor_amount as $key => $value) {
        if (starts_with($key, $name)) {
            $total+=$value;
        }
    }
    return number_format($total, 2);
}
?>

@extends('layouts.app')
@section('content_title','Revenue Per Doctor')
@section('content_description','')

@section('content')
<div class="box box-info">
    <div class="box-body">
        @if(!$investigations->isEmpty())
        <div class="box-header">
            {!! Form::open()!!}
            <a class="btn btn-xs btn-primary" href="">View all records</a>
            Clinic:
            <select name="clinic">
                <option></option>
                @if(isset($clinics))
                @foreach($clinics as $item)
                <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
                @endif
            </select>
            Payment Mode:
            <select name="mode">
                <option></option>
                <option value="cash">Cash</option>
                <option value="insurance">Insurance</option>
            </select>
            Doctor/Medic:
            <select name="medic">
                <option></option>
                @foreach($data['doctors'] as $doc)
                <option value="{{$doc->id}}">{{$doc->profile->full_name}}</option>
                @endforeach
            </select>
            Start Date:
            <input type="text" id="date1" name="start" value="{{$start}}"/>
            End Date:
            <input type="text" id="date2" name="end" value="{{$end}}"/>
            <button  type="submit" id="clearBtn" class="btn btn-primary btn-xs">
                <i class="fa fa-filter"></i> Filter</button>
            {!! Form::close()!!}
        </div>

        <div class="alert alert-success">
            <i class="fa fa-info-circle"></i>
            {{filter_description($data['filter'])}}
        </div>

        <table class="table table-condensed table-responsive table-striped" id="data">
            <tbody id="result">
                <?php
                $doctor = array();
                $amount = array();
                $i = 0;
                if ($mode == 'cash') {//Disply Cash Only
                    ?>
                    @foreach($investigations as $item)
                    <?php
                    try {
                        $total+= $item->price;
                        $i+=1;
                        $doctor[] = str_slug($item->doctors->profile->name) . '_' . $i;
                        $amount[] = $item->price;
                        ?>
                        <tr id="payment{{$item->id}}">
                            <td>{{$n+=1}}</td>
                            <td>{{$item->payments?$item->payments->batch->receipt:''}}</td>
                            <td>{{$item->doctors?$item->doctors->profile->name:''}}</td>
                            <td>{{$item->visits->patients?$item->visits->patients->full_name:''}}</td>
                            <td>{{$item->procedures->name}}</td>
                            <td>{{smart_date_time($item->created_at)}}</td>
                            <td>{{$item->price}}</td>
                            <td>{{$item->payments?$item->payments->batch->modes:''}}</td>
                        </tr>
                        <?php
                    } catch (\Exception $e) {
                        //Leave it alone
                    }
                    ?>
                    @endforeach
                    <?php
                } elseif ($mode == 'insurance') {
                    //Disply Insurance Only
                    ?>
                    @if(!$insurance->isEmpty())
                    @foreach($insurance as $inv)
                    @if(isset($doc))<!--Insurance only for specific Doctor -->
                    @if($inv->visits->doctorID ==$doc->id)
                    @foreach($inv->visits->investigations as $item)
                    <?php
                    $i+=1;
                    $i_amount+=$item->price;
                    $doctor[] = str_slug($inv->visits->doctor) . '_' . $i;
                    $amount[] = $item->price;
                    ?>
                    <tr>
                        <td>{{$n+=1}}</td>
                        <td>{{$inv->invoice_no}}</td>
                        <td>{{$inv->visits->doctor}}</td>
                        <td>{{$inv->visits->patients?$inv->visits->patients->full_name:''}}</td>
                        <td>{{$item->procedures->name}}</td>
                        <td>{{(new Date($inv->created_at))->format('jS M Y h:a A')}}</td>
                        <td>{{$item->price}}</td>
                        <td>
                            Insurance
                            @if(!$inv->payments->isEmpty())
                            (paid)
                            @else
                            (unpaid)
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else <!--Insurance only for all Doctors -->
                    @foreach($inv->visits->investigations as $item)
                    <?php
                    if ($inv->visits->doctor !== '') {
                        $i+=1;
                        $i_amount+=$item->price;
                        $doctor[] = str_slug($inv->visits->doctor) . '_' . $i;
                        $amount[] = $item->price;
                        ?>
                        <tr>
                            <td>{{$n+=1}}</td>
                            <td>{{$inv->invoice_no}}</td>
                            <td>{{$inv->visits->doctor}}</td>
                            <td>{{$inv->visits->patients?$inv->visits->patients->full_name:''}}</td>
                            <td>{{$item->procedures->name}}</td>
                            <td>{{(new Date($inv->created_at))->format('jS M Y h:a A')}}</td>
                            <td>{{$item->price}}</td>
                            <td>
                                Insurance
                                @if(!$inv->payments->isEmpty())
                                (paid)
                                @else
                                (unpaid)
                                @endif
                            </td>
                        </tr>
                    <?php } ?>
                    @endforeach

                    @endif
                    @endif
                    @endforeach
                    @endif
                    <?php
                } else {//Disply Both Cash and Insurance
                    ?>
                    @foreach($investigations as $item)
                    @if($item->visits->payment_mode!=='insurance')
                    <?php
                    try {
                        $total+= $item->price;
                        $i+=1;
                        $doctor[] = str_slug($item->doctors->profile->name) . '_' . $i;
                        $amount[] = $item->price;
                        ?>
                        <tr id="payment{{$item->id}}">
                            <td>{{$n+=1}}</td>
                            <td>{{$item->payments?$item->payments->batch->receipt:''}}</td>
                            <td>{{$item->doctors?$item->doctors->profile->name:''}}</td>
                            <td>{{$item->visits->patients?$item->visits->patients->full_name:''}}</td>
                            <td>{{$item->procedures->name}}</td>
                            <td>{{(new Date($item->created_at))->format('jS M Y h:a A')}}</td>
                            <td>{{$item->price}}</td>
                            <td>{{$item->payments?$item->payments->batch->modes:''}}</td>
                        </tr>
                        <?php
                    } catch (\Exception $ex) {

                    }
                    ?>
                    @endif
                    @endforeach
                    <!--End of Cash -->

                    @if(!$insurance->isEmpty())
                    @foreach($insurance as $inv)
                    <!--DISPLY FOR SPECIFIC DOCTOR -->
                    @if(isset($doc))
                    @if($inv->visits->doctorID ==$doc->id)
                    @foreach($inv->visits->investigations as $item)
                    <?php
                    try {
                        $i_amount+=$item->price;
                        $i+=1;
                        $doctor[] = str_slug($inv->visits->doctor) . '_' . $i;
                        $amount[] = $item->price;
                        ?>
                        <tr>
                            <td>{{$n+=1}}</td>
                            <td>{{$inv->invoice_no}}</td>
                            <td>{{$inv->visits->doctor}}</td>
                            <td>{{$inv->visits->patients?$inv->visits->patients->full_name:''}}</td>
                            <td>{{$item->procedures->name}}</td>
                            <td>{{(new Date($inv->created_at))->format('jS M Y h:a A')}}</td>
                            <td>{{$item->price}}</td>
                            <td>
                                Insurance
                                @if(!$inv->payments->isEmpty())
                                (paid)
                                @else
                                (unpaid)
                                @endif
                            </td>
                        </tr>
                        <?php
                    } catch (\Exception $e) {

                    }
                    ?>
                    @endforeach
                    @else
                    @foreach($inv->visits->investigations as $item)
                    <!--DISPLY FOR ALL DOCTORS -->
                    <?php
                    try {
                        if ($inv->visits->doctor !== '') {
                            $i_amount+=$item->price;
                            $i+=1;
                            $doctor[] = str_slug($item->doctors->profile->name) . '_' . $i;
                            $amount[] = $item->price;
                            ?>
                            <tr>
                                <td>{{$n+=1}}</td>
                                <td>{{$inv->invoice_no}}</td>
                                <td>{{$inv->visits->doctor}}</td>
                                <td>{{$inv->visits->patients?$inv->visits->patients->full_name:''}}</td>
                                <td>{{$item->procedures->name}}</td>
                                <td>{{(new Date($inv->created_at))->format('jS M Y h:a')}}</td>
                                <td>{{$item->price}}</td>
                                <td>
                                    Insurance
                                    @if(!$inv->payments->isEmpty())
                                    (paid)
                                    @else
                                    (unpaid)
                                    @endif
                                </td>
                            </tr>
                            <?php
                        }//Endif
                    } catch (\Exception $ex) {

                    }
                    ?>
                    @endforeach
                    @endif
                    @endif
                    @endforeach
                    @endif
                    <?php
                }
                ?>
                <tr>
                    <td>{{$n+=1}}</td>
                    <td><strong>Total:</strong></td>
                    <td><strong>Insurance:</strong></td>
                    <td><strong>{{number_format($i_amount,2)}}</strong></td>
                    <td></td>
                    <td><strong>Cash</strong></td>
                    <td><strong>{{number_format($total,2)}}</strong></td>
                    <td></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Receipt/Invoice</th>
                    <th>Doctor</th>
                    <th>Patient</th>
                    <th>Procedure</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Mode</th>
                </tr>
            </thead>
        </table>
        <hr>
        <div class="col-md-12 col-sm-12 col-lg-12">

            <div class="col-md-6">
                <table class="table table-striped">
                    <tr>
                        <th>Doctor</th>
                        <th>Amount</th>
                    </tr>
                    @foreach($data['doctors'] as $doc)
                    <tr>
                        <td><strong>{{$doc->profile->full_name}}:</strong> </td>
                        <td><strong>{{get_doctor_total(str_slug($doc->profile->name),$doctor, $amount)}}</strong></td>
                    </tr>
                    @endforeach
                </table>
            </div>

            <div class="col-md-6">
                <table class="table table-striped">
                    <tr>
                        <th>Payment Mode</th>
                        <th>Amount</th>
                    </tr>
                    <tr>
                        <td><strong>Cash: </strong></td>
                        <td><strong>{{number_format($total,2)}}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Insurance:</strong> </td>
                        <td><strong>{{number_format($i_amount,2)}}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
        @else
        <div class="alert alert-info">
            <p><i class="fa fa-info-circle"></i> No payment records found</p>
        </div>
        @endif
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#date1").datepicker({dateFormat: 'yy-mm-dd', onSelect: function (date) {
                $("#date2").datepicker('option', 'minDate', date);
            }});
        $("#date2").datepicker({dateFormat: 'yy-mm-dd'});

        $('table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'excel', 'pdf', 'print'
            ]
        });
    });
</script>
@endsection