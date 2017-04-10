
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