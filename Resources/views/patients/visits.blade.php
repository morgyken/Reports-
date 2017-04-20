<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 * Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
extract($data);
?>
@extends('layouts.app')
@section('content_title','Patient Visits')
@section('content_description','Analytics for patient visits')

@section('content')
<div class="box box-info">
    <div class="box-body">
        <div id="chart_space"></div>
        <table class="table table-striped">
            <tbody>
                @foreach($visits as $visit)
                <tr>
                    <td>{{$visit->id}}</td>
                    <td>{{$visit->patients->full_name}}</td>
                    <td>{{$visit->clinics->name}}</td>
                    <td>{{$visit->visit_destination}}</td>
                    <td>{{$visit->mode}}</td>
                    <td>{{smart_date($visit->created_at)}}</td>
                </tr>
                @endforeach
            </tbody>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Clinic</th>
                    <th>Department</th>
                    <th>Payment Mode</th>
                    <th>Date</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
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