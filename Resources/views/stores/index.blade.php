@extends('layouts.app')

@section('content_title','Internal stores')
@section('content_description','reports for internal stores')

@section('content')

    <div class="panel panel-info">
        <div class="panel-heading">Internal stores</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th>Store Name</th>
                            <th>Reports</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($stores as $store)
                            <tr>
                                <td>{{ $store->name }}</td>
                                <td>
                                    <a href="{{ url('reports/store/' .$store->id. '/stocks') }}" class="btn btn-primary btn-xs">Stock</a>
                                    <a href="{{ url('reports/store/' .$store->id. '/movement') }}" class="btn btn-primary btn-xs">Movement</a>
                                    <a href="{{ url('reports/store/' .$store->id. '/expiry') }}" class="btn btn-primary btn-xs">Expiry</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
