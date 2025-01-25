<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-time JSON Sync</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        pre {
            background-color: #f4f4f4;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Real-time JSON Sync</h1>
    <pre id="output">Loading...</pre>
    <button onclick="fetchManualUpdate()">Manual Refresh</button>

    <script>
        let lastChecked = 0;
        let isFetching = false;
        let controller = null;

        async function fetchUpdates(manual = false) {
            if (isFetching && !manual) return;
            isFetching = true;

            if (manual && controller) {
                controller.abort();
            }

            controller = new AbortController();
            const signal = controller.signal;

            try {
                const url = `sync.php?lastChecked=${lastChecked}&manual=${manual}`;
                const response = await fetch(url, { signal });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                let result;
                try {
                    result = await response.json();
                } catch (error) {
                    console.error('Error decoding JSON:', error);
                    return;
                }

                if (result.success) {
                    document.getElementById('output').textContent = JSON.stringify(result.data, null, 2);
                    lastChecked = result.lastModified;
                }

                isFetching = false;

                if (!manual) fetchUpdates();
            } catch (error) {
                if (error.name === 'AbortError') {
                    console.log('Fetch aborted.');
                } else {
                    console.error('Error fetching updates:', error);
                    setTimeout(() => fetchUpdates(), 5000);
                }
                isFetching = false;
            }
        }

        async function fetchManualUpdate() {
            isFetching = false;
            await fetchUpdates(true);
        }

        fetchUpdates();
    </script>
</body>
</html>