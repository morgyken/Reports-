<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 * Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
extract($data);
$start = Illuminate\Support\Facades\Input::get('start');
$end = Illuminate\Support\Facades\Input::get('end');
$total = 0;

?>

@extends('layouts.app')
@section('content_title','Revenue Per Doctor')
@section('content_description','')

@section('content')
    <div class="box box-info">
        <div class="box-body">
            @if(!$sales->isEmpty())
                <div class="box-header">
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

                <div class="alert alert-success">
                    <i class="fa fa-info-circle"></i>
                    {{filter_description($data['filter'])}}
                </div>

                <table class="table table-condensed table-responsive table-striped" id="data">
                    <tbody>
                    @foreach($sales as $sale)
                        <?php $total += $sale->total;?>
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$sale->products->name}}</td>
                            <td>{{$sale->quantity}}</td>
                            <td>{{$sale->price}}</td>
                            <td>{{$sale->total}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Units</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                </table>
                <hr>
                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="col-md-6">
                        <table class="table table-striped">
                            <tr>
                                <td><strong>Sum: </strong></td>
                                <td><strong>{{number_format($total,2)}}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <p><i class="fa fa-info-circle"></i> No sales records found</p>
                </div>
            @endif
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