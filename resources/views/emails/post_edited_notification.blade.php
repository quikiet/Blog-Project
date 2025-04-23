<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f8fa;
            padding: 20px;
            color: #333;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2c3e50;
        }

        ul {
            padding-left: 20px;
        }

        li {
            margin-bottom: 10px;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .footer {
            font-size: 12px;
            color: #888;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>📝 Bài viết "{{ $post->title }}" đã được chỉnh sửa</h2>

        <p><strong>Tác giả:</strong> {{ $author->name }} ({{ $author->email }})</p>
        <p><strong>Thời gian chỉnh sửa:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <h3>📌 Các trường thay đổi:</h3>
        <ul>
            @foreach ($changedDetails as $field => $value)
                <li>
                    <strong>{{ ucfirst($field) }}:</strong><br>
                    <span style="color:#e74c3c">"{{ $value['old'] ?? 'null' }}"</span> ➜
                    <span style="color:#27ae60">"{{ $value['new'] ?? 'null' }}"</span>
                </li>
            @endforeach
        </ul>
        @if ($post->status === 'published')
            <a href="http://localhost:4200/posts/{{ $post->slug }}" class="btn" target="_blank">
                🔗 Xem bài viết
            </a>
        @endif

        <div class="footer">
            Đây là email thông báo tự động từ hệ thống. Vui lòng không trả lời.
        </div>
    </div>
</body>

</html>