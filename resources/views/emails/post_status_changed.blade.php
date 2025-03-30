<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật trạng thái bài viết</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0" border="0"
        style="max-width: 600px; background-color: #fff; padding: 20px; border-radius: 10px;">
        <tr>
            <td align="center" style="padding-bottom: 20px;">
                <h2 style="color: #333;">Cập Nhật Trạng Thái Bài Viết</h2>
            </td>
        </tr>

        <tr>
            <td style="padding: 10px 0; text-align: center;">
                <h3 style="margin: 0; font-size: 18px;">Tiêu đề: {{ $post->title }}</h3>
                <p style="margin: 5px 0; font-size: 14px;">Tác giả: {{ $user->name }}</p>
            </td>
        </tr>

        <tr>
            <td align="center" style="padding: 20px;">
                @if($post->status === 'published')
                    <span style="color: #28a745; font-size: 18px; font-weight: bold;">Công khai ✅</span>
                @elseif($post->status === 'pending')
                    <span style="color: #ffc107; font-size: 18px; font-weight: bold;">Chờ duyệt ⏳</span>
                @elseif($post->status === 'rejected')
                    <span style="color: #dc3545; font-size: 18px; font-weight: bold;">Bị từ chối ❌</span>
                @elseif($post->status === 'draft')
                    <span style="color: #6c757d; font-size: 18px; font-weight: bold;">Nháp 📝</span>
                @elseif($post->status === 'scheduled')
                    <span style="color: #6c757d; font-size: 18px; font-weight: bold;">Đã lên lịch ngày:
                        {{ $post->published_at }}</span>
                @else
                    <span style="color: #007bff; font-size: 18px; font-weight: bold;">{{ ucfirst($post->status) }}</span>
                @endif
            </td>
        </tr>

        <tr>
            <td style="padding: 20px; text-align: center;">
                <a href="{{ url('/posts/' . $post->slug) }}"
                    style="text-decoration: none; background-color: #007bff; color: white; padding: 10px 20px; border-radius: 5px; display: inline-block;">Xem
                    bài viết</a>
            </td>
        </tr>

        <tr>
            <td style="text-align: center; padding-top: 20px; font-size: 12px; color: #666;">
                <p>Bạn nhận được email này vì bạn là tác giả của bài viết.</p>
            </td>
        </tr>
    </table>

</body>

</html>