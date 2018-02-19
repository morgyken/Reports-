@extends('layouts.app')

@section('content_title','Internal stores')
@section('content_description','stock expiry report')

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
                            <th>Arrival Date</th>
                            <th>Expiry Date</th>
                            <th>Remaining Stock</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            @foreach($product as $item)
                                <tr>
                                    <td>{{ $item['product_name'] }}</td>
                                    <td>{{ $item['arrival_date'] }}</td>
                                    <td>{{ $item['expiry_date'] }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td><span class="text-info">expiring in </span> {{ $item['status'] }} days</td>
                                </tr>
                            @endforeach
                        @endforeach
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
