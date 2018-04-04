@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="text-right">
                <a href="{{ url('allfile') }}" class="btn btn-danger btn-sm">Kembali</a>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Unduh</div>
                <div class="panel-body">
                    <div class="alert alert-warning">
                        Anda Akan Mengunduh File <strong>{{$file->name}}</strong>
                        <p>Silahkan periksa email Anda untuk mendapatkan OTP.</p>
                        <p>Hasil enkripsi OTP 
                            <?php 
                                $ex = explode('.', $time);
                                $depan = $ex[0];
                                if(count($ex) > 1) {
                                $belakang = substr($ex[1], 0,13);
                                echo $depan.','.$belakang;
                                } else {
                                echo $depan;
                                } 
                            ?>
                        </p>
                    </div>
                    <div class="text-center">
                        <form class="form-inline" method="POST" action="{{action('HomeController@actUnduh')}}">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$file->id}}">
                            <div class="form-group">
                                <label class="sr-only" for="exampleInputAmount">Amount (in dollars)</label>
                                <div class="input-group">
                                    <div class="input-group-addon">OTP</div>
                                    <input type="text" class="form-control" required="required" name="otp">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">UNDUH</button>
                        </form>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
