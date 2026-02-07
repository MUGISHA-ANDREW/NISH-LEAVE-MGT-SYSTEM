# 🚀 Fix Production Server Error (500)

The "500 Server Error" is likely caused by **missing Mail environment variables** in Railway.
Local `.env` changes are **NOT** automatically sent to Railway when you `git push`. You must update them in the dashboard manually.

## 📋 Step 1: Update Railway Variables (Crucial)

1.  Go to your **Railway Dashboard**.
2.  Click on your project: **nish-leave-mgt-system-production**.
3.  Go to the **Variables** tab.
4.  Add (or Update) the following variables EXACTLY as shown:

| Variable Name | Value |
| :--- | :--- |
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | `smtp.gmail.com` |
| `MAIL_PORT` | `587` |
| `MAIL_USERNAME` | `andrewmugisha699@gmail.com` |
| `MAIL_PASSWORD` | `fgjocmethlogxhdl` |
| `MAIL_ENCRYPTION` | `tls` |
| `MAIL_FROM_ADDRESS` | `andrewmugisha699@gmail.com` |
| `MAIL_FROM_NAME` | `Nish Auto Limited` |
| `LOG_CHANNEL` | `stderr` |

> **Note:** Adding `LOG_CHANNEL=stderr` is important. It ensures errors show up in Railway Logs instead of crashing the server due to file permission issues.

## 📋 Step 2: Redeploy

After adding the variables, Railway usually triggers a redeploy automatically.
If it doesn't:
1.  Click the **Deployments** tab.
2.  Click **Redeploy** on the latest commit.

## 📋 Step 3: Verify

1.  Wait for the deployment to finish (green checkmark).
2.  Go to your app's "Forgot Password" page.
3.  Try submitting an email.
4.  It should now show a success message or a specific error (not a 500 page).

## 🆘 Still seeing 500 Error?

If it fails again, check the logs to see the REAL error:
1.  In Railway, click on the **Logs** tab of your service.
2.  Look for the latest error message (usually in red text).
3.  Paste that error message here so I can help!
