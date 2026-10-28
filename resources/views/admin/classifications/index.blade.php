@extends('layouts.admin')

@section('content')
    <div class="px-4 md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">   
            <div class="min-w-0 col-span-12 sm:col-span-12">
                @if ($classifications)
                    <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="list-table">
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
                    </div>
                
                @endif

            </div>
        </div>
    </div>
@endsection