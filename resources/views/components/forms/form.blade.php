<form method="{{ $spoofMethod ? 'POST' : $method }}" {!! $attributes !!} @if($enctype) enctype="{{ $enctype }}" @endif>
    @unless(in_array($method, ['HEAD', 'GET', 'OPTIONS']))
        @csrf
    @endunless
    
    @if($spoofMethod)
        @method($method)
    @endif
    
        {!! $slot !!}
</form>