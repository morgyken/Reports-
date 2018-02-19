@extends('layouts.app')

@section('content_title','Internal stores')
@section('content_description','stock report')

@section('content')
    <div class="panel panel-info">
        <div class="panel-heading">Internal stores</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Cost</th>
                            <th>Remaining Stock</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($report as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['cost']}}</td>
                                <td>{{ $item['stock']}}</td>
                                <td>{{ $item['value'] }}</td>
                            </tr>
                        @endforeach
                        <tr></tr>
                        <tr>
                            <td></td><td></td>
                            <td class="pull-right">Total Stock Value:</td>
                            <td>{{ $stockValue }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection
