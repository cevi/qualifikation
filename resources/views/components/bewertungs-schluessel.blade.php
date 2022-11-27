<h4>Bewertungsschlüssel</h4>
<table class="table">
    <thead>
    <tr>
        @foreach ($answers as $answer)
            <th>
                {{$answer['name']}}
            </th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    <tr>
        @foreach ($answers as $answer)
            <td>
                {{$answer['description']}}
            </td>
        @endforeach
    </tr>
    </tbody>
</table>
