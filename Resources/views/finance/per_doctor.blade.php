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
$card_amnt = 0;
$cash_amount = 0;
$cheque_amount = 0;
$mpesa_amount = 0;
$i_amount = 0;
$start = Illuminate\Support\Facades\Input::get('start');
$end = Illuminate\Support\Facades\Input::get('end');

function get_doctor_amount($payment_type, $doctor_id) {
    $amount = 0;
    foreach ($payment_type->payments->details as $item) {
        if ($item->investigations->type == 'treatment') {
            if ($item->investigations->doctors->id === $doctor_id) {
                $amount+=$item->investigations->procedures->price;
            }
        }
    }
    return $amount;
}
?>

@extends('layouts.app')
@section('content_title','Doctor Summary')
@section('content_description','View detailed revenue per doctor report')

@section('content')
<div class="box box-info">
    <div class="box-header">
        <a class="btn btn-xs btn-primary" href="">View All Records</a>
        <div class="pull-right">
            {!! Form::open()!!}
            Doctor/Medic:
            <select name="medic">
                <option></option>
                @foreach($data['doctors'] as $doc)
                <option value="{{$doc->id}}">{{$doc->profile->full_name}}</option>
                @endforeach
            </select>
            Payment Mode: {!! Form::select('mode',mconfig('reports.options.payment_modes'),null,['placeholder'=>'All'])!!}
            Start Date: <input type="text" id="date1" name="start" value="{{$start}}"/>
            End Date: <input type="text" id="date2" name="end" value="{{$end}}"/>
            <button  type="submit" id="clearBtn" class="btn btn-primary btn-xs"><i class="fa fa-filter"></i> Filter</button>
            <!-- <a class="btn btn-xs btn-success" href="{{route('reports.print.cashier',['start'=>$start,'end'=>$end])}}"
               target="_blank"><i class="fa fa-file-pdf-o"></i> pdf</a> -->
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
                    <th>#</th>
                    <th>Receipt/Invoice.</th>
                    <th>Patient Name</th>
                    <th>Doctor</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php $n = 0; ?>
                @foreach($cash as $record)
                <tr>
                    <td>{{$n+=1}}</td>
                    <td>{{$record->payments->receipt}}</td>
                    <td>{{$record->payments->patients?$record->payments->patients->full_name:'-'}}</td>
                    <td>{{$record->payments->visits->doctor}}</td>
                    <td>{{$record->amount}} <!-- :: {{get_doctor_amount($record, $record->payments->visits->doctor_id)}} --></td>
                    <td>Cash</td>
                    <td>{{(new Date($record->created_at))->format('jS M Y h:i A')}}</td>
                </tr>
                @endforeach

                @foreach($card as $record)
                <tr>
                    <td>{{$n+=1}}</td>
                    <td>{{$record->payments->receipt}}</td>
                    <td>{{$record->payments->patients?$record->payments->patients->full_name:'-'}}</td>
                    <td>{{$record->payments->visits->doctor}}</td>
                    <td>{{$record->amount}}</td>
                    <td>Card</td>
                    <td>{{(new Date($record->created_at))->format('jS M Y h:i A')}}</td>
                </tr>
                @endforeach

                @foreach($cheque as $record)
                @if(isset($record->payment))
                <tr>
                    <td>{{$n+=1}}</td>
                    <td>{{$record->payments->receipt}}</td>
                    <td>{{$record->payments->patients->full_name}}</td>
                    <td>{{$record->payments->visits->doctor}}</td>
                    <td>{{$record->amount}}</td>
                    <td>Cheque</td>
                    <td>{{(new Date($record->created_at))->format('jS M Y h:i A')}}</td>
                </tr>
                @endif
                @endforeach

                @foreach($mpesa as $record)
                <tr>
                    <td>{{$n+=1}}</td>
                    <td>{{$record->payments->receipt}}</td>
                    <td>{{$record->payments->patients->full_name}}</td>
                    <td>{{$record->payments->visits->doctor}}</td>
                    <td>
                        {{$record->amount}}
                    </td>
                    <td>Mpesa</td>
                    <td>{{(new Date($record->created_at))->format('jS M Y h:i A')}}</td>
                </tr>
                @endforeach

                @if(!$insurance->isEmpty())
                @foreach($insurance as $inv)
                @foreach($inv->visits->investigations as $item)
                <?php $i_amount+=$item->price; ?>
                <tr>
                    <td>{{$n+=1}}</td>
                    <td>{{$inv->invoice_no}}</td>
                    <td>{{$inv->visits->patients?$inv->visits->patients->full_name:''}}</td>
                    <td>{{$item->doctors->profile->full_name?$item->doctors->profile->full_name:''}}</td>
                    <td>{{$item->price}}</td>
                    <td>
                        Insurance
                        @if(!$inv->payments->isEmpty())
                        (paid)
                        @else
                        (unpaid)
                        @endif
                    </td>
                    <td>{{(new Date($inv->created_at))->format('jS M Y h:i A H:m')}}</td>
                </tr>
                @endforeach
                @endforeach
                @endif
                <tr>
                    <th>{{$n+=1}}</th>
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
            <h4>Summary</h4>
            <table class="table table-striped">
                <tr>
                    <th>Mode:</th>
                    <th>Cash</th>
                    <th>Card</th>
                    <th>MPesa</th>
                    <th>Cheque</th>
                    <th>Insurance</th>
                </tr>
                <tr>
                    <td>Amount</td>
                    <td>{{number_format($cash->sum('amount'),2)}}</td>
                    <td>{{number_format($card->sum('amount'),2)}}</td>
                    <td>{{number_format($mpesa->sum('amount'),2)}}</td>
                    <td>{{number_format($cheque->sum('amount'),2)}}</td>
                    <td>{{number_format($i_amount,2)}}</td>
                </tr>
                <tr>
                    <td><strong>Total (Without Insurance):</strong></td>
                    <td colspan="5">{{number_format($cash->sum('amount')+$card->sum('amount')+$mpesa->sum('amount')+$cheque->sum('amount'),2)}}</td>
                </tr>
                <tr>
                    <td><strong>Total (With Insurance):</strong></td>
                    <td colspan="5">{{number_format($cash->sum('amount')+$card->sum('amount')+$mpesa->sum('amount')+$cheque->sum('amount')+$i_amount,2)}}</td>
                </tr>
            </table>
            <hr>
            <!--
            <table class="table table-striped">
                <tr>
                    <th>#</th>
                    <th>Doctor</th>
                    <th>Amount</th>
                </tr>
                @foreach($data['doctors'] as $doc)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$doc->profile->full_name}}</td>
                    <td></td>
                </tr>
                @endforeach
            </table>
            -->
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
        // alertify.success('NOTE: All ');
    });
</script>
@endsection