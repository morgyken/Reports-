
@extends('layouts.app')
@section('content_title','Laboratory Procedures')
@section('content_description','Lab procedures performed')

@section('content')
    <div class="box box-info">
        <div class="box-body">
            <div class="box-header">
                <div class="col-md-12">
                    {!! Form::open()!!}
	                    Start Date:
	                    <input type="text" id="start" name="filters[start]" value="{{ $dateFilters['start'] }}" />
	                    End Date:
	                    <input type="text" id="end" name="filters[end]" value="{{ $dateFilters['end'] }}" />
	                    <button type="submit" id="clearBtn" class="btn btn-primary btn-xs" name="filter">
	                        <i class="fa fa-filter"></i> Filter
	                    </button>
                    {!! Form::close()!!}
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