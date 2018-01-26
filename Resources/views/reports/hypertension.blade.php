@extends('layouts.app')
@section('content_title','Hypertension Report')
@section('content_description', 'hypertension report')

@section('content')
    <div class="box box-info">
        <div class="box-body">
            <div class="box-header">
                <div class="col-md-12">
                    {!! Form::open()!!}
                        Start Date:
                        <input type="text" id="start" name="start" value="{{ $start }}" />
                        End Date:
                        <input type="text" id="end" name="end" value="{{ $end }}" />

                        <button type="submit" id="clearBtn" class="btn btn-primary btn-xs">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                        <a class="btn btn-xs btn-primary" href="{{ route('reports.patients.hypertension') }}">View all records</a>
                    {!! Form::close()!!}
                </div>
            </div>
            <table class="table table-striped">
                <tbody id="result">
                    @forelse($visits as $visit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $visit->created_at->format('d/m/Y') }}</td>
                            <td>{{ $visit->patients->id }}</td>
                            <td>{{ $visit->patients->full_name }}</td>
                            <td>{{ $visit->patients->mobile }}</td>
                            <td>{{ $visit->patients->age }}</td>
                            <td>{{ $visit->patients->sex }}</td>
                            <td>{{ $visit->patients->town }}</td>
                            <td>{{ $visit->visit_type }}</td>
                            <td>{{ @$visit->vitals->bp_systolic}}</td>
                            <td>{{ @$visit->vitals->bp_diastolic}}</td>
                            <td>{{ @$visit->vitals->weight}}</td>
                            <td>{{ $visit->diagnosis }}</td>
                            <td>
                                <?php
                                    $arr = [];
                                    foreach ($visit->prescriptions as $prescription) {
                                        $arr[] = $prescription->drugs->name;
                                    }
                                    echo implode(', ', $arr);
                                ?>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td>No results found</td>
                        </tr>
                    @endforelse
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
            $("#start").datepicker({
                dateFormat: 'yy-mm-dd', onSelect: function (date) {
                    $("#end").datepicker('option', 'minDate', date);
                }
            });
            $("#end").datepicker({dateFormat: 'yy-mm-dd'});

            $("#start").datepicker({dateFormat: 'yy-mm-dd'});

            $('table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection