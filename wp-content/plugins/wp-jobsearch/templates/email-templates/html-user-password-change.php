<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body style="margin: 0; padding: 0;">
        <div style="background-color: #eeeeef; padding: 50px 0;">
            <table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                    <tr>
                        <td align="center" valign="top"><!-- Header -->
                            <table id="template_header" style="background-color: #454545; color: #f1f1f1; -webkit-border-top-left-radius: 6px !important; -webkit-border-top-right-radius: 6px !important; border-top-left-radius: 6px !important; border-top-right-radius: 6px !important; border-bottom: 0; font-family: Arial; font-weight: bold; line-height: 100%; vertical-align: middle;" border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h1 id="logo" style="color: #f1f1f1; margin: 0; padding: 28px 24px; display: block; font-family: Arial; font-size: 30px; font-weight: bold; text-align: center; line-height: 150%;">Password Change Successfully</h1>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- End Header --></td>
                    </tr>
                    <tr>
                        <td align="center" valign="top"><!-- Body -->
                            <table id="template_body" border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tbody>
                                    <tr>
                                        <td id="mailtpl_body_bg" style="background-color: #fafafa;" valign="top"><!-- Content -->
                                            <table border="0" width="100%" cellspacing="0" cellpadding="20">
                                                <tbody>
                                                    <tr>
                                                        <td valign="top">Hi {username},

                                                            {user_email}, This notice confirms that your password was changed Successfully on My {SITE_URL} If you did not change your password, please contact the Site Administrator at {ADMIN_EMAIL} This email has been sent to {user_email} please follow information below to get your new password.
                                                            <h5></h5>
                                                            <table class="blueTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="text-align: center;" colspan="2">user change password</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Username</td>
                                                                        <td>{username}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>User Email</td>
                                                                        <td>{user_email}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td> New Password</td>
                                                                        <td>{user_password}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            Best Regards,

                                                            {SITE_NAME}

                                                            {ADMIN_EMAIL}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <table id="template_footer" style="border-top: 1px solid #E2E2E2; background: #eee; -webkit-border-radius: 0px 0px 6px 6px; -o-border-radius: 0px 0px 6px 6px; -moz-border-radius: 0px 0px 6px 6px; border-radius: 0px 0px 6px 6px;" border="0" width="100%" cellspacing="0" cellpadding="10">
                                <tbody>
                                    <tr>
                                        <td valign="top">
                                            <table border="0" width="100%" cellspacing="0" cellpadding="10">
                                                <tbody>
                                                    <tr>
                                                        <td id="credit" style="border: 0; color: #777; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;" colspan="2" valign="middle">{COPYRIGHT_TEXT}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- End Footer --></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>