<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 * Author: Bravo Gidi
 */
extract($data);

$start = Illuminate\Support\Facades\Input::get('start');
$end = Illuminate\Support\Facades\Input::get('end');
$total = number_format(0, 2);
$total_i = number_format(0, 2);
?>
@extends('layouts.app')
@section('content_title','Revenue via Insurance')
@section('content_description','')

@section('content')
<div class="box box-info">
    <div class="box-body">
        <div class="box-header">
            {!! Form::open()!!}
            Clinic:
            <select name="clinic">
                <option>All</option>
                @foreach($clinics as $item)
                <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
            </select>
            Department:
            {!! Form::select('department',mconfig('reception.options.destinations'),null,['placeholder'=>'All'])!!}
            Doctor/Medic:
            <select name="medic">
                <option>All</option>
                @foreach($medic as $item)
                <?php $slug = $item->slug ?>
                @foreach($item->assignees as $item)
                @foreach($item->users as $item)
                <option value="{{$item->id}}">
                    @if($slug =='doctor')
                    Dr.
                    @endif
                    {{$item->profile->name}}
                </option>
                @endforeach
                @endforeach
                @endforeach
            </select>
            Start Date:
            <input type="text" id="date1" name="start" value="{{$start}}"/>
            End Date:
            <input type="text" id="date2" name="end" value="{{$end}}"/>
            <button  type="submit" id="clearBtn" class="btn btn-primary btn-xs">
                <i class="fa fa-filter"></i> Filter</button>
            {!! Form::close()!!}
            <a href="" class="btn btn-primary btn-xs">All Records</a>
        </div>
        <table class="table table-condensed table-responsive table-striped" id="data">
            <tbody id="result">
                @if(!$i_payments->isEmpty())
                @if(isset($i_payments))
                @foreach($data['i_payments'] as $payment)
                @foreach($payment->invoice->visits->investigations as $item)
                <?php $total_i+= $item->price; ?>
                <tr id="payment{{$item->id}}">
                    <td>#</td>
                    <td>{{$payment->invoice->invoice_no}}</td>
                    <td></td>
                    <td>{{$item->procedures->categories->name}}</td>
                    <td>{{$item->doctors->profile->name}}</td>
                    <td>{{$item->visits->patients?$item->visits->patients->full_name:'-'}}</td>
                    <td>{{smart_date_time($item->payments?$item->payments->batch->created_at:'')}}</td>
                    <td>{{$item->price}}</td>
                </tr>
                @endforeach
                @endforeach
                @endif
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right"><strong>Total</strong></td>
                    <td><strong>{{number_format($total_i,2)}}</strong></td>
                </tr>
                @endif
            </tbody>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Receipt/Invoice</th>
                    <th>Cashier</th>
                    <th>Department</th>
                    <th>Medic</th>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
        </table>
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