# Free Google Apps Script OTP mailer

This option sends verification messages through Google Apps Script `MailApp`, so the Laravel server does not need a Gmail SMTP App Password. Consumer Google accounts currently have a daily recipient quota; this is intended for a student project, not bulk mail.

## Deploy the Google sender

1. Sign into the Google account you want SAMI to send from and open [Google Apps Script](https://script.google.com/).
2. Create a project and replace the starter code with `scripts/sami-otp-mailer.gs` from this backend.
3. Save the project. Select `authorizeMailer` in the function list, click **Run**, and grant Google the permission to send email. Then choose **Deploy > New deployment > Web app**. Set **Execute as** to yourself and access to **Anyone** (anonymous access). Deploy and copy the URL ending in `/exec`.
4. In PowerShell, from the backend folder, run `scripts/configure-apps-script-mailer.ps1` and paste the deployment URL. The script stores the URL and a random shared token in the ignored backend `.env` file and prints the token once.
5. In the Apps Script editor, open **Project Settings > Script properties**. Add property `SAMI_OTP_TOKEN` with the printed token value. Return to PowerShell and press Enter so the helper verifies the connection.
6. Restart the Laravel API and request an OTP. The server sends only to `@tup.edu.ph` addresses. The OTP stays hashed in the database and expires after five minutes.

The shared token belongs only in Laravel `.env` and Apps Script Script properties. Never add it to the Flutter app. If you change the Apps Script code later, redeploy the web app.

