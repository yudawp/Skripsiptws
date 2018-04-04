@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Statistik</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <center>
                        <h1>Total File</h1>
                        <h2>{{number_format($files)}}</h2>
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
