
@extends('layouts.app')
@section('content_title','Client Reports')
@section('content_description','Client reports data')

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
	                    <th>Department</th>
	                    <th>Total count</th>
	                </tr>
	            </thead>

	            <tbody>
	                @foreach($clients as $department => $collection)
	                	<tr>
	                		<td></td>
	                		<td>{{ $department }}</td>
	                		<td>{{ count($collection) }}</td>
	                	</tr>
	                @endforeach
                    <tr>
                        <td></td>
                        <td><b>TOTAL</b></td>
                        <td>{{ $total }}</td>
                    </tr>
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