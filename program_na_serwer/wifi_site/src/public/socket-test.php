<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update example</title>
</head>
<script src="https://code.jquery.com/jquery-1.11.3.js"></script>
<body>

<p>deviceId: <input type="text" value="" placeholder="deviceId" id="deviceId" name="deviceId"/></p>
<p>optionId: <input type="text" value="" placeholder="optionId" id="optionId" name="optionId"/></p>
<p>value: <input type="text" value="" placeholder="value" name="value" id="value" /></p>
<button id="sendUpdate">Send TEST Update</button>

<script>

    $(document).ready(function () {
        var conn = new WebSocket('ws://localhost:8888/sendUpdate');
        conn.onopen = function (e) {
            console.log("Connection established!");
        };

        conn.onmessage = function (e) {
            console.log(e.data);
        };

        $('#sendUpdate').click(function() {
            var deviceId = $('#deviceId').val();
            var optionId = $('#optionId').val();
            var value = $('#value').val();
            var json = {'deviceId': deviceId, 'optionId': optionId, 'value': value, 'updated_at': '2017-02-03T10:38:46.000+01:00'};
            conn.send(JSON.stringify(json));
        });

    });

</script>
</body>
</html>
