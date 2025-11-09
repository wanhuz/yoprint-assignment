<!DOCTYPE html>
<html>
<head>
    <title>YoPrint CSV Uploader</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('axios.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('index.css') }}">
</head>
<body>
    <div class="container">
        <div class="content">
            <form action="{{ route('uploads.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                @csrf
                <div class="upload-area" id="upload-area">
                    <span class="upload-text">Select file/Drag and drop</span>
                    <input type="file" name="file" id="file-input" required>
                    <button type="button" class="upload-button" onclick="document.getElementById('file-input').click()">
                        Upload File
                    </button>
                </div>

                    @if ($errors->any())
                        <div class="alert alert-error">
                            <strong>Error{{ count($errors) > 1 ? 's' : '' }}:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
            </form>

            <div class="table-container">
                <table id="uploads-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>File Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($uploads as $upload)
                            <tr>
                                <td>
                                    <div>{{ $upload->created_at->format('n-j-Y g:ia') }}</div>
                                    <div class="time-info">({{ $upload->created_at->diffForHumans() }})</div>
                                </td>
                                <td>{{ $upload->original_filename }}</td>
                                <td class="status-{{ strtolower($upload->status) }}">
                                    {{ $upload->status }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Handle file selection
        document.getElementById('file-input').addEventListener('change', function(e) {
            if (this.files.length > 0) {
                document.getElementById('upload-form').submit();
            }
        });

        // Drag and drop functionality
        const uploadArea = document.getElementById('upload-area');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('dragover');
            }, false);
        });

        uploadArea.addEventListener('drop', function(e) {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('file-input').files = files;
                document.getElementById('upload-form').submit();
            }
        });

        // Auto-refresh table
        setInterval(() => {
            axios.get('{{ route('uploads.refresh') }}')
                .then(res => {
                    const tbody = res.data.map(u => {
                        const date = new Date(u.created_at);
                        const statusClass = `status-${u.status.toLowerCase()}`;
                        return `
                            <tr>
                                <td>
                                    <div>${formatDate(date)}</div>
                                    <div class="time-info">(${getTimeAgo(date)})</div>
                                </td>
                                <td>${u.original_filename}</td>
                                <td class="${statusClass}">${u.status}</td>
                            </tr>
                        `;
                    }).join('');
                    
                    document.querySelector('#uploads-table tbody').innerHTML = tbody;
                })
                .catch(err => console.error('Error refreshing uploads:', err));
        }, 3000);

        function formatDate(date) {
            const month = date.getMonth() + 1;
            const day = date.getDate();
            const year = date.getFullYear();
            let hours = date.getHours();
            const minutes = date.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12 || 12;
            
            return `${month}-${day}-${year} ${hours}:${minutes}${ampm}`;
        }

        function getTimeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            
            if (seconds < 60) return `${seconds} seconds ago`;
            
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
            
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
            
            const days = Math.floor(hours / 24);
            return `${days} day${days !== 1 ? 's' : ''} ago`;
        }
    </script>
</body>
</html>