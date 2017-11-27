<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 * Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
extract($data);
?>
@extends('layouts.app')
@section('content_title','Patient Contacts')
@section('content_description','Analytics for patient')

@section('content')
    <div class="box box-info">
        <div class="box-body">
            <div id="chart_space"></div>
            <table class="table table-striped">
                <tbody>
                @foreach($patients as $patient)
                    <tr>
                        <td>{{$patient->id}}</td>
                        <td>{{$patient->full_name}}</td>
                        <td>{{$patient->mobile}}</td>
                    </tr>
                @endforeach
                </tbody>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Contact</th>
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
                    'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection