<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    <!--[if mso]>
    <noscript>
    <xml>
        <o:OfficeDocumentSettings>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    </noscript>
    <![endif]-->
    <style>
        table, td, div, h1, p {font-family: Arial, sans-serif;}
    </style>
</head>
<body style="margin:0;padding:0;">
<table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
    <tr>
        <td align="center" style="padding:0;">
            <table role="presentation" style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
                <tr>
                    <td align="center" style="padding:40px 0 30px 0;background:#ffffff;">
                        <img src="{{ $message->embed( public_path() . '/images/logo.png' ) }}" alt="" width="300" style="height:auto;display:block;" />
                    </td>
                </tr>
                <tr>
                    <td style="padding:36px 30px 42px 30px;">
                        <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                            <tr>
                                <td style="padding:0 0 36px 0;color:#153643;">
                                    <h1 style="font-size:24px;margin:0 0 20px 0;font-family:Arial,sans-serif;">Moov-Africa E-Sim</h1>
                                    <p style="margin:0 0 12px 0;font-size:14px;line-height:22px;font-family:Arial,sans-serif;">Bonjour <small> <strong>{{ $user->name }}</strong> </small>.</p>
                                    <p style="margin:0 0 12px 0;font-size:14px;line-height:22px;font-family:Arial,sans-serif;">Prière de trouver, ci-dessous, vos coordonnées d'accès à l'application GT E-sim.</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0;">
                                    <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                                        <tr>
                                            <td colspan="2" style="width:260px;padding:0;vertical-align:top;color:#153643;">
                                                <p style="margin:0 0 12px 0;font-size:10px;line-height:20px;font-family:Arial,sans-serif;">
                                                    <dl>
                                                        <dt><small> <strong>Login:</strong> </small></dt>
                                                        <dd> <small>{{ $login }}</small></dd>

                                                        <dt><small> <strong>{{ $pwd ? 'Mot de Passe:' : '' }}</strong> </small></dt>
                                                        <dd><small>{{ $pwd }}</small></dd>
                                                    </dl>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>

                                            <td style="width:260px;padding:0;vertical-align:top;color:#153643;">
                                                <p style="margin:0 0 12px 0;font-size:10px;line-height:20px;font-family:Arial,sans-serif;"></p>
                                            </td>
                                            <td style="padding: 12px 18px 12px 18px; border-radius:5px; background-color: #1F7F4C;" align="center">
                                                <a rel="noopener" target="_blank" href="http://gtesim.moov-africa.ga/" target="_blank" style="font-size: 18px; font-family: Helvetica, Arial, sans-serif; font-weight: bold; color: #ffffff; text-decoration: none; display: inline-block;">Accédez à GT-E-sim &rarr;</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px;background:#FF5733;">
                        <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;font-size:9px;font-family:Arial,sans-serif;">
                            <tr>
                                <td style="padding:0;width:50%;" align="left">
                                    <p style="margin:0;font-size:8px;line-height:16px;font-family:Arial,sans-serif;color:#ffffff;">
                                        &reg; {{ config('app.name') }} {{ now()->year }} GT/DSI
                                    </p>
                                </td>
                                <td style="padding:0;width:50%;" align="right">
                                    <table role="presentation" style="border-collapse:collapse;border:0;border-spacing:0;">
                                        <tr>
                                            <td style="padding:0 0 0 10px;width:38px;">
                                                <a href="#" style="color:#ffffff;"><img src="{{ $message->embed( public_path() . '/images/facebook.png' ) }}" alt="Twitter" width="38" style="height:auto;display:block;border:0;" /></a>
                                            </td>
                                            <td style="padding:0 0 0 10px;width:38px;">
                                                <a href="#" style="color:#ffffff;"><img src="{{ $message->embed( public_path() . '/images/twiter.png' ) }}" alt="Facebook" width="38" style="height:auto;display:block;border:0;" /></a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
