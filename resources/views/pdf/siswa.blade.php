<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Siswa</title>
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
                <th>NIS</th>
                <th>Nama Lengkap</th>
                <th>Panggilan</th>
                <th>Orang Tua</th>
                <th>Tanggal Lahir</th> <th>L/P</th>
                <th>WN</th>
                <th>Sts. Keluarga</th>
                <th>Sts. Ortu</th>
                <th>Anak Ke</th>
                <th>Tgl Masuk</th>
                <th>Kelas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $record->nis }}</td>
                <td>{{ $record->nama_lengkap }}</td>
                <td>{{ $record->nama_panggilan }}</td>
                <td>{{ $record->user?->name ?? '-' }}</td>
                
                <td>
                    {{ $record->tempat_lahir }}, <br>
                    {{ \Carbon\Carbon::parse($record->tanggal_lahir)->format('d M Y') }}
                </td>
                
                <td>{{ $record->jenis_kelamin }}</td>
                <td>{{ $record->kewarganegaraan }}</td>
                <td>{{ $record->status_keluarga }}</td>
                <td>{{ $record->status_orangtua }}</td>
                <td style="text-align: center;">{{ $record->anak_ke }}</td>
                <td>{{ \Carbon\Carbon::parse($record->tgl_masuk)->format('d M Y') }}</td>
                <td style="text-align: center;">{{ $record->kelas }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer text-left">
        <p>Tanggal Export: {{ now()->format('d M Y') }}</p>
    </div>
</body>
</html>