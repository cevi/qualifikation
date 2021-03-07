@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin/">Dashboard</a></li>
            <li class="breadcrumb-item active">Klassifizierungen</li>
          </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Klassifizierungen</h1>
            </header>
            <div class="row">   
                <div class="col-sm-12">
                    @if ($classifications)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Created Date</th>
                                    <th scope="col">Updated Date</th>
                                </tr>
                            </thead>
                        @foreach ($classifications as $classification)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('classifications.edit',$classification->id)}}">{{$classification->name}}</a></td>
                                    <td>{{$classification->created_at ? $classification->created_at->diffForHumans() : 'no date'}}</td>
                                    <td>{{$classification->updated_at ? $classification->updated_at->diffForHumans() : 'no date'}}</td>
                                </tr>
                            </tbody>
                        @endforeach
                        </table>
                    
                    @endif

                </div>
            </div>
        </div>
    </section>
@endsection