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
            <td style="text-align: center; padding-top: 10px;">
                <h1>Subscription info</h1>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; padding-top: 10px;">
                <div id="response" style="color:red;"></div>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; padding-top: 10px;">
                <div id="unsubscribe-container" style="display: none;">
                    <a href="#" id="unsubscribe-link">Unsubscribe</a>
                </div>
                <div id="subscribe-container" style="display: none;">
                    <a href="/subscribe">Subscribe</a>
                </div>
            </td>
        </tr>
    </table>
</form>

<script>
    window.onload = function () {
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        const unsubscribe = urlParams.get('unsubscribe');

        const responseDiv = document.getElementById('response');
        const subscribeContainer = document.getElementById('subscribe-container');
        const unsubscribeContainer = document.getElementById('unsubscribe-container');
        const unsubscribeLink = document.getElementById('unsubscribe-link');

        if (token) {
            let url = `/api/confirm/${token}`;
            if (unsubscribe && unsubscribe === 'y'){
                url = `/api/unsubscribe/${token}`;
            }

            fetch(url)
                .then(response => {
                    return response.json().then(data => {
                        if (response.ok) {
                            if(unsubscribe && unsubscribe === 'y'){
                                unsubscribeContainer.style.display = 'none';
                                subscribeContainer.style.display = 'block';
                            } else{
                                unsubscribeContainer.style.display = 'block';
                                subscribeContainer.style.display = 'none';

                                const urlObject = new URL(window.location.href);
                                urlObject.searchParams.set('unsubscribe', 'y');
                                unsubscribeLink.href = urlObject.href;
                            }

                            if (data && data.message) {
                                responseDiv.textContent = data.message;
                            } else {
                                responseDiv.textContent = 'Message not found';
                            }
                        } else {
                            unsubscribeContainer.style.display = 'none';
                            subscribeContainer.style.display = 'block';
                            if (data && data.message) {
                                responseDiv.textContent = data.message;
                            } else {
                                responseDiv.textContent = `Error: ${response.status}  ${response.statusText}`;
                            }
                        }
                    });
                })
                .catch(error => {
                    responseDiv.textContent = 'Request error';
                    unsubscribeContainer.style.display = 'none';
                    subscribeContainer.style.display = 'block';
                });
        } else {
            responseDiv.textContent = 'Warning: token query param required';
            unsubscribeContainer.style.display = 'none';
            subscribeContainer.style.display = 'block';
        }
    };
</script>
</body>
</html>
