<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>

<body>
    <table  summary="Reset Password" style="width:100%">
        <caption>Reset Password</caption>
        <tr>
            <th scope="column">
                <table  style="text-align:center;width:600px;padding: 30px;margin-left: auto;margin-right: auto;background:url('https://i.ibb.co/D86Gc2v/Email-Template-Bkg-Image.jpg');background-repeat: no-repeat;background-size: auto;">
                     <caption>Details</caption>
                    <tr>
                        <th scope="column"><b>Dear {{ucfirst($email_data['name'])}},</b></th>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Welcome to Port Logistics Application.</td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Please click on the below link to reset your password.</td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><a href="{{ route('password.request').'/'.$email_data['token'] }}">Click here to reset your Port Logistics Account&#39;s password?</a>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td>If the link above is not clickable , copy paste the following URL into the address of your web browser.</td>
                    </tr>
                    <tr>
                        <td><a href="{{ route('password.request').'/'.$email_data['token'] }}">{{ route('password.request').'/'.$email_data['token'] }}</a></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Sincerely Yours,</td>
                    </tr>
                    <tr>
                        <td>Port Logistics Team</td>
                    </tr>
                </table>
            </th>
        </tr>
    </table>
</body>

</html>