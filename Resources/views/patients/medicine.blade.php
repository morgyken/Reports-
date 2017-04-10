<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 * Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
extract($data);
?>
@extends('layouts.app')
@section('content_title','Drugs Given')
@section('content_description','Analytics for pdrugs given')

@section('content')
<div class="box box-info">
    <div class="box-body">
        <table class="table table-striped">
            <tbody>
                @foreach($medication as $medicine)
                <tr>
                    <td>{{$medicine->id}}</td>
                    <td>{{$medicine->visits->patients->full_name}}</td>
                    <td>{{$medicine->dose}}</td>
                    <td>{{$medicine->visits->clinics->name}}</td>
                    <td>{{smart_date($medicine->created_at)}}</td>
                </tr>
                @endforeach
            </tbody>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Drug</th>
                    <th>Clinic</th>
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