<table>
    <thead>
    <tr>
        <th>TN</th>
        <th>Leitende</th>
        <th>Rückmeldung</th>
    </tr>
    </thead>
    <tbody>
    @foreach($posts as $post)
        <tr>
            <td>{{ $post->campUser->user['username'] }}</td>
            <td>{{ $post->leader['username'] }}</td>
            <td>{{ $post->comment }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
