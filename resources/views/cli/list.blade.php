<table>
    <thead>
        <tr>
            Name
        </tr>
    </thead>
    <tbody>
    @foreach($checks as $check)
        <tr>
            <td>
                {{ $check }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
