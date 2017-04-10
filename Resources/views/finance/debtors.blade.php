<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 *  Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
$records = $data['records'];
?>

@extends('layouts.app')
@section('content_title','Debtors')
@section('content_description','View detailed report for pending payments')

@section('content')
<div class="box box-info">
    <div class="box-header">
        <div class="pull-right">
            Start Date: <input type="text" id="date1">
            End Date: <input type="text" id="date2">
            <button id="clearBtn" class="btn btn-warning btn-xs">Clear</button>
        </div>
    </div>
    <div class="box-body">
        @if(!$records->isEmpty())
        <table id="cashier" class="table table-borderless">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Debt Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr>
                    <td>{{$record->visits->patients->full_name}}</td>
                    <td>{{$record->total}}</td>
                    <td><a href="{{route('system.report.debtors',$record->visits->patients->patient_id)}}"><i class="fa fa-eye-slash"></i>
                            View</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="alert alert-info">
            <p> <i class="fa fa-info-circle"></i> No previous payments.</p>
        </div>
        @endif
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#date1").datepicker({dateFormat: 'yy-mm-dd', onSelect: function (date) {
                $("#date2").datepicker('option', 'minDate', date);
                //fetch_table();
            }});
        $("#date2").datepicker({dateFormat: 'yy-mm-dd', onSelect: function (date) {
                //fetch_table();
            }});
        $('#clearBtn').click(function () {
            $("#date1").val('');
            $("#date2").val('');
            //fetch_table();
        });
        $('#cashier').DataTable({
            /* ajax: {
             url: "{{route('system.ajax.cashier_report')}}",
             dataSrc: 'data'
             }*/
        });
    });
</script>
@endsection