<h4 class="text-2xl font-bold dark:text-white">Bewertungsschlüssel</h4>
<ul class="w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white pb-1 my-4">
    @foreach ($answers as $answer)
        <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
            <div class="flex items-center ps-3 text-xl font-bold">
                {{$answer['name']}}
            </div>
            <div class="flex items-center ps-3">
                {{$answer['description']}}
            </div>
        </li>
    @endforeach
</ul>
