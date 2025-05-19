<!DOCTYPE html>
<html>
<head>
    <title>Subscription test</title>
</head>
<body>
<form id="myForm">
    @csrf {{-- Blade directive to generate hiden field with CSRF-token (419 http error), not applicable here, because SESSION_DRIVER=array --}}
    <table style="margin: 0 auto; width: 100%; max-width: 400px;">
        <tr>
            <td colspan="2" style="text-align: center; padding-top: 10px;">
                <h1>Subscribe to receive weather in your city</h1>
            </td>
        </tr>
        <tr>
            <td style="text-align: right;"><label for="email">Email:</label></td>
            <td style="padding-left: 10px; width: 100%;"><input type="email" id="email" name="email" required style="width: 100%; box-sizing: border-box;"></td>
        </tr>
        <tr>
            <td style="text-align: right;"><label for="city">City:</label></td>
            <td style="padding-left: 10px; width: 100%;"><input type="text" id="city" name="city" required style="width: 100%; box-sizing: border-box;"></td>
        </tr>
        <tr>
            <td style="text-align: right;"><label for="frequency">Frequency:</label></td>
            <td style="padding-left: 10px; width: 100%;">
                <select id="frequency" name="frequency" required style="width: 100%;">
                    <option value="hourly">hourly</option>
                    <option value="daily">daily</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; padding-top: 10px;">
                <button type="submit">Subscribe</button>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; padding-top: 10px;">
                <div id="response" style="color:red;"></div>
            </td>
        </tr>
    </table>
</form>

<script>
    document.getElementById('myForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new URLSearchParams(new FormData(form));
        const apiUrl = '/api/subscribe';

        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData
        })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        {{--let errorMessage = 'HTTP error! status: ' + response.status;--}}
                        if (data && data.message) {
                            let errorMessage = data.message;
                            throw new Error(errorMessage);
                        }

                        let errorMessage = 'HTTP error, code: ' + response.status;
                        throw new Error(errorMessage);
                    }
                    return data;
                });
            })
            .then(data => {
                const responseDiv = document.getElementById('response');
                if (data && data.message) {
                    responseDiv.textContent = data.message;
                } else {
                    responseDiv.textContent = 'Warning: message not found';
                }
            })
            .catch(error => {
                const responseDiv = document.getElementById('response');
                responseDiv.textContent = error.message;
            });
    });
</script>

</body>
</html>
