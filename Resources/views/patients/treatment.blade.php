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
            {{--<div class="row col-md-8">--}}
            {{--<div class="col-md-2">--}}
            {{--Doctor:--}}
            {{--</div>--}}
            {{--<div class="col-md-4">--}}
            {{--<select class="form-control" id="clinician">--}}
            {{--@foreach($clinician as $item)--}}
            {{--<option value="{{$item->user}}">{{$item->name}}</option>--}}
            {{--@endforeach--}}
            {{--</select>--}}
            {{--</div>--}}
            {{--</div>--}}
            <table class="table table-striped">
                <tbody id="result">
                @foreach($diagnoses as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->visits->created_at->format('d/m/Y')}}</td>
                        <td>{{$item->visits->patients->id}}</td>
                        <td>{{$item->visits->patients->full_name}}</td>
                        <td>{{$item->visits->patients->age}}</td>
                        <td>{{$item->visits->patients->sex}}</td>
                        <td>{{$item->visits->patients->town}}</td>
                        <td>{{$item->visit_type}}</td>
                        <td>{{$item->diagnosis}}</td>
                        <td>{{$item->doctor->profile->name??'-'}}</td>
                    </tr>
                @endforeach
                </tbody>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Visit Date</th>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Residence</th>
                    <th>Visit Type</th>
                    <th>Diagnosis</th>
                    <th>Doctor</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <script type="text/javascript">
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