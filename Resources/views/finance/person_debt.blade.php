<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 *  Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
$patient = $data['patient'];
$__visits = $patient->visits->where('payment_status', 'pending');
$de = $all = 0;
?>
@extends('layouts.app')
@section('content_title','Receive Payments')
@section('content_description','Receive payments from patients')


@section('content')
<div class="box box-info">
    <div class="box-body">
        Patient Name: <strong>{{$patient->full_name}}</strong>
        <br/>Total Amount:  <strong>Ksh <span id="total"></span></strong>
        <hr/>
        @if(!empty($patient->visits))
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Clinic</th>
                    <th>Visit</th>
                    <th>Procedure</th>
                    <th>Amount (Ksh)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($__visits as $visit)
                @if(!$visit->settled)
                @foreach($visit->treatments as $item)
                @if(!$item->fullyPaid)
                <tr>
                    <td>{{$visit->clinics->name}}</td>
                    <td>{{(new Date($visit->created_at))->format('jS M Y')}} </td>
                    <td>{{$item->procedures->name}}</td>
                    <td>{{$sum=$item->price*$item->no_performed}}</td>
                </tr>
                <?php
                $de++;
                $all+=$sum;
                ?>
                @endif
                @endforeach
                @endif
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
<script type="text/javascript">
    var sum = "{{$all}}";
    $('#total').html(sum);
</script>
<style type="text/css">
    #visits tbody tr.highlight { background-color: #B0BED9; }
</style>
@endsection-