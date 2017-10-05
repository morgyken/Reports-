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
            <table class="table table-striped">
                <tbody id="result">
                @foreach($diagnoses as $item)
                    <?php
                    $_v = $item->visits;
                    if (empty($_v)) {
                        continue;
                    }
                    $_p = $_v->patients;
                    if (empty($_p)) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$_v->created_at->format('d/m/Y')}}</td>
                        <td>{{$_p->id}}</td>
                        <td>{{$_p->full_name}}</td>
                        <td>{{$_p->age}}</td>
                        <td>{{$_p->sex}}</td>
                        <td>{{$_p->town}}</td>
                        <td>{{$item->visit_type}}</td>
                        <td>{{$item->diagnosis}}</td>
                        <td>{{$item->doctor->profile->name}}</td>
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
                pageLength: 50,
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection