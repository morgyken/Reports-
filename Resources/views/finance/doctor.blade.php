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
$i_amount = number_format(0, 2);
if (!isset($mode)) {
    $mode = null;
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
                if ($mode == 'cash') {//Disply Cash Only
                    ?>
                    @foreach($investigations as $item)
                    <?php $total+= $item->price; ?>
                    <tr id="payment{{$item->id}}">
                        <td>
                            {{$item->payments?$item->payments->batch->receipt:''}}
                        </td>
                        <td>{{$item->doctors?$item->doctors->profile->name:''}}</td>
                        <td>{{$item->visits->patients?$item->visits->patients->full_name:''}}</td>
                        <td></td>
                        <td>{{smart_date_time($item->payments?$item->payments->batch->created_at:'')}}</td>
                        <td>{{$item->price}}</td>
                        <td>{{$item->payments?$item->payments->batch->modes:''}}</td>
                    </tr>
                    @endforeach
                    <?php
                } elseif ($mode == 'insurance') {//Disply Insurance Only
                    ?>
                    @if(!$insurance->isEmpty())
                    @foreach($insurance as $inv)
                    <?php $i_amount+=$inv->payment; ?>
                    @if($inv->visits->doctor)
                    @if(isset($doc))
                    @if($inv->visits->doctorID ==$doc)
                    <tr>
                        <td>
                            {{$inv->invoice_no}} ({{$doc}})
                        </td>
                        <td>{{$inv->visits->doctor}}</td>
                        <td>{{$inv->visits->patients?$inv->visits->patients->full_name:''}}</td>
                        <td></td>
                        <td>{{(new Date($inv->created_at))->format('jS M Y h:a A')}}</td>
                        <td>{{$inv->payment}}</td>
                        <td>
                            Insurance
                            @if(!$inv->payments->isEmpty())
                            (paid)
                            @else
                            (unpaid)
                            @endif
                        </td>
                    </tr>
                    @endif
                    @else
                    <tr>
                        <td>
                            {{$inv->invoice_no}}
                        </td>
                        <td>{{$inv->visits->doctor}}</td>
                        <td>{{$inv->visits->patients?$inv->visits->patients->full_name:''}}</td>
                        <td></td>
                        <td>{{(new Date($inv->created_at))->format('jS M Y h:a A')}}</td>
                        <td>{{$inv->payment}}</td>
                        <td>
                            Insurance
                            @if(!$inv->payments->isEmpty())
                            (paid)
                            @else
                            (unpaid)
                            @endif
                        </td>
                    </tr>

                    @endif
                    @endif
                    @endforeach
                    @endif
                    <?php
                } else {//Disply Both Cash and Insurance
                    ?>
                    @foreach($investigations as $item)
                    @if($item->visits->payment_mode!=='insurance')
                    <?php $total+= $item->price; ?>
                    <tr id="payment{{$item->id}}">
                        <td>
                            {{$item->payments?$item->payments->batch->receipt:''}}
                        </td>
                        <td>{{$item->doctors?$item->doctors->profile->name:''}}</td>
                        <td>{{$item->visits->patients?$item->visits->patients->full_name:''}}</td>
                        <td></td>
                        <td>{{(new Date($item->payments?$item->payments->batch->created_at:''))->format('jS M Y h:a A')}}</td>
                        <td>{{$item->price}}</td>
                        <td>{{$item->payments?$item->payments->batch->modes:''}}</td>
                    </tr>
                    @endif
                    @endforeach<!--End of Cash -->

                    @if(!$insurance->isEmpty())
                    @foreach($insurance as $inv)
                    <?php $i_amount+=$inv->payment; ?>
                    @if($inv->visits->doctor)
                    @if(isset($doc))
                    @if($inv->visits->doctorID ==$doc->id)
                    <tr>
                        <td>
                            {{$inv->invoice_no}}
                        </td>
                        <td>{{$inv->visits->doctor}}</td>
                        <td>{{$inv->visits->patients?$inv->visits->patients->full_name:''}}</td>
                        <td></td>
                        <td>{{(new Date($inv->created_at))->format('jS M Y h:a A')}}</td>
                        <td>{{$inv->payment}}</td>
                        <td>
                            Insurance
                            @if(!$inv->payments->isEmpty())
                            (paid)
                            @else
                            (unpaid)
                            @endif
                        </td>
                    </tr>
                    @endif
                    @else
                    <tr>
                        <td>
                            {{$inv->invoice_no}}
                        </td>
                        <td>{{$inv->visits->doctor}}</td>
                        <td>{{$inv->visits->patients?$inv->visits->patients->full_name:''}}</td>
                        <td>{{(new Date($inv->created_at))->format('jS M Y')}}</td>
                        <td>{{$inv->payment}}</td>
                        <td>
                            Insurance
                            @if(!$inv->payments->isEmpty())
                            (paid)
                            @else
                            (unpaid)
                            @endif
                        </td>
                    </tr>
                    @endif
                    @endif
                    @endforeach
                    @endif
                    <?php
                }
                ?>
                <tr>
                    <td style="text-align: right"><strong>Total (Insurance):</strong></td>
                    <td><strong>{{number_format($i_amount,2)}}</strong></td>
                    <td></td>
                    <td style="text-align: right"><strong>Total (Cash):</strong></td>
                    <td><strong>{{number_format($total,2)}}</strong></td>
                    <td></td>
                </tr>
            </tbody>
            <thead>
                <tr>
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