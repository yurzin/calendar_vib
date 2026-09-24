<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Новая заявка</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #1e293b;">
    <h2 style="margin: 0 0 16px;">Новая заявка «Хочу в календарь»</h2>
    <table cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
        <tr><td style="color: #64748b;">Имя</td><td>{{ $lead->name }}</td></tr>
        <tr><td style="color: #64748b;">Компания</td><td>{{ $lead->company }}</td></tr>
        <tr><td style="color: #64748b;">Телефон</td><td><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></td></tr>
        @if ($lead->source)
            <tr><td style="color: #64748b;">Страница</td><td>{{ $lead->source }}</td></tr>
        @endif
        <tr><td style="color: #64748b;">Дата</td><td>{{ $lead->created_at->timezone('Asia/Novokuznetsk')->format('d.m.Y H:i') }}</td></tr>
    </table>
</body>
</html>
