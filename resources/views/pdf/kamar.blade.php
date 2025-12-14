<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Kamar</title>
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
        <h2>DATA KAMAR ASRAMA MI QUR'AN AL-FALAH</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kamar</th>
                <th>Kapasitas</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $record->nama_kamar }}</td>
                <td>{{ $record->kapasitas }}</td>
                <td>{{ $record->jenis_kelamin }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer text-left">
        <p>Tanggal Export: {{ now()->format('d M Y') }}</p>
    </div>
</body>
</html>