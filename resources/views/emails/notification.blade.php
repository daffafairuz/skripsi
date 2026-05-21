<!DOCTYPE html>
<html>
<body>

<h2>Smart Aquaponic Notification</h2>

<p>{{ $notification->message }}</p>

<hr>

<p>
Type:
{{ strtoupper($notification->type) }}
</p>

<p>
Site:
{{ $notification->site->name }}
</p>

<p>
Time:
{{ $notification->created_at }}
</p>

</body>
</html>