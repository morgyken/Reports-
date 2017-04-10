<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 * Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
extract($data);
?>
@extends('layouts.app')
@section('content_title','Performed Procedures')
@section('content_description','Analytics for procedures performed')

@section('content')
<div class="box box-info">
    <div class="box-body">
        <div id="chart_space"></div>
        <table class="table table-striped">
            <tbody>
                @foreach($investigations as $procedure)
                <tr>
                    <td>{{$procedure->id}}</td>
                    <td>{{$procedure->procedures->name}}</td>
                    <td>{{$procedure->visits->patients->full_name}}</td>
                    <td>{{smart_date_time($procedure->created_at)}}</td>
                    <td>{{$procedure->visits->clinics->name}}</td>
                    <td>{{ucfirst($procedure->type)}}</td>
                    <td>{{$procedure->pesa}}</td>
                    <td>{{$procedure->visits->payment_mode}}</td>
                </tr>
                @endforeach
            </tbody>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Procedure</th>
                    <th>Patient</th>
                    <th>Date / Time</th>
                    <th>Clinic</th>
                    <th>Department</th>
                    <th>Revenue</th>
                    <th>Mode</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@barchart('procedureCharts', 'chart_space')
<script type="text/javascript">
    $(function () {
        $('table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'excel', 'pdf'
            ]
        });
    });
</script>
@endsection