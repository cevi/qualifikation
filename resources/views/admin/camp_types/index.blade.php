@extends('layouts.admin')

@section('content')
        <div class="container-fluid">
            <!-- Page Header-->
            <x-page-title :title="$title" :help="$help"/>
            <div class="row">
                <div class="col-sm-9">
                    @if ($camp_types)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Kursleiter</th>
                                </tr>
                            </thead>
                        @foreach ($camp_types as $camp_type)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('camp_types.edit',$camp_type)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$camp_type->name}}</a></td>
                                    <td>{{$camp_type->user ? $camp_type->user['username'] : ''}}</a></td>
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
