<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Rolle</th>
        <th>Leiter</th>
        <th>Klassifizierung</th>
    </tr>
    </thead>
    <tbody>
    @foreach($camp_users as $camp_user)
        <tr>
            <td>{{ $camp_user->user['username'] }}</td>
            <td>{{ $camp_user->user['email'] }}</td>
            <td>{{ $camp_user->role['name'] }}</td>
            <td>{{ $camp_user->leader ? $camp_user->leader['username'] : '' }}</td>
            <td>{{ $camp_user->classification ? $camp_user->classification['name'] : '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
