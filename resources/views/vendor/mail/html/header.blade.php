@props(['url'])
{{-- Logo als PNG (SVG zeigen viele Mail-Programme nicht), absolute Adresse und feste Grösse --}}
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ asset('img/logo-mail.png') }}" width="220" height="32" alt="{{ config('app.name') }}" style="border: 0; height: 32px; width: 220px;">
</a>
</td>
</tr>
