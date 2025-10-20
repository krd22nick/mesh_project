<!DOCTYPE html>
<html>
<head>
    <title>Excel Upload</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<div>
    <h1>Upload Excel File</h1>
    <form id="upload-form" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".xlsx,.xls" required>
        <button type="submit">Upload</button>
    </form>
    <div id="progress"></div>
    <div id="rows"></div>
</div>

<script>
    document.getElementById('upload-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const response = await fetch('/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: formData,
        });
        const data = await response.json();
        pollProgress(data.file_key);
    });

    async function pollProgress(fileKey) {
        const response = await fetch(`/progress?file_key=${fileKey}`);
        const data = await response.json();
        document.getElementById('progress').innerText = `Processed ${data.progress} of ${data.total_rows} rows`;
        if (data.progress < data.total_rows) {
            setTimeout(() => pollProgress(fileKey), 1000);
        } else {
            fetchRows();
        }
    }

    async function fetchRows() {
        const response = await fetch('/rows');
        const data = await response.json();
        document.getElementById('rows').innerHTML = JSON.stringify(data, null, 2);
    }

    window.Echo.channel('rows').listen('.row.created', (e) => {
        console.log('New row created:', e.row);
        fetchRows();
    });
</script>
</body>
</html>
