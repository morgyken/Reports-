<?php
extract($data);
$cash = null;
$card = null;
$mpesa = null;
$cheque = null;
$total = $total_i = 0;

if (isset($data['cash'])) {
    $cash = $data['cash'];
} elseif (isset($data['card'])) {
    $card = $data['card'];
} elseif (isset($data['mpesa'])) {
    $mpesa = $data['mpesa'];
} elseif (isset($data['cheque'])) {
    $cheque = $data['cheque'];
} else {
    $all = TRUE;
}
$start = Illuminate\Support\Facades\Input::get('start');
$end = Illuminate\Support\Facades\Input::get('end');
?>

@extends('layouts.app')
@section('content_title','Payment Mode Summary')
@section('content_description','View detailed report for previous payments')

@section('content')
<div class="box box-info">
    <div class="box-header">
        <div class="pull-right">
            {!! Form::open()!!}
            Start Date: <input type="text" id="date1" name="start" value="{{$start}}"/>
            End Date: <input type="text" id="date2" name="end" value="{{$end}}"/>
            Payment Mode: {!! Form::select('mode',mconfig('analytics.options.payment_modes'),null,['placeholder'=>'All'])!!}
            <button  type="submit" id="clearBtn" class="btn btn-primary btn-xs">
                <i class="fa fa-filter"></i> Filter
            </button>
            {!! Form::close()!!}
        </div>
    </div>
    <div class="box-body">
        <div class="alert alert-success">
            <i class="fa fa-info-circle"></i> {{filter_description($data['filter'])}}
        </div>
        <table id="cashier" class="table table-borderless">
            <thead>
                <tr>
                    <th>Receipt/Invoice No.</th>
                    <th>Patient Name</th>
                    <th>Cashier</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($cash))
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
                @endif

                @if(isset($card))
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
                @endif

                @if(isset($mpesa))
                @foreach($mpesa as $record)
                <tr>
                    <td>{{$record->payments->receipt}}</td>
                    <td>{{$record->payments->patients->full_name}}</td>
                    <td>{{$record->payments->users->profile->full_name}}</td>
                    <td>{{$record->amount}}</td>
                    <td>Cash(Mpesa)</td>
                    <td>{{(new Date($record->created_at))->format('jS M Y')}}</td>
                </tr>
                @endforeach
                @endif

                @if(isset($cheque))
                @foreach($cheque as $record)
                <tr>
                    <td>{{$record->payments->receipt}}</td>
                    <td>{{$record->payments->patients->full_name}}</td>
                    <td>{{$record->payments->users->profile->full_name}}</td>
                    <td>{{$record->amount}}</td>
                    <td>Cheque</td>
                    <td>{{(new Date($record->created_at))->format('jS M Y')}}</td>
                </tr>
                @endforeach
                @endif

                @if(isset($insurance))
                @if(!$insurance->isEmpty())
                @foreach($insurance as $inv)
                @foreach($inv->visits->investigations as $item)
                <?php
                $total_i+= $item->price;
                ?>
                <tr id="payment{{$item->id}}">
                    <td>{{$inv->invoice_no}}</td>
                    <td>{{$item->visits->patients?$item->visits->patients->full_name:'-'}}</td>
                    <td>
                        @if(!$inv->payments->isEmpty())
                        @foreach($inv->payments as $p)
                        {{$p->users->profile->full_name}}
                        @endforeach
                        @else
                        ** unpaid
                        @endif
                    </td>
                    <td>{{$item->price}}</td>
                    <td>
                        Insurance
                        @if(!$inv->payments->isEmpty())
                        (paid)
                        @else
                        (unpaid)
                        @endif
                    </td>
                    <td>{{smart_date_time($inv->created_at)}}</td>
                </tr>
                @endforeach
                @endforeach
                @endif
                @endif

            </tbody>
        </table>
        <div>
            <hr/>
            <h4>Period summary</h4>
            <dl class="dl-horizontal">
                <dt>Cash:</dt><dd>{{$cash?number_format($cash->sum('amount'),2):'0.00'}}</dd>
                <dt>Card:</dt><dd>{{$card?number_format($card->sum('amount'),2):'0.00'}}</dd>
                <dt>MPESA:</dt><dd>{{$mpesa?number_format($mpesa->sum('amount'),2):'0.00'}}</dd>
                <dt>Cheque:</dt><dd>{{$cheque?number_format($cheque->sum('amount'),2):'0.00'}}</dd>
                <dt>Insurance:</dt><dd>{{number_format($total_i,2)}}</dd>
                <?php
                $cash ? $total+=$cash->sum('amount') : '';
                $card ? $total+=$card->sum('amount') : '';
                $cheque ? $total+=$cheque->sum('amount') : '';
                $mpesa ? $total+=$mpesa->sum('amount') : '';
                $total+=$total_i;
                ?>
                <dt>Total Amount: {{number_format($total, 2)}}</dt><dd></dd>
            </dl>
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