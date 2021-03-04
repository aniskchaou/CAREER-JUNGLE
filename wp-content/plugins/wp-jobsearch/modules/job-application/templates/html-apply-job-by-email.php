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
                                            <h1 id="logo" style="color: #f1f1f1; margin: 0; padding: 28px 24px; display: block; font-family: Arial; font-size: 30px; font-weight: bold; text-align: center; line-height: 150%;">Job Apply Form</h1>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- End Header -->
                        </td>
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
                                                        <td valign="top">Hi ,

                                                            A user has applied your job ( {job_title} ) by email.
                                                            <table class="blueTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="text-align: center;" colspan="2">Apply Form</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Job URL</td>
                                                                        <td>{job_url}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>First Name</td>
                                                                        <td>{first_name}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Last Name</td>
                                                                        <td>{last_name}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Email</td>
                                                                        <td>{user_email}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Phone</td>
                                                                        <td>{user_phone}</td>
                                                                    </tr>
                                                                        <td>Candidate Job Title</td>
                                                                        <td>{candidate_job_title}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Current Salary</td>
                                                                        <td>{current_salary}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2">{custom_fields}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Employer Message</td>
                                                                        <td>{user_message}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>User accept email communication</td>
                                                                        <td>{accepts_email_communication}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
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