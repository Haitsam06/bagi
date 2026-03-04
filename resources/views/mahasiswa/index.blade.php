<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa</title>
</head>
<body>
    <h1>Data Mahasiswa</h1>
    <table border="1" rules="all" cellpadding="5" cellspacing="0">
        <thead>
             <tr>
                <th>Nama</th>
                <th>NIM</th>
                <th>Jenis Kelamin</th>
                <th>Usia</th>
                <th>Prodi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mahasiswa as $mhs)
            <tr>
                <td>{{ $mhs->nama }}</td>
                <td>{{ $mhs->nim }}</td>
                <td>{{ $mhs->jenis_kelamin }}</td>
                <td>{{ $mhs->usia }}</td>
                <td>{{ $mhs->prodi['nama'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>