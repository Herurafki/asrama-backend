<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Orang Tua</title>
    <style>
        body { 
                font-family: sans-serif;
                font-size: 10px; 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            page-break-inside: auto; 
        }  

        tr { 
            page-break-inside: avoid; 
            page-break-after: auto; 
            
        }

        th, td { 
            border: 1px solid #161515; 
            padding: 4px; 
            text-align: left; 
            vertical-align: top; 
        }

        th { 
            background: #009933; 
            font-color: white;
            font-weight: bold; 
        }
        
        /* Utilitas untuk header */
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 2px 0; color: #666; font-size: 12px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>DATA SANTRIWAN/SANTRIWATI ASRAMA MI QUR'AN AL-FALAH</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Ayah</th>
                <th>Pendidikan Ayah</th>
                <th>Pekerjaan Ayah</th>
                <th>Nama Ibu</th>
                <th>Pendidikan Ibu</th> 
                <th>Pekerjaan Ibu</th>
                <th>Alamat</th>
                <th>Nama Wali</th>
                <th>Pekerjaan Wali</th>
                <th>Alamat Wali</th>
                <th>No Hp</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $record->nama_ayah }}</td>
                <td>{{ $record->pendidikan_ayah }}</td>
                <td>{{ $record->pekerjaan_ayah }}</td>
                <td>{{ $record->nama_ibu }}</td>
                <td>{{ $record->pendidikan_ibu }}</td> 
                <td>{{ $record->pekerjaan_ibu }}</td>
                <td>{{ $record->alamat }}</td>
                <td>{{ $record->nama_wali }}</td>
                <td>{{ $record->pekerjaan_wali }}</td>
                <td>{{ $record->alamat_wali }}</td>
                <td>{{ $record->no_hp }}</td>
                <td>{{ optional($record->user)->email }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer text-left">
        <p>Tanggal Export: {{ now()->format('d M Y') }}</p>
    </div>
</body>
</html>