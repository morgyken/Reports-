<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 * Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
extract($data);
$start = Illuminate\Support\Facades\Input::get('start');
$end = Illuminate\Support\Facades\Input::get('end');
?>
@extends('layouts.app')
@section('content_title','Clinic Report')
@section('content_description',$clinic.' report')

@section('content')
    <div class="box box-info">
        <div class="box-body">
            <div class="box-header">
                <div class="col-md-12">
                    {!! Form::open()!!}
                    Start Date:
                    <input type="text" id="date1" name="start" value="{{$start}}"/>
                    End Date:
                    <input type="text" id="date2" name="end" value="{{$end}}"/>
                    <button type="submit" id="clearBtn" class="btn btn-primary btn-xs">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                    <a class="btn btn-xs btn-primary" href="">View all records</a>
                    {!! Form::close()!!}
                </div>
            </div>
            <div class="alert alert-success">
                <i class="fa fa-info-circle"></i>
                {{filter_description($data['filter'])}}
            </div>
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
                        <td>{{$_p->mobile}}</td>
                        <td>{{$_p->age}}</td>
                        <td>{{$_p->sex}}</td>
                        <td>{{$_p->town}}</td>
                        <td>{{$item->visit_type}}</td>
                        <td>{{@$_v->vitals->bp_systolic}}</td>
                        <td>{{@$_v->vitals->bp_diastolic}}</td>
                        <td>{{@$_v->vitals->weight}}</td>
                        <td>{{$item->diagnosis}}</td>
                        <td>
                            <?php
                            $arr = [];
                            foreach ($_v->prescriptions as $p) {
                                $arr[] = $p->drugs->name;
                            }
                            echo implode(', ', $arr);
                            ?>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Visit Date</th>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Phone</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Residence</th>
                    <th>Visit Type</th>
                    <th>BP Systolic</th>
                    <th>BP Diastolic</th>
                    <th>Weight</th>
                    <th>Diagnosis</th>
                    <th>Medications</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#date1").datepicker({
                dateFormat: 'yy-mm-dd', onSelect: function (date) {
                    $("#date2").datepicker('option', 'minDate', date);
                }
            });
            $("#date2").datepicker({dateFormat: 'yy-mm-dd'});

            $("#date").datepicker({dateFormat: 'yy-mm-dd'});

            $('table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection