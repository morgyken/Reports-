<?php
/*
 * Collabmed Solutions Ltd
 * Project: iClinic
 * Author: Samuel Okoth <sodhiambo@collabmed.com>
 */
extract($data);
$total_i = number_format(0, 2);
if (!isset($mode)) {
    $mode = null;
}
$start = Illuminate\Support\Facades\Input::get('start');
$end = Illuminate\Support\Facades\Input::get('end');
$total = 0; //number_format(0, 2);
$n = 0;

$laboratory_amount = 0;
$physio_amount = 0;
$theatre_amount = 0;
$diagnostics_amount = 0;
$radiology_amount = 0;
$pharmacy_amount = 0;
$optical_amount = 0;
$nursing_amount = 0;
$doctor_amount = 0;
$ultrasound_amount = 0;
?>
@extends('layouts.app')
@section('content_title','Revenue per Department')
@section('content_description','')

@section('content')
<div class="box box-info">
    <div class="box-body">
        @if(!$investigations->isEmpty())
        <div class="box-header">
            {!! Form::open()!!}
            <a href="" class="btn btn-xs btn-primary">Show all records</a>
            Clinic:
            <select name="clinic">
                @foreach($clinics as $item)
                <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
            </select>
            Mode:
            <select name="mode">
                <option></option>
                <option value="cash">Cash</option>
                <option value="insurance">Insurance</option>
            </select>
            Department:
            {!! Form::select('department',mconfig('reception.options.destinations'),null,['placeholder'=>'All'])!!}
            Start Date:
            <input type="text" id="date1" name="start" value="{{$start}}"/>
            End Date:
            <input type="text" id="date2" name="end" value="{{$end}}"/>
            <button  type="submit" id="clearBtn" class="btn btn-primary btn-xs"><i class="fa fa-filter"></i> Filter</button>
            {!! Form::close()!!}
        </div>

        <div class="alert alert-success">
            <i class="fa fa-info-circle"></i> {{filter_description($data['filter'])}}
            @if(isset($data['department']))
            {{$data['department']?', in '.$data['department']:''}}
            @endif
        </div>
        <table class="table table-condensed table-responsive table-striped" id="patients">
            <tbody id="result">
                @if($mode =='cash')
                <!--Cash Only-->
                @foreach($investigations as $item)
                @if(isset($item->visits))
                @if($item->visits->payment_mode!=='insurance')
                @if(isset($item->payments->batch->receipt))
                <?php
                $total+= $item->amount > 0 ? $item->amount : $item->price;
                if ($item->type == 'laboratory') {
                    $laboratory_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'physiotherapy') {
                    $physio_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'theatre') {
                    $theatre_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'diagnostics') {
                    $diagnostics_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'radiology') {
                    $radiology_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'optical') {
                    $optical_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'pharmacy') {
                    $pharmacy_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'nursing') {
                    $nursing_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'treatment') {
                    $doctor_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'doctor') {
                    $doctor_amount+=$item->amount > 0 ? $item->amount : $item->price;
                }
                ?>
                <tr id="payment{{$item->id}}">
                    <td>{{$n+=1}}</td>
                    <td>{{$item->payments?$item->payments->batch->receipt:''}}</td>
                    <td>
                        @if($item->type =='treatment')
                        Doctor
                        @else
                        {{ucfirst($item->type)}}
                        @endif
                    </td>
                    <td>
                        {{$item->procedures->name}}
                    </td>
                    <td>{{$item->visits->patients?$item->visits->patients->full_name:''}}</td>
                    <td>{{smart_date_time($item->payments?$item->payments->batch->created_at:'')}}</td>
                    <td>{{$item->amount>0?$item->amount:$item->price}}</td>
                    <td>{{$item->payments?$item->payments->batch->modes:''}}</td>
                </tr>
                @endif
                @endif
                @endif
                @endforeach<!--End Of Cash-->

                @elseif($mode =='insurance')
                <!--Insurance Only -->
                @if(!$insurance->isEmpty())
                @foreach($insurance as $inv)
                @foreach($inv->visits->investigations as $item)
                <?php
                $total_i+= $item->amount > 0 ? $item->amount : $item->price;
                ?>
                <tr id="payment{{$item->id}}">
                    <td>{{$n+=1}}</td>
                    <td>{{$inv->invoice_no}}</td>
                    <td>
                        @if($item->type =='treatment')
                        Doctor
                        @else
                        {{ucfirst($item->type)}}
                        @endif
                    </td>
                    <td>
                        $item->procedures->name
                    </td>
                    <td>{{$item->visits->patients?$item->visits->patients->full_name:'-'}}</td>
                    <td>{{smart_date_time($inv->created_at)}}</td>
                    <td>{{$item->amount>0?$item->amount:$item->price}}</td>
                    <td>
                        Insurance
                        @if(!$inv->payments->isEmpty())
                        (paid)
                        @else
                        (unpaid)
                        @endif
                    </td>
                </tr>
                @endforeach
                @endforeach
                @endif<!--End Of Insurance-->

                @else<!--Both cash and Insurance-->

                @foreach($investigations as $item)
                @if(isset($item->visits))
                @if($item->visits->payment_mode!=='insurance')
                @if(isset($item->payments->batch->receipt))
                <?php
                $total+= $item->amount > 0 ? $item->amount : $item->price;
                if ($item->type == 'laboratory') {
                    $laboratory_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'physiotherapy') {
                    $physio_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'theatre') {
                    $theatre_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'diagnostics') {
                    $diagnostics_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'radiology') {
                    $radiology_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'optical') {
                    $optical_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'pharmacy') {
                    $pharmacy_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'nursing') {
                    $nursing_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'treatment') {
                    $doctor_amount+=$item->amount > 0 ? $item->amount : $item->price;
                } elseif ($item->type == 'doctor') {
                    $doctor_amount+=$item->amount > 0 ? $item->amount : $item->price;
                }
                try {
                    ?>
                    <tr id="payment{{$item->id}}">
                        <td>{{$n+=1}}</td>
                        <td>
                            {{$item->payments?$item->payments->batch->receipt:''}}
                        </td>
                        <td>
                            @if($item->type =='treatment')
                            Doctor
                            @else
                            {{ucfirst($item->type)}}
                            @endif</td>
                        <td>{{$item->procedures->name}}</td>
                        <td>{{$item->visits->patients?$item->visits->patients->full_name:''}}</td>
                        <td>{{smart_date_time($item->created_at)}}</td>
                        <td>{{$item->amount>0?$item->amount:$item->price}}</td>
                        <td>{{$item->payments?$item->payments->batch->modes:''}}</td>
                    </tr>
                    <?php
                } catch (\Exception $e) {
                    ?>
                    <tr id="payment{{$item->id}}">
                        <td colspan="7"></td>
                    </tr>
                    <?php
                }
                ?>
                @endif
                @endif
                @endif
                @endforeach <!-- End of Cash-->

                @if(!$insurance->isEmpty())
                @foreach($insurance as $inv)
                @foreach($inv->visits->investigations as $item)
                <?php
                $total_i+= $item->amount > 0 ? $item->amount : $item->price;
                ?>
                <tr id="payment{{$item->id}}">
                    <td>{{$n+=1}}</td>
                    <td>{{$inv->invoice_no}}</td>
                    <td>
                        @if($item->type =='treatment')
                        Doctor
                        @else
                        {{ucfirst($item->type)}}
                        @endif
                    </td>
                    <td>{{$item->procedures->name}}</td>
                    <td>{{$item->visits->patients?$item->visits->patients->full_name:'-'}}</td>
                    <td>{{smart_date_time($item->created_at)}}</td>
                    <td>{{$item->amount>0?$item->amount:$item->price}}</td>
                    <td>
                        Insurance
                        @if(!$inv->payments->isEmpty())
                        (paid)
                        @else
                        (unpaid)
                        @endif
                    </td>
                </tr>
                @endforeach
                @endforeach
                @endif<!-- End of insurance-->

                @endif<!-- End of all-->
                <tr>
                    <td>{{$n+=1}}</td>
                    <td><strong>Totals:</strong></td>
                    <td style="text-align: right"><strong>Insurance:</strong></td>
                    <td><strong>{{number_format($total_i,2)}}</strong></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right"><strong>Cash:</strong></td>
                    <td><strong>{{number_format($total,2)}}</strong></td>
                    <td></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Receipt/Invoice</th>
                    <th>Department</th>
                    <th>Procedure</th>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Mode</th>
                </tr>
            </thead>
        </table>

        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="col-md-6 col-sm-12 col-lg-6">
                <hr/>
                <h4>Department summary</h4>
                <table class="table table-striped">
                    <tr>
                        <th>Laboratory</th>
                        <td style="text-align: left">
                            {{number_format($laboratory_amount,2)}}
                        </td>
                    </tr>
                    <tr>
                        <th>Radiology</th>
                        <td>{{number_format($radiology_amount,2)}}</td>
                    </tr>
                    <tr>
                        <th>Diagnostics</th>
                        <td>{{number_format($diagnostics_amount,2)}}</td>
                    </tr>
                    <tr>
                        <th>Physiotherapy</th>
                        <td>{{number_format($physio_amount,2)}}</td>
                    </tr>
                    <tr>
                        <th>Theatre</th>
                        <td>{{number_format($theatre_amount,2)}}</td>
                    </tr>

                    <tr>
                        <th>Nursing</th>
                        <td>{{number_format($nursing_amount,2)}}</td>
                    </tr>
                    <tr>
                        <th>Doctor</th>
                        <td>{{number_format($doctor_amount,2)}}</td>
                    </tr>
                    @if(m_setting('evaluation.eye'))
                    <tr>
                        <th>Optical</th>
                        <td>{{number_format($optical_amount,2)}}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        <hr>

        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="col-md-6 col-sm-12 col-lg-6">
                <hr/>
                <h4>Total</h4>
                <table class="table table-striped">
                    <tr>
                        <th>Cash | Credit Card | Mpesa | Cheque</th>
                        <td>{{number_format($total,2)}}</td>
                    </tr>
                    <tr>
                        <th>Insurance</th>
                        <td>{{number_format($total_i,2)}}</td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><strong>Total Amount:</strong></td>
                        <td><strong>{{number_format($total_i+$total,2)}}</strong> </td>
                    </tr>
                </table>
            </div>
        </div>

        @else
        <div class="alert alert-info">
            <p><i class="fa fa-info-circle"></i> No records found</p>
        </div>
        @endif

    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#date1").datepicker({dateFormat: 'yy-mm-dd', onSelect: function (date) {
                $("#date2").datepicker('option', 'minDate', date);
            }});
        $("#date2").datepicker({dateFormat: 'yy-mm-dd'});

        $('table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'excel', 'pdf', 'print'
            ]
        });

    });
</script>
@endsection