<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 * Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
extract($data);
?>
@extends('layouts.app')
@section('content_title','Performed Diagnoses')
@section('content_description','Analytics for patient treatement')

@section('content')
<div class="box box-info">
    <div class="box-body">
        <div class="row col-md-8">
            <div class="col-md-2">
                Clinic:
            </div>
            <div class="col-md-4">
                <select class="form-control" id="clinic">
                    @foreach($clinics as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                Doctor:
            </div>
            <div class="col-md-4">
                <select class="form-control" id="clinician">
                    @foreach($clinician as $item)
                    <option value="{{$item->user}}">{{$item->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <table class="table table-striped">
            <tbody id="result">
                @foreach($investigations as $procedure)
                <?php
                // try {
                // $procedure->visits->patients;
                ?>
                <tr>
                    <td>{{$procedure->id}}</td>
                    <td>{{$procedure->procedures->name}}</td>
                    <td>{{$procedure->visits?$procedure->visits->patients->first_name:''}}</td>
                    <td>{{smart_date_time($procedure->created_at)}}</td>
                    <td>{{$procedure->visits?$procedure->visits->clinics->name:''}}</td>
                    <td>{{$procedure->doctors?$procedure->doctors->profile->full_name:''}}</td>
                    <td>{{$procedure->pesa}}</td>
                    <td>{{$procedure->visits?$procedure->visits->payment_mode:''}}</td>
                </tr>
                <?php
                //  } catch (\Exception $ex) {
                //  }
                ?>
                @endforeach
            </tbody>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Procedure</th>
                    <th>Patient</th>
                    <th>Date / Time</th>
                    <th>Clinic</th>
                    <th>Clinician</th>
                    <th>Revenue</th>
                    <th>Mode</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">

    $('#clinic').change(function () {
        FetchDiagnoses('clinic', this.value);
    });

    $('#clinician').change(function () {
        FetchDiagnoses('clinician', this.value);
    });

    function FetchDiagnoses(type, value) {
        $(document).ready(function () {
            $.ajax({
                type: 'get',
                url: "{{route('api.reports.diagnoses.clinic')}}",
                data: {'type': type, 'value': value},
                dataType: 'html',
                success: function (response) {
                    $('#result').html(response);
                }
            });
        });
    }

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