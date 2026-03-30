@props([
    'text', 
    'hasSurveys', 
    'url' => 'javascript:;', 
    'btnClass' => 'opensurvey',
    'target' => '_self'
])

<div class="col-sm-4" style="margin-bottom: 10px;">
    @if($hasSurveys)
        <a href="{{ $url }}" target="{{ $target }}" class="focus:outline-hidden text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900 {{ $btnClass }}" role="button">{{ $text }}</a>
    @else
        <span title="Bitte zuerst auf 'Qualifikationen erstellen' klicken" style="display: inline-block; cursor: not-allowed;">
            <button disabled class="focus:outline-hidden text-white bg-purple-700 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600" style="pointer-events: none; opacity: 0.5; display: inline-block;">{{ $text }}</button>
        </span>
    @endif
</div>
