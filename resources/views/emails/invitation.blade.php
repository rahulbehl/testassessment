<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Email</title>
    <style>
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 16px;
        }

        table,
        td {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 16px;
        }

    </style>
</head>

<body>
    <table width="600" cellspacing="0" cellpadding="0">
        <tr style="background-color: #2c3543;">
            <td height="100px" style="text-align: center;" cellspacing="0" cellpadding="0">
                <img src="https://picsum.photos/seed/picsum/536/354">
            </td>
        </tr>
        <tr>
            <td style="background-color: #f5f5f5; padding: 25px;">
                <p style="font-weight: 600;">Hi Welcome to Assessment!</p>
                <p></p>
                <p>Register here to get best online benifits</p>
                <p></p>
                <p style="margin-bottom: 8%; clear: both; width: 100%;">
                    <a href="{{$params['signup_url']}}" style="border: 1px solid; padding: 15px 25px; background: red; color: white; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 20px; float: left; margin-left: 10%;"> Reject Invitation </a>
                </p>
                <p></p>
                <p></p>
                <p>Regards,</p>
                <p></p>
                <p>Assessment</p>
                <p></p>
                <hr>
            </td>
        </tr>
    </table>
</body>

</html>
