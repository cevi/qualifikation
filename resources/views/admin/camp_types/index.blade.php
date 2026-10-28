@extends('layouts.admin')

@section('content')
        <div class="px-[15px] md:px-12">
            <!-- Page Header-->
            <x-layouts.page-title :title="$title" :help="$help"/>
            <div class="grid grid-cols-12 sm:gap-x-8">
                <div class="min-w-0 col-span-12 sm:col-span-9">
                    @if ($camp_types)
                        <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="list-table">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Kursleiter</th>
                                </tr>
                            </thead>
                        @foreach ($camp_types as $camp_type)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('admin.camp_types.edit',$camp_type)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$camp_type->name}}</a></td>
                                    <td>{{$camp_type->user ? $camp_type->user['username'] : ''}}</a></td>
                                </tr>
                            </tbody>
                        @endforeach
                        </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
