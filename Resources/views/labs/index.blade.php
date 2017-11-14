
@extends('layouts.app')
@section('content_title','Laboratory Procedures')
@section('content_description','Lab procedures performed')

@section('content')
    <div class="box box-info">
        <div class="box-body">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::open(['class'=>'form-inline'])!!}
                            <label>Start Date:</label>
                            <input type="text" id="start" name="filters[date][start]" class="form-control" value="{{ $dateFilters['start'] }}" />
                            <label>End Date:</label>
                            <input type="text" id="end" name="filters[date][end]" class="form-control" value="{{ $dateFilters['end'] }}" />
                            <label>Age:</label>

                            <select name="filters[age]" class="form-control">
                                <option {{ $ageFilters == 'all' ? 'selected' : '' }} value="all">All</option>
                                <option {{ $ageFilters == 5 ? 'selected' : '' }} value="5">5 years &amp; under</option>
                                <option {{ $ageFilters == 6 ? 'selected' : '' }} value="6">Over 5 years</option>
                            </select> 
                            <button type="submit" id="clearBtn" class="btn btn-primary btn-sm" name="filter">
                                <i class="fa fa-filter"></i> Apply Filters 
                            </button>
                        {!! Form::close()!!}
                    </div>
                </div>
            </div>

	        <table class="table table-striped">
	        	<thead>
	                <tr>
	                    <th>#</th>
	                    <th>Procedure</th>
	                    <th>Total count</th>
	                </tr>
	            </thead>

	            <tbody>
	                @foreach($investigations as $procedure => $count)
	                	<tr>
	                		<td></td>
	                		<td>{{ $procedure }}</td>
	                		<td>{{ $count }}</td>
	                	</tr>
	                @endforeach
	            </tbody>
	            
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