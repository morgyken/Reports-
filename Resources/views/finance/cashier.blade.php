<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 *  Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
$cash = $data['cash'];
$card = $data['card'];
$cheque = $data['cheque'];
$mpesa = $data['mpesa'];
$insurance = $data['insurance'];
$i_amount = 0;
$start = Illuminate\Support\Facades\Input::get('start');
$end = Illuminate\Support\Facades\Input::get('end');
?>

@extends('layouts.app')
@section('content_title','Cashier Summary')
@section('content_description','View detailed report for previous payments')

@section('content')
<div class="box box-info">
    <div class="box-header">
        <div class="pull-right">
            {!! Form::open()!!}
            Start Date: <input type="text" id="date1" name="start" value="{{$start}}"/>
            End Date: <input type="text" id="date2" name="end" value="{{$end}}"/>
            <button  type="submit" id="clearBtn" class="btn btn-primary btn-xs"><i class="fa fa-filter"></i> Filter</button>
            {!! Form::close()!!}
        </div>
    </div>
    <div class="box-body">
        <div class="alert alert-success">
            <i class="fa fa-info-circle"></i> {{filter_description($data['filter'])}}
        </div>
        <table id="cashier" class="table table-striped">
            <thead>
                <tr>
                    <th>Receipt No.</th>
                    <th>Patient Name</th>
                    <th>Cashier</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cash as $record)
                <tr>
                    <td>{{$record->payments->receipt}}</td>
                    <td>{{$record->payments->patients?$record->payments->patients->full_name:'-'}}</td>
                    <td>{{$record->payments->users->profile->full_name}}</td>
                    <td>{{$record->amount}}</td>
                    <td>Cash</td>
                    <td>{{(new Date($record->created_at))->format('jS M Y')}}</td>
                </tr>
                @endforeach

                @foreach($card as $record)
                <tr>
                    <td>{{$record->payments->receipt}}</td>
                    <td>{{$record->payments->patients?$record->payments->patients->full_name:'-'}}</td>
                    <td>{{$record->payments->users->profile->full_name}}</td>
                    <td>{{$record->amount}}</td>
                    <td>Card</td>
                    <td>{{(new Date($record->created_at))->format('jS M Y')}}</td>
                </tr>
                @endforeach

                @foreach($cheque as $record)
                @if(isset($record->payment))
                <tr>
                    <td>{{$record->payments->receipt}}</td>
                    <td>{{$record->payments->patients->full_name}}</td>
                    <td>{{$record->payments->users->profile->full_name}}</td>
                    <td>{{$record->amount}}</td>
                    <td>Cheque</td>
                    <td>{{(new Date($record->created_at))->format('jS M Y')}}</td>
                </tr>
                @endif
                @endforeach

                @foreach($mpesa as $record)
                <tr>
                    <td>{{$record->payments->receipt}}</td>
                    <td>{{$record->payments->patients->full_name}}</td>
                    <td>{{$record->payments->users->profile->full_name}}</td>
                    <td>{{$record->amount}}</td>
                    <td>Mpesa</td>
                    <td>{{(new Date($record->created_at))->format('jS M Y')}}</td>
                </tr>
                @endforeach

                @if(!$insurance->isEmpty())
                @foreach($insurance as $inv)
                <?php $i_amount+=$inv->payment; ?>
                <tr>
                    <td>{{$inv->invoice_no}}</td>
                    <td>{{$inv->visits->patients?$inv->visits->patients->full_name:''}}</td>
                    <td>
                        @if(!$inv->payments->isEmpty())
                        @foreach($inv->payments as $p)
                        {{$p->users->profile->full_name}}
                        @endforeach
                        @else
                        ** unpaid
                        @endif
                    </td>
                    <td>{{$inv->payment}}</td>
                    <td>
                        Insurance
                        @if(!$inv->payments->isEmpty())
                        (paid)
                        @else
                        (unpaid)
                        @endif
                    </td>
                    <td>{{(new Date($inv->created_at))->format('jS M Y')}}</td>
                </tr>
                @endforeach
                @endif

                <tr>
                    <th>Totals</th>
                    <th>Cash: {{number_format($cash->sum('amount'),2)}}</th>
                    <th>Card: {{number_format($card->sum('amount'),2)}}</th>
                    <th>Insurance: {{$i_amount}}</td>
                    <th>Mpesa: {{number_format($mpesa->sum('amount'),2)}}</th>
                    <th>Cheque: {{number_format($cheque->sum('amount'),2)}}</th>
                </tr>
            </tbody>
        </table>
        <div>
            <hr/>
            <h4>Period summary</h4>
            <table class="table table-striped">
                <tr>
                    <th>Cash</th>
                    <th>Card</th>
                    <th>MPesa</th>
                    <th>Cheque</th>
                    <th>Insurance</th>
                </tr>
                <tr>
                    <td>{{number_format($cash->sum('amount'),2)}}</td>
                    <td>{{number_format($card->sum('amount'),2)}}</td>
                    <td>{{number_format($mpesa->sum('amount'),2)}}</td>
                    <td>{{number_format($cheque->sum('amount'),2)}}</td>
                    <td>{{number_format($i_amount,2)}}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#date1").datepicker({dateFormat: 'yy-mm-dd', onSelect: function (date) {
                $("#date2").datepicker('option', 'minDate', date);
            }});
        $("#date2").datepicker({dateFormat: 'yy-mm-dd'});

        $('#cashier').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'excel', 'pdf', 'print'
            ]
        });
    });
</script>

@endsection