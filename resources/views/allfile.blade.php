@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if (session('status'))
                <div class="alert alert-danger">
                    {{ session('status') }}
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">Semua File</div>

                <div class="panel-body">
                    <table class="table"> 
                        <thead> 
                            <tr> 
                                <th>No</th> 
                                <th>Nama File</th> 
                                <th>Tanggal</th>
                                <th></th>
                            </tr> 
                        </thead> 
                        <tbody>
                            @foreach ($files as $i => $file)
                                <tr>
                                    <th scope="row">{{$i+1}}</th> 
                                    <td>{{$file->name}}</td> 
                                    <td>{{date('d F Y H:i',strtotime($file->created_at))}}</td>
                                    <td><a href="{{ url('unduh/'.$file->id) }}" class="btn btn-success btn-xs">Unduh</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <center>
                        {{$files->links()}}
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
