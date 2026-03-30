@props(['text', 'hasSurveys'])

<div class="col-sm-4" style="margin-bottom: 10px;">
    @if($hasSurveys)
        <a href="javascript:;" class="focus:outline-hidden text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900 opensurvey" role="button">{{ $text }}</a>
    @else
        <span title="Bitte zuerst auf 'Qualifikationen erstellen' klicken" style="display: inline-block; cursor: not-allowed;">
            <a href="javascript:;" class="focus:outline-hidden text-white bg-purple-700 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600" style="pointer-events: none; opacity: 0.5; text-decoration: none; display: inline-block;" role="button" aria-disabled="true">{{ $text }}</a>
        </span>
    @endif
</div>
